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

    protected function parseData(array $data) {
        $result = array();
        foreach ($data as $k => $v) {
            $result[$k] = ':' . $k;
        }
        return $result;
    }

    /**
     * 生成insert sql语句
     * @param array $data
     * @param bool $replace
     * @return int|string|string[]
     */
    public function getInsertSql(array $data, $replace = false) {
        $data = $this->parseData($data);
        if (empty($data)) {
            return 0;
        }
        $fields = array_keys($data);
        $values = array_values($data);

        return str_replace(
            array('%INSERT%', '%TABLE%', '%FIELD%', '%DATA%'),
            array(
                $replace ? 'REPLACE' : 'INSERT',
                $this->parseTable(),
                implode(' , ', $fields),
                implode(' , ', $values),
            ),
            $this->insertSql
        );
    }
}