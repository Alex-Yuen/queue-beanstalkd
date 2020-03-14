<?php
/**
 * 饭粒科技
 * Date: 2020/3/14 * Time: 15:26
 * Author: AnQin <an-qin@qq.com>
 * Copyright © 2020. Hangzhou FanLi Technology Co., Ltd All rights reserved.
 */

namespace an\queue;

use think\{App, queue\Job};

class CallQueuedHandler {
    protected ?App $app = null;

    public function __construct(App $app) {
        $this->app = $app;
    }

    public function call(Job $job, array $data) {
        $command = $this->unpack($data['command']);
        $this->app->invoke([$command, 'handle']);
        if (!$job->isDeletedOrReleased()) $job->delete();
    }

    private function unpack($data) {
        $unserialize = $this->app->config->get('beanstalkd.serialize', ['serialize', 'unserialize'])[1] ?? 'unserialize';
        return $unserialize($data);
    }

    public function failed(array $data) {
        $command = $this->unpack($data['command']);
        if (method_exists($command, 'failed')) $command->failed();
    }
}
