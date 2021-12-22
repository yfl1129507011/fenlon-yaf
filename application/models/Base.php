<?php
/**
 * Base.php Created by
 * User: fenlon
 * Date: 2021/12/17 12:53
 * model基类
 */

class BaseModel {
    protected $dao;
    protected $entity;

    protected $entityReflection;

    public function __construct()
    {
        $className = get_called_class();
        $className = stristr($className, 'model', true);
        $daoClass = '\\src\\DAO\\' . ucwords($className);
        $entityClass = '\\src\\Entity\\' . ucwords($className);
        $this->dao = new $daoClass();
        $this->entity = new $entityClass();
    }

    public function __set($name, $value)
    {
        if (empty($this->entityReflection)) {
            $this->entityReflection = new ReflectionObject($this->entity);
        }
        if ($this->entityReflection->hasProperty($name)) {
            $this->entity->{$name} = $value;
        }
    }

    /**
     * 保存数据，如果传入主键则更新数据
     * @param array $data
     * @return mixed
     */
    public function save(array $data = array()) {
        if (empty($data)) {
            $data = $this->entity->toArray();
        }
        return $this->dao->save($data);
    }
}