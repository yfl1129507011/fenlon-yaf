<?php
/**
 * Cookies.php Created by
 * User: fenlon
 * Date: 2021/11/30 16:23
 */
namespace Http;

class Cookies {
    private static $instance = null;
    private $config = array(
        'prefix'    => 'FenLon',  // cookie名称前缀
        'expire'    => 24*60*60, // cookie保存时间（s）, 默认1天
        'path'      => '/', // cookie保存路径
        'domain'    => '', // cookie有效域名
        'secure'    => false, // cookie启用安全传输
        'httponly'  => false, // httponly 设置
        'arrayFlag' => 'array[]:', // cookie数据数组标识
    );

    private function __construct(array $config = array())
    {
        if (!empty($config)) {
            $this->config = array_merge($this->config, $config);
        }
        if (!empty($this->config['httponly'])) {
            ini_set('session.cookie_httponly', 1);
        }
    }

    public static function getInstance(array $config = array()) {
        if ( !(self::$instance instanceof self) ) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    /**
     * @param $name
     * @param $value
     * @param int $expire
     * 存储cookie
     */
    public function set($name, $value, $expire = 0) {
        if (empty($expire)) {
            $expire = $this->config['expire'];
        }
        $expire = $_SERVER['REQUEST_TIME'] + intval($expire);
        $name = $this->config['prefix'] . $name;
        if (is_array($value)) {
            $value = $this->config['arrayFlag'] . json_encode($value);
        }
        setcookie(
            $name, $value, $expire,
            $this->config['path'], $this->config['domain'],
            $this->config['secure'], $this->config['httponly']
        );
    }

    /**
     * @param $name
     * @param $value
     * 永久保存cookie
     */
    public function forever($name, $value) {
        $expire = 315360000;
        $this->set($name, $value, $expire);
    }

    /**
     * @param $name
     * @return bool
     * 判断是否有Cookie数据
     */
    public function has($name) {
        $name = $this->config['prefix'] . $name;
        return isset($_COOKIE[$name]);
    }

    /**
     * @param string $name
     * @return mixed|null
     * 获取cookie值
     */
    public function get($name = '') {
        $value = null;
        $key = $this->config['prefix'] . $name;
        if ('' == $name) {
            // 获取全部
            $value = $_COOKIE;
        } elseif (isset($_COOKIE[$key])) {
            $value = $_COOKIE[$key];
            if (0 === strpos($value, $this->config['arrayFlag'])) {
                // 有数组标识
                $value = json_decode(substr($value, strlen($this->config['arrayFlag'])), true);
            }
        }

        return $value;
    }

    /**
     * @param $name
     * 删除cookie数据
     */
    public function delete($name) {
        if (!empty($name)) {
            $name = $this->config['prefix'] . $name;
            $expire = $_SERVER['REQUEST_TIME'] - 3600;
            setcookie(
                $name, '', $expire, $this->config['path'], $this->config['domain'],
                $this->config['secure'], $this->config['httponly']
            );
        }
    }

    /**
     * 清除指定前缀的所有cookie
     */
    public function clear() {
        $prefix = $this->config['prefix'];
        if ($prefix) {
            foreach ($_COOKIE as $key => $value) {
                if (0 === strpos($key, $prefix)) {
                    setcookie(
                        $key, '', $_SERVER['REQUEST_TIME'] - 3600, $this->config['path'],
                        $this->config['domain'], $this->config['secure'], $this->config['httponly']
                    );
                }
            }
        }
    }

}