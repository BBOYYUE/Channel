<?php
namespace Bboyyue\Channel;

use Bboyyue\Channel\Worker\Event;
use Bboyyue\Channel\Worker\Server;
use Workerman\Worker;

class WebsocketStart
{
    public static function runAll(){
        $server = new Server();
        $worker = new Worker($server->getWebsocketAddress());
        $event = new Event($server);
        $worker->onWorkerStart = array($event, 'onWorkerStart');
        $worker->onConnect     = array($event, 'onConnect');
        $worker->onMessage     = array($event, 'onMessage');
        $worker->onClose       = array($event, 'onClose');
        $worker->onWorkerStop  = array($event, 'onWorkerStop');
        Worker::runAll();
    }
}
