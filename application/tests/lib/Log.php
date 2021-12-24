<?php
/**
 * Log.php Created by
 * User: fenlon
 * Date: 2021/12/24 17:36
 * 测试用例
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('ROOT_PATH', realpath(dirname(dirname(dirname(dirname(__FILE__))))));
define('APP_PATH', ROOT_PATH . '/application');

$app = new Yaf\Application(ROOT_PATH . '/conf/application.ini');

try {
    $app->bootstrap();
} catch (Exception $e) {
    Log::error($e);
}

echo Log::error('系统错误');
echo Log::debug('测试');
echo Log::warning('变量不存在');