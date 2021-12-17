<?php
ini_set('display_errors', 1);
//error_reporting(E_ALL);

define('ROOT_PATH', realpath(dirname(dirname(__FILE__))));
define('APP_PATH', ROOT_PATH . '/application');

$app = new Yaf\Application(ROOT_PATH . '/conf/application.ini');

try {
    $app->bootstrap()->run();
} catch (Exception $e) {
    echo $e->getMessage();
}
