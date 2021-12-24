<?php
/**
 * Log.php Created by
 * User: fenlon
 * Date: 2021/12/23 11:04
 */

use Yaf\Registry;
class Log {
    const ERROR = 'ERROR';
    const DEBUG = 'DEBUG';
    const WARNING = 'WARNING';
    const TRACE = 'TRACE';

    static $logPath = APP_PATH . '/log';
    static $fileExt = '.log';

    /**
     * 写入日志
     * @param $level
     * @param $msg
     * @return bool|string
     */
    protected static function write($level, $msg) {
        $logConfig = Registry::get('config')->log;
        if (!empty($logConfig)) {
            if ($logConfig->get('filePath')) {
                self::$logPath = $logConfig->get('filePath');
            }
            if ($logConfig->get('fileExt')) {
                self::$fileExt = $logConfig->get('fileExt');
            }
        }
        file_exists(self::$logPath) OR mkdir(self::$logPath, 0755, true);

        $level = strtoupper($level);
        $filepath = self::$logPath . '/log-' . date('Y-m-d') . self::$fileExt;
        $message = '';
        if (!file_exists($filepath)) {
            $newFile = true;
        }
        if (! $fp = @fopen($filepath, 'ab')) {
            return false;
        }
        flock($fp, LOCK_EX);
        if (php_sapi_name() != 'cli') {
            $backtrace = debug_backtrace();
            $traceInfo = $backtrace[2];
            $msg = sprintf("[%s::%s] [%s] %s", $traceInfo['class'], $traceInfo['function'], $traceInfo['object']->getRequest()->getRequestUri(), $msg);
        }
        $message .= $level . ' - ' . date('Y-m-d H:i:s') . ' --> ' . $msg . PHP_EOL;
        fwrite($fp, $message);
        flock($fp, LOCK_UN);
        fclose($fp);

        if (isset($newFile) && $newFile === true) {
            chmod($filepath, 0644);
        }

        return $filepath;
    }

    public static function error($msg) {
        return self::write(self::ERROR, $msg);
    }

    public static function debug($msg) {
        return self::write(self::DEBUG, $msg);
    }

    public static function warning($msg) {
        return self::write(self::WARNING, $msg);
    }

    public static function trace($msg) {
        return self::write(self::TRACE, $msg);
    }
}