<?php
/**
 * Factory.php Created by
 * User: fenlon
 * Date: 2021/12/14 11:06
 * Des: 数据库工厂类
 */
namespace DB;

// 最终类，禁止继承
use Yaf\Registry;

final class Factory {
    // 数据库配置列表
    protected $dbConfig = array();

    // 当前数据库工厂类静态实例
    private static $instance = null;

    private function __construct()
    {
        $dbConfig = Registry::get('dbConfig');
        if (!empty($dbConfig)) {
            $this->dbConfig = $dbConfig->dbConfig->get('db')->toArray();
        }

    }

    public static function getInstance($dbConfig = array()) {
        if (! (self::$instance instanceof self) ) {
            self::$instance = new self();
        }
        if (is_array($dbConfig)) {
            self::$instance->dbConfig = array_merge(self::$instance->dbConfig, $dbConfig);
        }

        return self::$instance;
    }

    public function getDB() {
        if (empty($this->dbConfig['type'])) {
            throw new \InvalidArgumentException('undefined db type');
        }
        $class = '\\DB\\Driver\\' . ucwords($this->dbConfig['type']);
        return new $class($this->dbConfig);
    }
}
