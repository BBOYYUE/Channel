<?php

namespace Bboyyue\Channel\worker;

use Workerman\Worker;
use Illuminate\Support\Str;
use Bboyyue\Channel\worker\client\event;

class client
{
    /**
     * client listen
     */
    function listen()
	{
        $config = new config();
        $worker = new Worker($config->getClientLink(),$config->getClientContext());
		if($config->getClientIsSsl()) $worker->transport = 'ssl';
		$worker->count = $config->getClientProcCount();
		// 全局群组到连接的映射数组

        $event = new event($config);

        $worker->onWorkerStart = array($event, 'onWorkerStart');
        $worker->onConnect     = array($event, 'onConnect');
        $worker->onMessage     = array($event, 'onMessage');
        $worker->onClose       = array($event, 'onClose');
        $worker->onWorkerStop  = array($event, 'onWorkerStop');
        Worker::runAll();

	}


}


