<?php
class PublicController extends BaseController {

    public function init()
    {

    }

    # 退出
    public function logoutAction() {
        $this->logout();
        $this->redirect('login.html');
    }

    # 登录
    public function loginAction() {
        if (\Http\Cookies::getInstance()->has(UserModel::LOGIN_FLAG)) {
            // 已登录
            $this->redirect('/');
        }
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
        $this->logout();
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            $user = new UserModel();
            $res = $user->register($postData);
            $this->ajaxReturn($res);
        }
    }

    # 忘记密码
    public function forgotAction() {
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            $user = new UserModel();
            $res = $user->sendEmailCode($postData['email']);
            $this->ajaxReturn($res);
        }
    }

    # 验证邮箱验证码
    public function checkAction() {
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            $user = new UserModel();
            $res = $user->checkEmailCode($postData['email'], $postData['code']);
            $this->ajaxReturn($res);
        }
        $email = $this->getRequest()->get('email');
        $this->getView()->assign('_email', $email);
    }

    # 修改密码
    public function recoverAction() {
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            $user = new UserModel();
            $res = $user->resetPassword($postData['password'], $postData['rePassword'], $postData['email']);
            $this->ajaxReturn($res);
        }
        $email = $this->getRequest()->get('email');
        $this->getView()->assign('_email', $email);
    }

    # 生成验证码
    public function captchaAction() {
        Yaf\Dispatcher::getInstance()->disableView();
        Image\Captcha::getInstance()->outputImg();
    }
}
