<?php
/**
 * 饭粒科技
 * Date: 2020/3/14 * Time: 19:23
 * Author: AnQin <an-qin@qq.com>
 * Copyright © 2020. Hangzhou FanLi Technology Co., Ltd All rights reserved.
 */

namespace an\queue\job;


use an\queue\connector\Beanstalkd as Banstalkd;
use think\{queue\Job, App};

class Beanstalkd extends Job {
    private ?\Pheanstalk\Job $job = null;
    private int $attempts = 1;
    private array $parseRaw = [];

    public function __construct(App $app, Banstalkd $beanstalk, \Pheanstalk\Job $job, string $connection, string $queue) {
        $this->queue = $queue;
        $this->app = $app;
        $this->job = $job;
        /** @var Banstalkd instance */
        $this->instance = $beanstalk;
        $this->connection = $connection;
        $this->attempts = $this->payload()['attempts'] ?? 1;
    }

    public function payload(): array {
        return empty($this->parseRaw) ? $this->parseRaw = $this->instance->unpack($this->job->getData()) : $this->parseRaw;
    }

    public function getJobId(): int {
        return $this->job->getId();
    }

    public function attempts() {
        return $this->attempts;
    }

    public function release($delay = 0) {
        parent::release($delay);
        $this->delete();
        $this->instance->release($this->queue, $this->payload(), $delay, ++$this->attempts);
    }

    public function delete() {
        parent::delete();
        $this->instance->deleteReserved($this->job);
    }

    public function getRawBody(): ?array {
        dump($this->job->getData());
        return $this->payload();
    }
}