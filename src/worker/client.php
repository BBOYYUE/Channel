<?php

namespace Bboyyue\Channel\worker;

use Workerman\Worker;
use Illuminate\Support\Str;
use Workerman\Lib\Timer;
use Bboyyue\Channel\worker\client\container;

class client
{
	static function listen()
	{
		$worker = new Worker(config('services.channel.client.port'),config('services.channel.client.context'));
		$worker->transport = 'ssl';
		$worker->count = 4;
		// 全局群组到连接的映射数组
        $event = new event();
        $worker->onWorkerStart = array($event, 'onWorkerStart');
        $worker->onConnect     = array($event, 'onConnect');
        $worker->onMessage     = array($event, 'onMessage');
        $worker->onClose       = array($event, 'onClose');
        $worker->onWorkerStop  = array($event, 'onWorkerStop');

		Worker::runAll();
	}


}

// 全局的保存链接的变量
$equipment_number_map = array();
