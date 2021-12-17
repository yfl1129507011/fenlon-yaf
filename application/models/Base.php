<?php
/**
 * Base.php Created by
 * User: fenlon
 * Date: 2021/12/17 12:53
 * model基类
 */

class BaseModel {
    public $dao;

    public function __construct()
    {
        $className = get_called_class();
        $daoClass = '\\src\\DAO\\' . ucwords($className);
        $entityClass = '\\src\\Entity\\' . ucwords($className);
        $this->dao = $daoClass;
//        $this->dao = new $daoClass();
    }
}