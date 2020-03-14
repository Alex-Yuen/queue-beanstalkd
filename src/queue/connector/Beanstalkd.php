<?php
/**
 * 饭粒科技
 * Date: 2020/3/14 * Time: 15:26
 * Author: AnQin <an-qin@qq.com>
 * Copyright © 2020. Hangzhou FanLi Technology Co., Ltd All rights reserved.
 */

namespace an\queue\connector;

use think\queue\Connector;
use \an\queue\job\Beanstalkd as BeanstalkdJob;
use Pheanstalk\{Connection, Job, Pheanstalk, SocketFactory, Contract\PheanstalkInterface};
use Throwable;

class Beanstalkd extends Connector {
    /** @var  Pheanstalk */
    protected ?Pheanstalk $beanstalk = null;

    public function __construct(Pheanstalk $beanstalk, array $config) {
        $this->options = $config;
        $this->beanstalk = $beanstalk;
    }

    public static function __make(array $options) {
        $beanstalk = new Pheanstalk(new Connection(new SocketFactory($options['host'], $options['port'], $options['timeout'], $options['imp'])));
        return new self($beanstalk, $options);
    }

    public function getHandler(): Pheanstalk {
        return $this->beanstalk;
    }

    public function push($job, $data = '', $queue = null): Job {
        return $this->pushRaw($this->createPayload($job, $data), $queue);
    }

    public function pushRaw($payload, $queue = null, $delay = null): Job {
        return $this->beanstalk->useTube($queue ?? $this->options['queue'])
                               ->put($this->pack($payload), PheanstalkInterface::DEFAULT_PRIORITY, $delay ?? PheanstalkInterface::DEFAULT_DELAY);
    }

    /**
     * 序列化数据
     * @access protected
     * @param mixed $data
     * @return string
     */
    public function pack($data) {
        $serialize = $this->options['serialize'][0] ?? 'serialize';
        return $serialize($data);
    }

    protected function createPayload($job, $data = '') {
        $payload = $this->createPayloadArray($job, $data);
        return $payload;
    }

    public function size($queue) {
        return -1;
    }

    public function later($delay, $job, $data = '', $queue = null) {
        return $this->pushRaw($this->createPayload($job, $data), $queue);
    }

    public function pop($queue = null): ?BeanstalkdJob {
        $queue = $queue ?? $this->options['queue'];
        try {
            $job = $this->beanstalk->watch($queue)->reserveWithTimeout($this->options['reserve_timeout']);
            if ($job instanceof Job) return new BeanstalkdJob($this->app, $this, $job, $this->connection, $queue);
        } catch (Throwable $e) {
        }

        return null;
    }

    /**
     * 重新发布任务
     * @param string $queue
     * @param array  $payload
     * @param int    $delay
     * @param int    $attempts
     * @return mixed
     */
    public function release($queue, array $payload, $delay, $attempts) {
        $payload = $this->setMeta($payload, 'attempts', $attempts);
        return $this->beanstalk->useTube($queue)
                               ->put($this->pack($payload), PheanstalkInterface::DEFAULT_PRIORITY, $delay ?? PheanstalkInterface::DEFAULT_DELAY);
    }

    protected function setMeta($payload, $key, $value) {
        $payload[$key] = $value;

        return $payload;
    }

    /**
     * 删除任务
     * @param Job $job
     * @return void
     */
    public function deleteReserved($job) {
        $this->beanstalk->delete($job);
    }

    /**
     * 反序列化数据
     * @access protected
     * @param string $data
     * @return mixed
     */
    public function unpack($data) {
        $unserialize = $this->options['serialize'][1] ?? 'unserialize';
        try {
            return $unserialize($data);
        } catch (Throwable$e) {
            return unserialize($data);
        }
    }

    protected function createObjectPayload($job) {
        return [
            'job'       => 'an\queue\CallQueuedHandler@call',
            'maxTries'  => $job->tries ?? null,
            'timeout'   => $job->timeout ?? null,
            'timeoutAt' => $this->getJobExpiration($job),
            'data'      => [
                'commandName' => get_class($job),
                'command'     => $this->pack(clone $job),
            ],
        ];
    }
}
