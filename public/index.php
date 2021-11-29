<?php
ini_set('display_errors', 1);
//error_reporting(E_ALL);

define('APP_PATH', realpath(dirname(dirname(__FILE__))));

$app = new Yaf\Application(APP_PATH . '/conf/application.ini');

try {
    $app->bootstrap()->run();
} catch (Exception $e) {
    echo $e->getMessage();
}
