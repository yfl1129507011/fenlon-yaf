<?php
class PublicController extends BaseController {
    const REMEMBER_COOKIE_FLAG = 'Form-Data';
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
            $returnData = array();
            $returnData['code'] = 200;
            $returnData['return_url'] = '/';
            $postData = $this->getRequest()->getPost();
            if (! Image\Captcha::getInstance()->checkCode($postData['captcha'])) {
                $returnData['code'] = 401;
                $returnData['msg'] = '验证码错误';
                $this->ajaxReturn($returnData);
            }

            if (isset($postData['remember']) && $postData['remember'] == 'on') {
                Http\Cookies::getInstance()->forever(self::REMEMBER_COOKIE_FLAG, $postData);
            } else {
                Http\Cookies::getInstance()->delete(self::REMEMBER_COOKIE_FLAG);
            }
            $this->ajaxReturn($returnData);
        }

        // 【记住我】的数据展示逻辑
        $reEmail = "";
        $rePassword = "";
        $remember = "";
        if (Http\Cookies::getInstance()->has(self::REMEMBER_COOKIE_FLAG)) {
            $postData = Http\Cookies::getInstance()->get(self::REMEMBER_COOKIE_FLAG);
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
        echo Log::error('122');
        return false;
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
