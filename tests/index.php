<?php
// +----------------------------------------------------------------------
// | Demo [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.lmx0536.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <http://www.lmx0536.cn>
// +----------------------------------------------------------------------
// | Date: 2016/8/17 Time: 12:14
// +----------------------------------------------------------------------
namespace limx\tools;
require __DIR__ . '/../src/MyRedis.php';

$config['host'] = '';
$config['auth'] = '';
$config['database'] = 0;

$redis = MyRedis::getInstance($config);
$redis->select(1);
print_r($redis->keys('*'));
$redis->set('key1', 'val2');
print_r($redis->get('key1'));
