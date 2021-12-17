<?php
/**
 * Driver.php Created by
 * User: fenlon
 * Date: 2021/12/14 16:32
 * Des: 数据库驱动虚拟类
 */
namespace DB;

use PDO;

abstract class Driver {
    // PDO 连接参数
    protected $params = array(
        PDO::ATTR_CASE              => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES  => false,
    );
    // 数据库连接id，支持多个连接
    protected $links = array();

    // 当前连接ID
    protected $linkID;
    protected $linkRead;
    protected $linkWrite;

    // 事务指令数
    protected $transTimes = 0;

    // 数据库配置参数
    protected $dbConfig = array();

    public function __construct(array $dbConfig)
    {
        $this->dbConfig = $dbConfig;
    }

    /**
     * @param $config
     * @return mixed
     * 解析pdo连接的dsn信息
     */
    abstract protected function parseDsn($config);

    public function execute($sql) {

    }

    /**
     * @param array $config 数据库配置信息
     * @param int $linkNum 连接id序号
     * @param array|bool $autoConnection 是否自动连接主数据库（用于分布式），
     * 自动连接主数据库时，改参数传入主数据库的配置信息
     * @return mixed
     * 连接数据库方法
     */
    public function connect(array $config, $linkNum = 0, $autoConnection = false) {
        if (!isset($this->links[$linkNum])) {
            if (is_array($config['params']) && !empty($config['params'])) {
                $params = $config['params'] + $this->params;
            } else {
                $params = $this->params;
            }
            try {
                if (empty($config['dsn'])) {
                    $config['dsn'] = $this->parseDsn($config);
                }
                $this->links[$linkNum] = new PDO($config['dsn'], $config['username'], $config['password'], $params);
            } catch (\PDOException $e) {
                throw $e;
            }
        }

        return $this->links[$linkNum];
    }

    /**
     * @param bool $master 是否主服务器写入
     * @return mixed
     * 连接分布式服务器
     */
    protected function multiConnect($master = false) {
        $_config = array();
        // 分布式数据库解析
        $nameArr = array('username', 'password', 'hostname', 'port', 'database', 'dsn', 'charset');
        foreach ($nameArr as $name) {
            $_config[$name] = explode(',', $this->dbConfig[$name]);
        }

        // 主服务器序号
        $m = floor(mt_rand(0, $this->dbConfig['masterNum'] - 1));
        if ($this->dbConfig['rwSeparate']) {
            // 主从式采用读写分离
            if ($master) {
                // 主服务器写入
                $r = $m;
            } else {
                // 读操作连接从服务器 每次随机连接的数据库
                $r = floor(mt_rand($this->dbConfig['masterNum'], count($_config['hostname']) - 1));
            }
        } else {
            // 读写操作不区分服务器 每次随机连接的数据库
            $r = floor(mt_rand(0, count($_config['hostname']) - 1));
        }
        $dbMaster = false;
        if ($m != $r) {
            $dbMaster = array();
            foreach ($nameArr as $name) {
                $dbMaster[$name] = isset($_config[$name][$m]) ? $_config[$name][$m] : $_config[$name][0];
            }
        }
        $dbConfig = array();
        foreach ($nameArr as $name) {
            $dbConfig[$name] = isset($_config[$name][$r]) ? $_config[$name][$r] : $_config[$name][0];
        }
        return $this->connect($dbConfig, $r, $dbMaster);
    }

    /**
     * @param bool $master 是否主服务器
     * 初始化数据库连接
     */
    protected function initConnect($master = true) {
        if (!empty($this->dbConfig['deploy'])) {
            // 采用分布式数据库
            if ($master || $this->transTimes) {
                if (!$this->linkWrite) {
                    $this->linkWrite = $this->multiConnect(true);
                }
                $this->linkID = $this->linkWrite;
            } else {
                if (!$this->linkRead) {
                    $this->linkRead = $this->multiConnect(false);
                }
                $this->linkID = $this->linkRead;
            }
        } elseif (!$this->linkID) {
            // 默认单数据库
            $this->linkID = $this->connect($this->dbConfig);
        }
    }

    public function close() {
        $this->linkID    = null;
        $this->linkWrite = null;
        $this->linkRead  = null;
        $this->links = array();
    }

    public function __destruct()
    {
        $this->close();
    }
}