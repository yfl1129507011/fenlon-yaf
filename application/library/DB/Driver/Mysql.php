<?php
/**
 * Mysql.php Created by
 * User: fenlon
 * Date: 2021/12/14 15:59
 * Des: mysql数据库驱动
 */
namespace DB\Driver;

use DB\Driver;

class Mysql extends Driver {

    // SQL 表达式
    protected $insertSql    = '%INSERT% INTO %TABLE% (%FIELD%) VALUES (%DATA%)';
    protected $updateSql    = 'UPDATE %TABLE% SET %SET% WHERE %WHERE%';

    /**
     * 解析DNS
     * @param $config
     * @return mixed|string
     */
    protected function parseDsn($config) {
        if (!empty($config['port'])) {
            $dsn = 'mysql:host=' . $config['hostname'] . ';port=' . $config['port'];
        } else {
            $dsn = 'mysql:host=' . $config['hostname'];
        }
        $dsn .= ';dbname=' . $config['database'];

        if (!empty($config['charset'])) {
            $dsn .= ';charset=' . $config['charset'];
        }

        return $dsn;
    }

    /**
     * 获取表名：库名.表名
     * @return string
     */
    protected function parseTable() {
        return '`' . $this->dbConfig['database'] . '`.`' . $this->dbConfig['table'] . '`';
    }

    /**
     * 解析绑定的数据
     * @param array $data
     * @return array
     */
    protected function parseData(array $data) {
        $result = array();
        foreach ($data as $k => $v) {
            if (is_array($v) && !empty($v)) {
                // uTimes => array('inc', 1)
                switch (strtolower($v[0])) {
                    case 'inc':
                        $result[$k] = $k . '+' . floatval($v[1]);
                        $this->setBind($k, floatval($v[1]));
                        break;
                    case 'dec':
                        $result[$k] = $k . '-' . floatval($v[1]);
                        $this->setBind($k, floatval($v[1]));
                        break;
                    default:
                        break;
                }
            } else {
                $result[$k] = ':' . $k;
                $this->setBind($k, $v);
            }
        }
        return $result;
    }

    /**
     * 解析where
     * @param array $condition
     * array(
     *  'name' => 'aaa',
     *  'age' => array('>', 9)
     *  'status' => array('in', array(1,2,3))
     * )
     * @return array
     */
    protected function parseWhere(array $condition) {
        $result = array();
        $operators = array('IN', 'LIKE', '<', '>', '<=', '>=', '!=');
        foreach ($condition as $k => $v) {
            if (is_array($v)) {
                $op = strtoupper($v[0]);
                if (in_array($op, $operators)) {
                    switch ($op) {
                        case 'IN':
                            $result[] = $k . ' ' . $op . '(' . implode(', ', array_fill(0, count($v[1]), '?')) . ')';
                            $this->setBind($v[1]);
                            break;
                        case 'LIKE':
                            $result[] = $k . ' ' . $op . ' ?';
                            $this->setBind($k, $v[1]);
                            break;
                        default:
                            $result[] = $k . ' ' . $op . ':' . $k;
                            $this->setBind($k, $v[1]);
                            break;
                    }
                }
            } else {
                $result[] = $k . '=:' . $k;
                $this->setBind($k, $v);
            }
        }
        return implode(' AND ', $result);
    }


    /**
     * 添加记录
     * @param array $data
     * @param bool $replace
     * @return bool
     * @throws \Exception
     */
    public function insert(array $data, $replace = false) {
        $data = $this->parseData($data);
        if (empty($data)) {
            return 0;
        }
        $fields = array_keys($data);
        $values = array_values($data);

        $sql = str_replace(
            array('%INSERT%', '%TABLE%', '%FIELD%', '%DATA%'),
            array(
                $replace ? 'REPLACE' : 'INSERT',
                $this->parseTable(),
                implode(' , ', $fields),
                implode(' , ', $values),
            ),
            $this->insertSql
        );
        if (!$sql) {
            return false;
        }
        $bind = $this->getBind();
        $result = $this->execute($sql, $bind);
        if ($result) {
            return $this->getLastInsertId();
        }
        return $result;
    }

    /**
     * 更新记录
     * @param array $data
     * @param array $condition
     * @return bool
     * @throws \Exception
     */
    public function update(array $data, array $condition) {
        $data = $this->parseData($data);
        if (empty($data)) {
            return false;
        }
        $set = array();
        foreach ($data as $k => $v) {
            $set[] = $k . '=' . $v;
        }

        $sql = str_replace(
            array('%TABLE%', '%SET%', '%WHERE%'),
            array(
                $this->parseTable(),
                implode(',', $set),
                $this->parseWhere($condition)
            ),
            $this->updateSql
        );
        if (!$sql) {
            return false;
        }
        $bind = $this->getBind();

        return $this->execute($sql, $bind);
    }
}