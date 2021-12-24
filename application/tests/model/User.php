<?php
/**
 * User.php Created by
 * User: fenlon
 * Date: 2021/12/24 17:37
 */

error_reporting(E_ALL ^ E_NOTICE);

define('ROOT_PATH', realpath(dirname(dirname(dirname(dirname(__FILE__))))));
define('APP_PATH', ROOT_PATH . '/application');

$app = new Yaf\Application(ROOT_PATH . '/conf/application.ini');

try {
    $app->bootstrap();
} catch (Exception $e) {
    Log::error($e);
}

$user = new UserModel();
print_r($user->get(1));