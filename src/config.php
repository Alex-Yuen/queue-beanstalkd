<?php
/**
 * 饭粒科技
 * Date: 2020/3/14 * Time: 15:26
 * Author: AnQin <an-qin@qq.com>
 * Copyright © 2020. Hangzhou FanLi Technology Co., Ltd All rights reserved.
 */

use \an\queue\connector\Beanstalkd;
use Pheanstalk\SocketFactory;
use think\facade\Env;

return [
    'type'            => Beanstalkd::class,
    'queue'           => Env::get('beanstalkd.queue', 'default'),//默认队列分组,默认分组必消费
    'host'            => Env::get('beanstalkd.host', '127.0.0.1'),//服务器IP
    'port'            => Env::get('beanstalkd.port', 11300),//端口
    'imp'             => Env::get('beanstalkd.imp', SocketFactory::AUTODETECT),//连接模式
    'timeout'         => Env::get('beanstalkd.timeout', 5),//连接服务器超时时间
    'reserve_timeout' => Env::get('beanstalkd.reserve_timeout', 10),//消费队列的等待时间
    'serialize'       => ['serialize', 'unserialize'],//序列化函数
];