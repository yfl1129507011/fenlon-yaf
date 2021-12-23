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
    protected $db;

    // 数据库配置信息
    protected $dbConfig = array();

    public function __construct()
    {
        // 指定表名
        $this->dbConfig['table'] = $this->tableName;
        $this->db = Factory::getInstance($this->dbConfig)->getDB();
    }

    /**
     * 过滤不允许的字段名称
     * @param array $data
     * @return array
     */
    protected function filterField(array $data) {
        $result = array();
        foreach ($data as $k => $v) {
            if (in_array($k, $this->allowFields)) {
                $result[$k] = $v;
            }
        }
        return $result;
    }

    protected function filterCondition($condition) {
        $result = array();
        if (is_scalar($condition)) {
            $result[$this->pk] = $condition;
        } elseif (is_array($condition)) {
            $result = $this->filterField($condition);
        }
        return $result;
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

        return $this->filterField($data);
    }

    /**
     * 添加或更新数据
     * @param array $data
     * @param array $condition
     * @return bool|void
     */
    public function save(array $data, array $condition = array()) {
        $data = $this->filterData($data);
        if (empty($data)) {
            return false;
        }
        if (array_key_exists($this->pk, $data) || !empty($condition)) {
            // 更新
            if(empty($condition)) {
                $condition[$this->pk] = $data[$this->pk];
                unset($data[$this->pk]);
            } else {
                $condition = $this->filterField($condition);
            }
            return $this->db->update($data, $condition);
        } else {
            // 新增
            return $this->db->insert($data);
        }
    }

    /**
     * 获取单条数据
     * @param $condition
     * @return array
     */
    public function get($condition) {
        $condition = $this->filterCondition($condition);
        if (empty($condition)) {
            return $condition;
        }

        $option = array(
            'limit' => 1
        );
        $res = $this->db->select($condition, $option);
        if (!empty($res)) {
            return $res[0];
        }
        return $res;
    }
}