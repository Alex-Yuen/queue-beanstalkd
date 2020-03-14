<?php
/**
 * Created by PhpStorm.
 * Date: 2019/6/1 * Time: 15:36
 * Author: AnQin <an-qin@qq.com>
 * Copyright (c) 2014-2019 AnQin All rights reserved.
 */

namespace an\queue;

use think\queue\Job;

interface InterfaceJob {
    public function failed($data, $exception);

    public function fire(Job $job, $data);
}