<?php
/**
 * 饭粒科技
 * Date: 2020/3/14 * Time: 15:26
 * Author: AnQin <an-qin@qq.com>
 * Copyright © 2020. Hangzhou FanLi Technology Co., Ltd All rights reserved.
 */

return [
    'type'            => \an\queue\connector\Beanstalkd::class,
    'queue'           => 'default',
    'host'            => '127.0.0.1',
    'port'            => 11300,
    'imp'             => 1,
    'timeout'         => 5,
    'reserve_timeout' => 10,
    'serialize'       => ['msgpack_pack', 'msgpack_unpack'],
];