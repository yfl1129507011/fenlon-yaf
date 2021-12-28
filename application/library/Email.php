<?php
/**
 * Email.php Created by
 * User: fenlon
 * Date: 2021/12/28 15:15
 */

use Http\Cookies;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Email {
    public static function sendCode($email, $length = 6) {
        $charset = '23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVW';
        $charsetLen = strlen($charset);
        $code = array();
        for ($i = 1; $i < $length; $i++) {
            $code[$i] = $charset[mt_rand(0, $charsetLen-1)];
        }
        $code = implode('', $code);

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            //$mail->Host = "smtp.163.com"; //SMTP服务器 163邮箱例子
            $mail->Host = "smtp.126.com"; //SMTP服务器 126邮箱例子
            //$mail->Host = "smtp.qq.com"; //SMTP服务器 qq邮箱例子
            $mail->SMTPAuth = true;
            $mail->Username = 'yangfeilong925@126.com';
            $mail->Password = 'GHUZDBPCFUJYFHTC';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = PHPMailer::ENCODING_BASE64;

            $mail->setFrom('yangfeilong925@126.com', 'FenLon');
            $mail->addAddress($email, $email);

            $mail->isHTML(true);
            $mail->Subject = '重置密码';
            $datetime = date('Y年m月d日');
            $mail->Body = <<<EMAIL
<b>亲爱的用户：</b><br>
您好！您的账号（{$email}）正在进行密码重置，本次请求的验证码为：<b style="color: orange">{$code}</b>
(为了保障您账号的安全性，请在1小时内完成验证)<br><br>

{$datetime}
EMAIL;
            if( $mail->send() ) {
                Cookies::getInstance()->set('email-code', strtolower($code), 60*60);
            }

        } catch (Exception $e) {
            Log::error("code message could not be sent. Mailer Error:{$mail->ErrorInfo}");
        }
    }

    public static function checkCode($code) {
        return Cookies::getInstance()->get('email-code') === strtolower($code);
    }
}