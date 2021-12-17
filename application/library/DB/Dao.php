<?php
/**
 * Dao.php Created by
 * User: fenlon
 * Date: 2021/12/11 17:04
 */
namespace DB;

use Yaf\Registry;

class Dao {
    // 数据库连接实例
    protected $db = null;

    // 数据库配置信息
    protected $dbConfig = array();

    public function __construct()
    {
        $this->db = Factory::getInstance($this->dbConfig)->getDB();
    }
}