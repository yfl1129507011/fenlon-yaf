<?php
class ErrorController extends Yaf\Controller_Abstract {
    public function errorAction($exception) {
        echo $exception;
        return false;
    }
}