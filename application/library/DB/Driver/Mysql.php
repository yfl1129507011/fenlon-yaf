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
}