<?php
// +----------------------------------------------------------------------
// | Demo [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.lmx0536.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <http://www.lmx0536.cn>
// +----------------------------------------------------------------------
// | Date: 2016/8/17 Time: 10:35
// +----------------------------------------------------------------------
namespace limx\tools;

use Redis;

class MyRedis
{
    protected static $_instance = null;
    protected $redis;
    protected $prefix = 'demo_';

    /**
     * MyRedis constructor.
     * @param $host
     * @param $port
     * @param $auth
     */
    private function __construct($host, $port, $auth)
    {
        try {
            $this->redis = new Redis();
            $this->redis->connect($host, $port);
            if (!empty($auth)) {
                $this->redis->auth($auth);
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
     * @param array $config
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

        if (self::$_instance === null) {
            self::$_instance = new self($host, $port, $auth);
        }
        return self::$_instance;
    }

    public function setPrefix($prefix = '')
    {
        $this->prefix = $prefix;
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
        if (is_array($arguments)) {
            if (count($arguments) > 0) {
                $arguments[0] = $this->prefix . $arguments[0];
            }
        }
        return call_user_func_array([$this->redis, $name], $arguments);
    }


    /**
     * debug
     *
     * @param mixed $debuginfo
     */
    private function debug($debuginfo)
    {
        var_dump($debuginfo);
        exit();
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