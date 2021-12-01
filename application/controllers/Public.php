<?php
class PublicController extends BaseController {
    public $rememberCookieFlag = "Form-Data";
    public function init()
    {

    }

    # 退出
    public function logoutAction() {
        $this->display('login');
    }

    # 登录
    public function loginAction() {
        if ($this->getRequest()->isPost()) {
            $returnData = array();
            $returnData['code'] = 200;
            $returnData['return_url'] = '/';
            $postData = $this->getRequest()->getPost();
            $this->ajaxReturn($postData);
            if (! Image\Captcha::getInstance()->checkCode($postData['captcha'])) {
                $returnData['code'] = 401;
                $returnData['msg'] = '验证码错误';
                $this->ajaxReturn($returnData);
            }

            if (isset($postData['remember']) && $postData['remember'] == 'on') {
                Http\Cookies::getInstance()->forever($this->rememberCookieFlag, $postData);
            }
        }

        // 【记住我】的数据展示逻辑
        $reEmail = "";
        $rePassword = "";
        if (Http\Cookies::getInstance()->has($this->rememberCookieFlag)) {
            $postData = Http\Cookies::getInstance()->get($this->rememberCookieFlag);
            $reEmail = $postData['email'];
            $rePassword = $postData['password'];
        }
        $this->getView()->assign('_email', $reEmail);
        $this->getView()->assign('_password', $rePassword);
    }

    # 注册
    public function registerAction() {
        //Http\Cookies::getInstance()->set('aaa', 123, 60);
        echo Http\Cookies::getInstance()->get('aaa');
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
