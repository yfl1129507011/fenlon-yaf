<?php
/**
 * User.php Created by
 * User: fenlon
 * Date: 2021/12/15 17:31
 */
namespace src\Entity;

use DB\Entity;

class User extends Entity {
    /**
     * @var `uid` INT UNSIGNED
     */
    public $uid;

    /**
     * @var 用户名
     * VARCHAR(20)
     */
    public $uName;

    /**
     * @var 密码
     * VARCHAR(32)
     */
    public $uPassword;

    /**
     * @var 邮箱
     * VARCHAR(64)
     */
    public $uEmail;

    /**
     * @var 添加时间
     * INT
     */
    public $uCreated;

    /**
     * @var 更新时间
     * INT
     */
    public $uUpdated;

    /**
     * @var 删除时间
     * INT
     */
    public $uDeleted;

    /**
     * @var 用户状态 ：1-正常，9-删除
     * TINYINT(3)
     */
    public $uStatus;
}