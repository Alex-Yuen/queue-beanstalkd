<?php
/**
 * 饭粒科技
 * Date: 2020/3/14 * Time: 15:26
 * Author: AnQin <an-qin@qq.com>
 * Copyright © 2020. Hangzhou FanLi Technology Co., Ltd All rights reserved.
 */

namespace an\queue;

use an\Queue;
use an\queue\command\Test;

class Service extends \think\Service {
    public function register() {
        $this->app->bind('queue', Queue::class);
        $config = $this->app->config->get('queue');
        $_config = $this->app->config->get('beanstalkd');
        $config['connections']['beanstalkd'] = $_config;
        $config['default'] = 'beanstalkd';
        $this->app->config->set($config, 'queue');
        unset($config, $_config);
    }

    public function boot() {
        $this->commands([Test::class]);
    }
}
