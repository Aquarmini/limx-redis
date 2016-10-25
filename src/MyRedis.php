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
    protected $prefix = 'demo:';

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


        if (self::$_instance === null) {
            self::$_instance = new self($host, $port, $auth, $db);
        }
        return self::$_instance;
    }

    public function setPrefix($prefix = '')
    {
        $this->prefix = $prefix;
    }

    public function select($id = 0)
    {
        $this->redis->select($id);
    }

    /**
     * [keys desc]
     * @desc 重写keys方法
     * @author limx
     * @param string $pattern
     */
    public function keys($pattern = '*')
    {
        $res = $this->redis->keys($this->retKey($pattern));
        foreach ($res as $i => $v) {
            $res[$i] = preg_replace('/' . $this->prefix . '/', '', $v, 1);
        }
        return $res;
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
                $arguments[0] = $this->retKey($arguments[0]);
            }
        }
        return call_user_func_array([$this->redis, $name], $arguments);
    }

    /**
     * [retKey desc]
     * @desc 为操作符增加前缀
     * @author limx
     * @param $key
     * @return string
     */
    public function retKey($key)
    {
        if (is_array($key)) {
            foreach ($key as $i => $v) {
                $key[$i] = $this->retKey($v);
            }
            return $key;
        } else {
            return $this->prefix . $key;
        }
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