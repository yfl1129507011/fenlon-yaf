<?php
/**
 * Captcha.php Created by
 * User: fenlon
 * Date: 2021/11/26 15:34
 * Description: 验证码生成类库
 */
namespace Image;

class Captcha {
    private static $instance = null;
    // 参数配置信息
    private $captchaConfig = array(
        // 验证码中使用的字符，01IO容易混淆，建议不使用
        'charset'   => '23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVW',
        'code'      => '',          // 验证码
        'codeLen'   => 4,           // 验证码长度
        'imgW'      => 124,          // 验证码图片宽度
        'imgH'      => 44,          // 验证码图片高度
        'font'      => 'font.ttf',  // 字体格式文件
        'fontsize'  => 20,          // 字体大小
        'expire'    => 1800,         // 验证码过期时间（s）
        'flag'      => __CLASS__,   // 验证码存放session中的标识号
        'imgHandle' => null,        // 验证码图片实例
    );

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @param $name
     * @return mixed
     * 魔术方法：读取不可访问或不存在属性时被调用
     * eg: $this->code
     */
    public function __get($name)
    {
        if (isset($this->captchaConfig[$name])) {
            return $this->captchaConfig[$name];
        }
        return null;
    }

    /**
     * @param $name
     * @param $value
     * 魔术方法：当给不可访问或不存在属性赋值时被调用
     * eg: $this->code = ''
     */
    public function __set($name, $value)
    {
        $this->captchaConfig[$name] = $value;
    }

    private function __construct($config = array())
    {
        if (is_array($config) && !empty($config)) {
            $this->captchaConfig = array_merge($this->captchaConfig, $config);
        }
    }

    public static function getInstance($config = array()) {
        if (! (self::$instance instanceof self)) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    /**
     * 制作验证码图片
     */
    private function createImg() {
        // 新建一个彩色图像
        $this->imgHandle = imagecreatetruecolor($this->imgW, $this->imgH);
        // 为画板分配颜色
        $color = imagecolorallocate($this->imgHandle, mt_rand(157,255), mt_rand(157,255), mt_rand(157,255));
        // 画一个矩形画板
        imagefilledrectangle($this->imgHandle, 0, $this->imgH, $this->imgW, 0, $color);

        // 填充文字
        $charsetLen = strlen($this->charset);
        $_x = $this->imgW / $this->codeLen;
        $_code = array();
        for ($i = 0; $i < $this->codeLen; $i++) {
            $_code[$i] = $this->charset[mt_rand(0, $charsetLen-1)];
            $color = imagecolorallocate($this->imgHandle, mt_rand(0,156), mt_rand(0,156), mt_rand(0,156));
            imagettftext(
                $this->imgHandle,
                $this->fontsize,
                mt_rand(-30, 30),
                $_x * $i + mt_rand(1,5),
                $this->imgH / 1.4,
                $color,
                $this->font,
                $_code[$i]
            );
        }
        $this->code = implode('', $_code);

        // 填充线条
        for ($i = 0; $i < 6; $i++) {
            $color = imagecolorallocate($this->imgHandle, mt_rand(0,156), mt_rand(0,156), mt_rand(0,156));
            imageline(
                $this->imgHandle,
                mt_rand(0, $this->imgW),
                mt_rand(0, $this->imgH),
                mt_rand(0, $this->imgW),
                mt_rand(0, $this->imgH),
                $color
            );
        }

        // 生成雪花
        for ($i = 0; $i < 30; $i++) {
            $color = imagecolorallocate($this->imgHandle, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
            imagestring(
                $this->imgHandle,
                mt_rand(1, 5),
                mt_rand(0, $this->imgW),
                mt_rand(0, $this->imgH),
                '*',
                $color
            );
        }
    }

    /**
     * 存储验证码到cookie
     */
    private function saveCode() {
        // todo setcookie()
        setcookie(md5($this->flag), md5(strtolower($this->code)), $_SERVER['REQUEST_TIME']+$this->expire);
    }

    /**
     * 输出验证码图片
     */
    public function outputImg() {
        $this->createImg();
        $this->saveCode();

        header('Content-type:img/png');
        // 以 PNG 格式将图像输出到浏览器或文件
        imagepng($this->imgHandle);
        // 销毁图像
        imagedestroy($this->imgHandle);
    }

    /**
     * @param $code
     * @return bool
     * 验证码检查
     */
    public function checkCode($code) {
        $data = $_COOKIE[md5($this->flag)];
        return md5(strtolower($code)) == $data;
    }
}