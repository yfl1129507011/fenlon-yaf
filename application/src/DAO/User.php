<?php
/**
 * User.php Created by
 * User: fenlon
 * Date: 2021/12/17 12:50
 */
namespace src\DAO;

use DB\Dao;

class User extends Dao {
    protected $createdField = 'uCreated';
    protected $updatedField = 'uUpdated';
    protected $deletedField = 'uDeleted';

    protected $tableName = 'fenlon_user';

    protected $pk = 'uid';

    protected $allowFields = array(
        'uid',
        'uName',
        'uPassword',
        'uEmail',
        'uCreated',
        'uUpdated',
        'uDeleted',
        'uStatus',
    );
}