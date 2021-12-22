<?php
/**
 * Entity.php Created by
 * User: fenlon
 * Date: 2021/12/17 18:03
 * 实体基类
 */
namespace DB;

class Entity {

    public function toArray() {
        $vars = get_object_vars($this);
        $data = array();
        foreach ($vars as $k => $v) {
            if (!empty($v)) {
                $data[$k] = $v;
            }
        }

        return $data;
    }
}