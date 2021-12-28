<?php
/**
 * Base.php Created by
 * User: fenlon
 * Date: 2021/11/25 17:34
 */
class BaseController extends Yaf\Controller_Abstract {
    public function init()
    {
        if(! \Http\Cookies::getInstance()->has(UserModel::LOGIN_FLAG) ) {
            $this->redirect('logout.html');
        }
    }

    protected function logout() {
        if(\Http\Cookies::getInstance()->has(UserModel::LOGIN_FLAG) ) {
            Http\Cookies::getInstance()->delete(UserModel::LOGIN_FLAG);
        }
    }

    protected function display($tpl, array $parameters = null)
    {
        Yaf\Dispatcher::getInstance()->disableView();  // 取消视图自动加载
        parent::display($tpl, $parameters); // TODO: Change the autogenerated stub
    }

    protected function ajaxReturn(array $data, $type = 'json', $json_option = 320) {
        switch (strtoupper($type)) {
            case 'JSON':
            default:
                header("Content-Type:application/json; charset=utf-8");
                exit(json_encode($data, $json_option));
        }
    }
}