<?php
class PublicController extends BaseController {
    public function init()
    {

    }

    # 退出
    public function logoutAction() {
        $this->display('login');
    }

    # 登录
    public function loginAction() {

    }

    # 注册
    public function registerAction() {

    }

    # 忘记密码
    public function forgotAction() {

    }

    # 修改密码
    public function recoverAction() {

    }

    # 生成验证码
    public function captchaAction() {
        Yaf\Dispatcher::getInstance()->disableView();
        Image\Captcha::getInstance()->outputImg();
    }
}
