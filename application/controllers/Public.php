<?php
class PublicController extends BaseController {

    public function init()
    {

    }

    # 退出
    public function logoutAction() {
        $this->redirect('login.html');
    }

    # 登录
    public function loginAction() {
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            $user = new UserModel();
            $res = $user->login($postData);
            $this->ajaxReturn($res);
        }

        // 【记住我】的数据展示逻辑
        $reEmail = "";
        $rePassword = "";
        $remember = "";
        if (Http\Cookies::getInstance()->has(UserModel::REMEMBER_COOKIE_FLAG)) {
            $postData = Http\Cookies::getInstance()->get(UserModel::REMEMBER_COOKIE_FLAG);
            $reEmail = $postData['email'];
            $rePassword = $postData['password'];
            $remember = "on";
        }
        $this->getView()->assign('_email', $reEmail);
        $this->getView()->assign('_password', $rePassword);
        $this->getView()->assign('_remember', $remember);
    }

    # 注册
    public function registerAction() {
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            $user = new UserModel();
            $res = $user->register($postData);
            $this->ajaxReturn($res);
        }
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
