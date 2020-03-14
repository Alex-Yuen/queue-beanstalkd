<?php
/**
 * 饭粒科技
 * Date: 2020/3/14 * Time: 15:26
 * Author: AnQin <an-qin@qq.com>
 * Copyright © 2020. Hangzhou FanLi Technology Co., Ltd All rights reserved.
 */

namespace an;

use InvalidArgumentException;
use think\helper\Str;
use think\Queue as _Queue;
use think\queue\Connector;

class Queue extends _Queue {
    protected function createDriver($name) {
        $config = $this->getConfig($name);
        $driver = $config['type'];

        $class = false !== strpos($driver, '\\') ? $driver : $this->namespace . Str::studly($driver);

        /** @var Connector $driver */
        if (class_exists($class)) {
            $driver = $this->app->invokeClass($class, [$config]);

            return $driver->setApp($this->app)->setConnection($name);
        }

        throw new InvalidArgumentException("Driver [$driver] not supported.");
    }
}
