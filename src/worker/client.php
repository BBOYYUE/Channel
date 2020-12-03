<?php

namespace Bboyyue\AlphaChannel\worker;

use Workerman\Worker;
use Channel\Client as ClientMange;
use Illuminate\Support\Facades\Redis as RedisMange;
use Illuminate\Support\Str;


class client
{

    /**
     * @param string $client
     * @param string $server
     * @param string $product
     */
    static function listen($client = 'websocket://0.0.0.0:1234', $server = 'websocket://127.0.0.1:2206', $product = '')
    {

        $server_agreement = Str::before($server, ':');
        $server_ip = Str::after($server, '://');
        $server_port = Str::after($server_ip, ":");
        $server_ip = Str::before($server_ip, ":");

        $worker = new Worker($client);

        $worker->count = 1;
        // 全局群组到连接的映射数组
        $worker->onWorkerStart = function ($worker) use ($product, $server_agreement, $server_ip, $server_port) {
            $worker_id = $worker->id;
            $worker_map = json_decode(RedisMange::get('alpha_channel_client'), true);
            $worker_map = isset($worker_map) && is_array($worker_map) ? array_push($worker_map, $worker_id) : [$worker_id];
            RedisMange::set('alpha_channel_client', json_encode($worker_map));

            $channel = 'alpha_channel_' . $product;
            // 频道暂时不区分项目了
            $channel = 'alpha_channel';


            // Channel客户端连接到Channel服务端
            ClientMange::connect($server_ip, $server_port);

            // 监听全局分组发送消息事件
            ClientMange::on($channel, function ($event_data) use ($channel) {
                global $equipment_number_map;
                if(is_array($equipment_number_map)) $equipment_numbers = array_keys($equipment_number_map);
                else $equipment_numbers = 0;
                $equipment_number = $event_data['equipment_number'];
                $message = $event_data['message'];
                if (isset($equipment_number_map[$equipment_number])) {
                    foreach ($equipment_number_map[$equipment_number] as $con) {
                        $con->send($message);
                    }
                }
                RedisMange::set('alpha_channel_equipment_number',json_encode($equipment_numbers));
            });
        };

        $worker->onMessage = function ($con, $data) {
            global $equipment_number_map;
            $data = json_decode($data, true);
            $cmd = isset($data['method']) ? $data['method'] : 'listen';
            // 设备号
            $equipment_number = isset($data['equipment_number']) ? $data['equipment_number'] : 0;
            // 项目id
            $product = isset($data['product']) ? $data['product'] : '';
            // 频道
            $channel = 'alpha_channel_' . $product;
            // 频道暂时不区分项目了
            $channel = 'alpha_channel';
            // 设备列表

            switch ($cmd) {
                // 需要监听某个频道
                case "listen":
                    // 通过设备id获取链接id.
                    $equipment_number_map[$equipment_number][$con->id] = $con;
                    // 保存链接的设备ID.
                    $con->equipment_number = isset($con->equipment_number) ? $con->equipment_number : array();
                    // 保存这个设备在内个频道
                    $con->chanenl_id[$equipment_number] = $channel;
                    break;
                // 需要往某个频道发送消息
                case "send":
                    // 往指定的频道发消息
                    ClientMange::publish($channel, array(
                        'equipment_number' => $equipment_number,
                        'message' => $data['message']
                    ));
                    break;
            }
        };
        // 这里很重要，连接关闭时把连接从全局群组数据中删除，避免内存泄漏
        $worker->onClose = function ($con) use ($product) {
            global $equipment_number_map;
            // 遍历连接加入的所有群组，从group_con_map删除对应的数据
            if (isset($con->equipment_number)) {
                foreach ($con->equipment_number as $equipment_number) {
                    unset($equipment_number_map[$equipment_number][$con->id]);
                    if (empty($equipment_number_map[$equipment_number])) {
                        unset($equipment_number_map[$equipment_number]);
                    }
                }
            }

            $count = RedisMange::get('alpha_channel_connect')?RedisMange::get('alpha_channel_connect'):0;
            $count--;
            RedisMange::set('alpha_channel_connect',$count);
        };
        $worker->onConnect = function($connection)
        {
            $count = RedisMange::get('alpha_channel_connect')?RedisMange::get('alpha_channel_connect'):0;
            $count++;
            RedisMange::set('alpha_channel_connect',$count);
        };
        Worker::runAll();
    }


}

// 全局的保存链接的变量
$equipment_number_map = array();
