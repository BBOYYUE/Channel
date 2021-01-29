<?php


namespace Bboyyue\Channel\worker\client;

use Bboyyue\Channel\worker\config;
use Channel\Client as ClientMange;
use Illuminate\Support\Facades\Redis as RedisMange;
use Bboyyue\Channel\worker\client\container;
use Workerman\Lib\Timer;


class event
{
    // status 负责统计当前状态的类
    public $status;
    public $log;
    protected $container;
    protected $config;

    public function __construct(config $config)
    {
        $this -> config = $config;

    }

    public function onConnect($connection)
    {
    }

    /**
     * @param $worker
     * 这里建立了客户端和服务端的连接
     *
     */
    public function onWorkerStart($worker)
    {
        $channel = 'channel';
        // Channel客户端连接到Channel服务端
        ClientMange::connect('127.0.0.1', '2206');

        // 监听全局分组发送消息事件
        // 这里根据消息中的设备号 进行消息转发
        ClientMange::on($channel, function ($event_data) {
            global $equipment_number_map;
            $equipment_number = $event_data['equipment_number'];
            if (isset($equipment_number_map[$equipment_number])) {
                if(is_array($event_data['message'])||is_object($event_data['message'])){
                    $event_data['message'] = json_encode($event_data['message']);
                }
                foreach ($equipment_number_map[$equipment_number] as $con) {
                    $con->send($event_data['message']);
                }
            }
        });
        // 心跳,如果十秒不发消息自动断开
        $this->heart($worker);
    }

    public function onMessage($connection,$data)
    {
        global $equipment_number_map;
        // 心跳功能必须要的
        try {
            $connection->lastMessageTime = time();
            $container = container::make($this->config,$data);
            if($error = $container->getErrorMsg()) throw new \Exception("容器初始化失败 :". $error);

            $data = $container->onBegin($connection);
            if($error = $container->getErrorMsg()) throw new \Exception("Begin 事件执行失败:". $error);
            $data = $container->forTapType($connection);

            if($error = $container->getErrorMsg()) throw new \Exception("TapType 事件执行失败:".$error);
            $container->onEnd($connection);
            if($error = $container->getErrorMsg()) throw new \Exception("End 事件执行失败:".$error);

        }catch (\Exception $e){
            $connection->send($e->getMessage());
        }
    }
    public function onClose($connection)
    {
        global $equipment_number_map;
        // 遍历连接加入的所有群组，从group_con_map删除对应的数据
        if (isset($con->equipment_number)) {
            foreach ($con->equipment_number as $equipment_number) {
                // unset 拥有这个设备号的连接
                unset($equipment_number_map[$equipment_number][$con->id]);
                // 如果这个设备号没有人连,那么清空设备号
                if (empty($equipment_number_map[$equipment_number])) {
                    unset($equipment_number_map[$equipment_number]);
                }
            }
        }

    }
    public function onWorkerStop($connection)
    {}
    protected function heart($worker)
    {
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
                    $data = [
                        'taoType'=>113,
                        'message'=>"你没有心跳!"
                    ];
                    $connection->send(json_encode($data));
                    $connection->close();
                }
            }
        });
    }
}
// 全局的保存链接的变量
$equipment_number_map = array();