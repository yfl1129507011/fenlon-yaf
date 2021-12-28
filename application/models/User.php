<?php
/**
 * User.php Created by
 * User: fenlon
 * Date: 2021/12/15 17:30
 */

class UserModel extends BaseModel {
    const REMEMBER_COOKIE_FLAG = 'Form-Data';
    const LOGIN_FLAG = 'Login-Data';

    public function resetPassword($password, $rePassword, $email) {
        $returnData = array();
        $returnData['code'] = 200;
        $returnData['return_url'] = '/login.html';
        if (!empty($password) && !empty($rePassword) && !empty($email)) {
            if ($password != $rePassword) {
                $returnData['code'] = 400;
                $returnData['msg'] = '密码不一致';
            } else {
                $this->save(array('uPassword'=>md5($password)), array('uEmail'=>$email));
            }
        } else {
            $returnData['code'] = 401;
            $returnData['msg'] = '参数错误';
        }

        return $returnData;
    }

    public function checkEmailCode($email, $code) {
        $returnData = array();
        $returnData['code'] = 200;
        $returnData['return_url'] = '/recover-password.html?email=' . $email;
        if (!empty($email) && !empty($code)) {
            $res = $this->get(array('uEmail' => $email));
            if (!$res) {
                $returnData['code'] = 400;
                $returnData['msg'] = '账号错误';
            }
            if (!Email::checkCode($code)) {
                $returnData['code'] = 401;
                $returnData['msg'] = '验证码错误';
            }
        } else {
            $returnData['code'] = 402;
            $returnData['msg'] = '参数错误';
        }

        return $returnData;
    }

    public function sendEmailCode($email) {
        $returnData = array();
        $returnData['code'] = 200;
        $returnData['return_url'] = '/check-code.html?email=' . $email;
        if (!empty($email)) {
            $res = $this->get(array('uEmail' => $email));
            if (!$res) {
                $returnData['code'] = 400;
                $returnData['msg'] = '账号不存在';
            }
            Email::sendCode($email);
            $returnData['msg'] = '验证码已发送';
        } else {
            $returnData['code'] = 402;
            $returnData['msg'] = '参数错误';
        }

        return $returnData;
    }

    public function login(array $data) {
        $returnData = array();
        $returnData['code'] = 200;
        $returnData['return_url'] = '/';
        if (!empty($data['email']) && !empty($data['password']) && !empty($data['captcha'])) {
            if (! \Image\Captcha::getInstance()->checkCode($data['captcha'])) {
                $returnData['code'] = 400;
                $returnData['msg'] = '验证码错误';
            }
            $res = $this->get(array('uEmail' => $data['email']));
            if (!empty($res) && $res['uPassword'] == md5($data['password'])) {
                // 登录成功
                if (isset($data['remember']) && $data['remember'] == 'on') {
                    Http\Cookies::getInstance()->forever(self::REMEMBER_COOKIE_FLAG, $data);
                } else {
                    Http\Cookies::getInstance()->delete(self::REMEMBER_COOKIE_FLAG);
                }
                Http\Cookies::getInstance()->set(self::LOGIN_FLAG, $data, 24*60*60);
            } else {
                $returnData['code'] = 401;
                $returnData['msg'] = '账户或密码错误';
            }
        } else {
            $returnData['code'] = 402;
            $returnData['msg'] = '参数错误';
        }

        return $returnData;
    }

    public function register(array $data) {
        $returnData = array();
        $returnData['code'] = 200;
        $returnData['return_url'] = '/login.html';
        if (!empty($data['username']) && !empty($data['email']) && !empty($data['password']) && !empty($data['rePassword'])) {
            if ($data['password'] != $data['rePassword']) {
                $returnData['code'] = 400;
                $returnData['msg'] = '密码不一致';
            } else {
                $res = $this->get(array('uEmail' => $data['email']));
                if ($res) {
                    $returnData['code'] = 401;
                    $returnData['msg'] = '邮箱已存在';
                } else {
                    $this->uName = $data['username'];
                    $this->uEmail = $data['email'];
                    $this->uPassword = md5($data['password']);
                    $res = $this->save();
                    if ($res) {
                        $returnData['msg'] = '注册成功';
                    } else {
                        $returnData['code'] = 402;
                        $returnData['msg'] = '注册失败';
                    }
                }
            }
        } else {
            $returnData['code'] = 403;
            $returnData['msg'] = '参数错误';
        }

        return $returnData;
    }
}