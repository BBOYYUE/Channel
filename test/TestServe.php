<?php
use Workerman\Worker;
include_once "../vendor/autoload.php";
include_once "../vendor/workerman/channel/src/Server.php";

// 不传参数默认是监听0.0.0.0:2206
$channel_server = new Channel\Server();


if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}
