<?php
// +----------------------------------------------------------------------
// | Demo [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.lmx0536.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <http://www.lmx0536.cn>
// +----------------------------------------------------------------------
// | Date: 2016/11/16 Time: 16:35
// +----------------------------------------------------------------------
namespace limx\tools;

use Redis;

class LRedis
{
    protected static $_instance = [];
    protected $redis;

    /**
     * MyRedis constructor.
     * @param $host
     * @param $port
     * @param $auth
     */
    private function __construct($host, $port, $auth, $db)
    {
        try {
            $this->redis = new Redis();
            $this->redis->connect($host, $port);
            if (!empty($auth)) {
                $this->redis->auth($auth);
            }
            if ($db > 0) {
                $this->redis->select($db);
            }
        } catch (PDOException $e) {
            $this->outputError($e->getMessage());
        }
    }

    /**
     * 防止克隆
     */
    private function __clone()
    {
    }

    /**
     * [getInstance desc]
     * @author limx
     * @param array $config = [
     * 'host' => '127.0.0.1',
     * 'auth' => '',
     * 'port' => '6379',
     * ];
     * @return MyRedis|null
     */
    public static function getInstance($config = [])
    {
        if (file_exists(__DIR__ . '/config.php')) {
            $default = include('config.php');
            $config = $config + $default;
        }

        $host = $config['host'];
        $auth = $config['auth'];
        $port = $config['port'];
        $db = $config['database'];

        $key = md5(json_encode($config));

        if (empty(self::$_instance[$key])) {
            self::$_instance[$key] = new self($host, $port, $auth, $db);
        }
        return self::$_instance[$key];
    }

    /**
     * [__call desc]
     * @author limx
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->redis, $name], $arguments);
    }

    /**
     * 输出错误信息
     *
     * @param String $strErrMsg
     */
    private function outputError($strErrMsg)
    {
        throw new Exception('Redis Error: ' . $strErrMsg);
    }
}