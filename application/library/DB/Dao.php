<?php
/**
 * Dao.php Created by
 * User: fenlon
 * Date: 2021/12/11 17:04
 * DAO基类
 */
namespace DB;

use Yaf\Registry;

class Dao {
    /**
     * 表状态值
     */
    const STATUS_NORMAL = 1;
    const STATUS_REMOVE = 9;
    protected $statusField;

    /**
     * 指定添加、更新和删除的表字段名称
     */
    protected $createdField;
    protected $updatedField;
    protected $deletedField;

    /**
     * @var 指定表名
     */
    protected $tableName;

    /**
     * @var 指定表的主键字段名称
     */
    protected $pk;

    /**
     * @var array 指定允许的表字段名称
     */
    protected $allowFields = array();

    // 数据库连接实例
    protected $db = null;

    // 数据库配置信息
    protected $dbConfig = array();

    public function __construct()
    {
        // 指定表名
        $this->dbConfig['table'] = $this->tableName;
        $this->db = Factory::getInstance($this->dbConfig)->getDB();
    }

    /**
     * 过滤数据
     * @param array $data
     * @return array
     */
    protected function filterData(array $data) {
        if (array_key_exists($this->pk, $data)) {
            // 更新数据
            $data[$this->updatedField] = time();
        } else {
            $data[$this->createdField] = time();
        }
        if (array_key_exists($this->statusField, $data)) {
            if (self::STATUS_REMOVE === $data[$this->statusField]) {
                // 删除
                $data[$this->deletedField] = time();
            }
        }
        $result = array();
        foreach ($data as $k => $v) {
            if (in_array($k, $this->allowFields)) {
                $result[$k] = $v;
            }
        }
        return $result;
    }

    public function save(array $data) {
        $data = $this->filterData($data);
        if (empty($data)) {
            return false;
        }
        if (array_key_exists($this->pk, $data)) {
            // 更新
        } else {
            // 新增
            return $this->insert($data);
        }
    }

    /**
     * 添加记录
     * @param array $data
     * @param bool $replace
     * @return bool
     */
    protected function insert(array $data, $replace = false) {
        $sql = $this->db->getInsertSql($data, $replace);
        if (!$sql) {
            return false;
        }
        $result = $this->db->execute($sql, $data);
        if ($result) {
            return $this->db->getLastInsertId();
        }
        return $result;
    }
}