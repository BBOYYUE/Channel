<?php


namespace Bboyyue\Channel\Worker;

use Channel\Client as ClientMange;

use ErrorData;
use ErrorEquipmentNumber;
use Exception;
use Workerman\Lib\Timer;

class Event
{
    private Server $server;
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * 当与转发服务器建立连接时执行的方法
     * 新版支持多频道,频道由名称和转发逻辑(闭包)组成
     * 不同的频道执行不同的转发逻辑
     * @param $worker
     * @throws Exception
     */
    public function onWorkerStart($worker):void
    {
        $address = $this->server->getServerAddress();
        ClientMange::connect($address[0], $address[1]);
        $server = $this->server;
        ClientMange::on('public',function($event_data) use ($server){
                        $equipmentNumberMap = $server->getEquipmentNumberMap();
                        $equipmentNumber = $event_data['equipment_number'];
                            // 如果消息体是对象或者数组的话,是不能直接进行转发的.所以需要进行检查
                        if(is_array($event_data['message'])||is_object($event_data['message'])){
                            $event_data['message'] = json_encode($event_data['message']);
                        }
                            // 公开频道向所有设备转发
                        foreach ($equipmentNumberMap as $con) {
                             $con->send($event_data['message']);
                        }
                    });
        ClientMange::on('protected',function($event_data) use ($server){
                        $equipmentNumberMap = $server->getEquipmentNumberMap();
                        $equipmentNumber = $event_data['equipment_number'];
                        if (isset($equipmentNumberMap[$equipmentNumber])) {
                            // 如果消息体是对象或者数组的话,是不能直接进行转发的.所以需要进行检查
                            if(is_array($event_data['message'])||is_object($event_data['message'])){
                                $event_data['message'] = json_encode($event_data['message']);
                            }
                            $clientList = $event_data['clientList'];
                            // 受保护频道向所有发送过消息的人转发
                            foreach ($equipmentNumberMap as $key => $con) {
                                if(in_array($key,$clientList)){
                                    $con->send($event_data['message']);
                                }
                            }
                        }
                    });
        ClientMange::on('private',function($event_data) use ($server){
                        $equipmentNumberMap = $server->getEquipmentNumberMap();
                        $equipmentNumber = $event_data['equipment_number'];
                        if (isset($equipmentNumberMap[$equipmentNumber])) {
                            // 如果消息体是对象或者数组的话,是不能直接进行转发的.所以需要进行检查
                            if(is_array($event_data['message'])||is_object($event_data['message'])){
                                $event_data['message'] = json_encode($event_data['message']);
                            }
                            // 只向监听这个设备的设备转发
                            foreach ($equipmentNumberMap[$equipmentNumber] as $con) {
                                $con->send($event_data['message']);
                            }
                        }
                    });
        $this->heart($worker);
    }

    public function onWorkerStop()
    {}

    /**
     * 每个连接成功建立时执行的方法,传入参数为连接对象
     * @param $connection
     */
    public function onConnect($connection): void
    {
        $connection->id = $connection->worker->id.$connection->id;
        $connection->client = new Client($connection->id);
        $connection->client->setLastMessageTime(time());
        $msg = ['tapTip'=>200,'msg'=>'连接成功'];
        $connection->send(json_encode($msg));
    }

    /**
     * 连接关闭时执行的方法, 需要清除连接id与设备号的绑定关系
     * 首先检查有没有设备号与此连接相绑定,有的话解除绑定
     * 然后检查设备号是否已经没有连接与其绑定了,是的话检查所有的连接,是否有连接与此设备号绑定,有的话解除绑定
     * @param $connection
     */
    public function onClose($connection): void
    {
        $this->server->delEquipmentNumberMap($connection->id,$connection);
        $msg = ['tapTip'=>200,'msg'=>'连接关闭'];
        $connection->send(json_encode($msg));
    }

    /**
     * 接收到连接发送的消息时执行的方法
     * @param $connection
     * @param $data
     */
    public function onMessage($connection,$data)
    {
	    try {
		echo $data."\r\n";
            $message = new Message($data);
            MessageMethod::run($message,$this->server, $connection);
            $connection->client->setLastMessageTime(time());
        } catch (Exception $e){
            if($e instanceof \BaseException) $connection->send($e->getMsg());
            else $connection->send($e->getMessage());
        }
    }

    protected function heart($worker)
    {
        Timer::add(10, function() use ($worker){
            $time_now = time();
            foreach($worker->connections as $connection) {

                if(!isset($connection->client)){
                    $connection->id = $connection->worker->id.$connection->id;
                    $connection->client = new Client($connection->id);
                    $connection->client->setLastMessageTime(time());
                }

                // 上次通讯时间间隔大于心跳间隔，则认为客户端已经下线，关闭连接
                if ($time_now - (int)$connection->client->getLastMessageTime() > 55) {
                    $data = [
                        'tapTip'=>500,
                        'message'=>"你没有心跳!"
                    ];
                    $connection->send(json_encode($data));
                    $connection->close();
                }
            }
        });
    }
}
