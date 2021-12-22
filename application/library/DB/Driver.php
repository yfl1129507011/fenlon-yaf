<?php
/**
 * Driver.php Created by
 * User: fenlon
 * Date: 2021/12/14 16:32
 * Des: 数据库驱动虚拟类
 */
namespace DB;

use PDO;
use Yaf\Exception;

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

    // PDO操作实例
    protected $PDOStatement;

    // 影响记录行数
    protected $numRows = 0;
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

    /**
     * 获取最近插入的id
     * @param null $sequence 自增序列名
     * @return mixed
     */
    public function getLastInsertId($sequence = null) {
        return $this->linkID->lastInsertId($sequence);
    }

    /**
     * 执行查询 返回数据集
     * @param $sql sql指令
     * @param array $bind 参数绑定
     * @param bool $master 是否在主服务器读操作
     * @return bool
     * @throws \Exception
     */
    public function query($sql, $bind = [], $master = false) {
        $this->initConnect($master);
        if (empty($this->linkID)) {
            return false;
        }

        try {
            // 预处理
            $this->PDOStatement = $this->linkID->prepare($sql);
            // 参数绑定
            $this->bindValue($bind);
            // 执行语句
            $this->PDOStatement->execute();
            $result = $this->PDOStatement->fetchAll(PDO::FETCH_ASSOC);
            $this->numRows = count($result);
            return $result;
        } catch (\PDOException $e) {
            return $this->close()->query($sql, $bind, $master);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 执行语句
     * @param $sql  sql指令
     * @param array $bind 参数绑定
     * @return bool
     * @throws \Exception
     */
    public function execute($sql, $bind = array()) {
        $this->initConnect();
        if (empty($this->linkID)) {
            return false;
        }

        try {
            // 预处理
            $this->PDOStatement = $this->linkID->prepare($sql);
            // 参数绑定
            $this->bindValue($bind);
            // 执行语句
            $this->PDOStatement->execute();
            $this->numRows = $this->PDOStatement->rowCount();
            return $this->numRows;
        } catch (\PDOException $e) {
            return $this->close()->execute($sql, $bind);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function bindValue(array $bind = array()) {
        foreach ($bind as $k => $v) {
            // 占位符 (1, 'abc')或(':name', 'abc')
            $param = is_numeric($k) ? $k + 1 : ':' . $k;
            if (is_array($v)) {
                if (PDO::PARAM_INT == $v[1] && '' === $v[0]) {
                    $v[0] = 0;
                }
                $result = $this->PDOStatement->bindValue($param, $v[0], $v[1]);
            } else {
                $result = $this->PDOStatement->bindValue($param, $v);
            }
            if (!$result) {
                throw new \PDOException("Error occurred when binding parameters '{$param}'");
            }
        }
    }

    /**
     * SQL安全过滤
     * @param $str SQL字符串
     * @param bool $master
     * @return mixed
     */
    public function quote($str, $master = true) {
        $this->initConnect($master);
        return $this->linkID ? $this->linkID->quote($str) : $str;
    }

    /**
     * @param array $config 数据库配置信息
     * @param int $linkNum 连接id序号
     * @param array|bool $autoConnection 是否自动连接主数据库（用于分布式），
     * 自动连接主数据库时，该参数传入主数据库的配置信息
     * @return mixed
     * 连接数据库方法
     */
    public function connect(array $config, $linkNum = 0, $autoConnection = false) {
        if (!isset($this->links[$linkNum])) {
            if (!empty($config['params']) && is_array($config['params'])) {
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
                if ($autoConnection) {
                    return $this->connect($autoConnection, $linkNum);
                } else {
                    throw $e;
                }
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

    /**
     * 启动事务
     * @return bool
     */
    public function startTrans() {
        $this->initConnect(true);
        if (!$this->linkID) {
            return false;
        }

        ++$this->transTimes;
        try {
            if (1 == $this->transTimes) {
                $this->linkID->beginTransaction();
            }
        } catch (\Exception $e) {
            return $this->close()->startTrans();
        } catch (\Error $e) {
            throw $e;
        }
    }

    /**
     * 事务提交
     */
    public function commit() {
        $this->initConnect(true);
        if (1 == $this->transTimes) {
            $this->linkID->commit();
        }

        --$this->transTimes;
    }

    /**
     * 事务回滚
     */
    public function rollback() {
        $this->initConnect(true);
        if (1 == $this->transTimes) {
            $this->linkID->rollBack();
        }

        $this->transTimes = max(0, $this->transTimes - 1);
    }

    public function close() {
        $this->linkID    = null;
        $this->linkWrite = null;
        $this->linkRead  = null;
        $this->links = array();
        $this->PDOStatement = null;
        return $this;
    }

    public function __destruct()
    {
        $this->close();
    }
}