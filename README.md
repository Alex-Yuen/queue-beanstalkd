Queue-Beanstalkd
===============

> 运行环境要求PHP7.4+。

## 安装

~~~
composer create-project anqin/beanstalkd
~~~
## 卸载
安装后默认启用beanstalkd队列,如取消请直接卸载
~~~
composer remove anqin/beanstalkd
~~~
## 配置
修改queue.php配置文件
~~~
return [
    'default'     => 'beanstalkd',
    'connections' => [
        //...其他默认配置
        'beanstalkd' => [
            'type'            => '\an\queue\connector\Beanstalkd',
            'queue'           => 'default',//默认队列分组,默认分组必消费
            'host'            => '127.0.0.1',//服务器IP
            'port'            => 11300,//端口
            'imp'             => 0,//连接模式
            'timeout'         => 5,//连接服务器超时时间
            'reserve_timeout' => 10,//消费队列的等待时间
            'serialize'       => ['serialize', 'unserialize'],//序列化函数
        ],
        //...其他默认配置
    ],
    //...其他默认配置
];
~~~
配置文件在config/beanstalkd.php

## 使用
使用方式与think-queue一致
