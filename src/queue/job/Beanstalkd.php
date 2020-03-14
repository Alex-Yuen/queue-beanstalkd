<?php
/**
 * 饭粒科技
 * Date: 2020/3/14 * Time: 15:26
 * Author: AnQin <an-qin@qq.com>
 * Copyright © 2020. Hangzhou FanLi Technology Co., Ltd All rights reserved.
 */

namespace an\queue\job;

use think\{queue\Job, App};
use \an\queue\connector\Beanstalkd as Banstalkd;

class Beanstalkd extends Job {
    protected ?Banstalkd $beanstalk = null;
    protected array $parseRaw = [];
    protected ?\Pheanstalk\Job $job = null;
    protected int $attempts = 1;

    public function __construct(App $app, Banstalkd $beanstalk, \Pheanstalk\Job $job, string $connection,string $queue) {
        $this->job = $job;
        $this->queue = $queue;
        $this->beanstalk = $beanstalk;
        $this->parseRaw = $this->payload();
        $this->app = $app;
        $this->queue = $queue;
        $this->connection = $connection;

        $this->attempts = $this->parseRaw['attempts'] ?? 1;
    }

    public function payload() {
        return empty($this->parseRaw) ? $this->beanstalk->unpack($this->job->getData()) : $this->parseRaw;
    }

    public function attempts(): int {
        return $this->attempts;
    }

    public function getJobId() {
        return $this->job->getId();
    }

    /**
     * 重新发布任务
     * @param int $delay
     * @return void
     */
    public function release($delay = 0) {
        parent::release($delay);
        $this->delete();
        $this->beanstalk->release($this->queue, $this->getRawBody(), $delay, ++$this->attempts);
    }

    /**
     * 删除任务
     * @return void
     */
    public function delete() {
        parent::delete();
        $this->beanstalk->deleteReserved($this->job);
    }

    /**
     * Get the raw body string for the job.
     * @return array
     */
    public function getRawBody(): array {
        return $this->parseRaw;
    }
}
