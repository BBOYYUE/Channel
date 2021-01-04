<?php


namespace Bboyyue\Channel\worker\client;

use Channel\Client as ClientMange;
use Illuminate\Support\Facades\Redis as RedisMange;
class event
{
    // status 负责统计当前状态的类
    public $status;
    public $log;

    public function __construct()
    {
        $this -> status = new status();
        $this -> log = new log();
    }

    public function onConnect($connection)
    {
        $this->status->addClientCount();
    }
    public function onWorkerStart($worker)
    {
        $this->status->addClientWorker($worker->id);
        $channel = 'channel';
        // Channel客户端连接到Channel服务端
        ClientMange::connect(config('services.channel.client.server_ip'), config('services.channel.client.server_port'));

        // 监听全局分组发送消息事件
        ClientMange::on($channel, function ($event_data) {
            global $equipment_number_map;
            $equipment_number = $event_data['equipment_number'];
            if (isset($equipment_number_map[$equipment_number])) {
                foreach ($equipment_number_map[$equipment_number] as $con) {
                    $con->send($event_data['message']);
                }
            }
        });
        // 心跳,如果十秒不发消息自动断开
        Timer::add(10, function() use ($worker){
            $time_now = time();
            foreach($worker->connections as $connection) {
                // 有可能该connection还没收到过消息，则lastMessageTime设置为当前时间
                if (empty($connection->lastMessageTime)) {
                    $connection->lastMessageTime = $time_now;
                    continue;
                }
                // 上次通讯时间间隔大于心跳间隔，则认为客户端已经下线，关闭连接
                if ($time_now - $connection->lastMessageTime > 55) {
                    $connection->close();
                }
            }
        });
    }
    public function onMessage($connection,$data)
    {
        global $equipment_number_map;
        $connection->lastMessageTime = time();
        $data = json_decode($data, true);

        // 默认执行的操作是 listen
        $cmd = isset($data['method']) ? $data['method'] : 'listen';
        $channel = 'channel';
        if($this->check($cmd))
        {
            $method = container::make($cmd);
            $method->run($method);
            if($method->send) {
                ClientMange::publish($channel, $method->data);
                $this->log($method->name, $method->data);
            }
        }else{
            $this->log('error', $cmd);
        }

        // 设备列表
        /*
        switch ($cmd) {
            // 需要监听某个频道
            case "listen":
                // 通过设备id获取链接id.
                $equipment_number_map[$equipment_number][$connection->id] = $connection;
                // 保存这个链接和内个频道有通讯.
                $con->equipment_number = isset($con->equipment_number) ? $con->equipment_number : array();
                // 保存这个设备在内个频道
                $con->chanenl_id[$equipment_number] = $channel;
                break;
            // 需要往某个频道发送消息
            case "send":
                // 往指定的频道发消息
                if(!is_string($data['message'])) $data['message']=json_encode($data['message']);
                ClientMange::publish($channel, array(
                    'equipment_number' => $equipment_number,
                    'message' => $data['message']
                ));
                break;
        }
        */
    }
    public function onClose($connection)
    {
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

    }
    public function onWorkerStop($connection)
    {

    }
    public  function send()
    {

    }
    public function listen($connection,$data)
    {
        $equipment_number = $data['equipment_number'];
        $equipment_number_map[$equipment_number][$connection->id] = $connection;
        // 保存这个链接和内个频道有通讯.
        $this->addClientEquipment($connection->id,$equipment_number);
        if(isset($connection->equipment_number)){
            $connection->equipment_number[] = $equipment_number;
        }else{
            $connection->equipment_number = [$equipment_number];
        }

    }
}