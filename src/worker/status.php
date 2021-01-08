<?php
namespace Bboyyue\Channel\worker;
use Workerman\Worker;
use Channel\Client as ClientMange;
use Illuminate\Support\Facades\Redis as RedisMange;

class status{



    function __construct(){
        $config = new Config;
    }

    /**
     * @param string $product
     * @return mixed
     * 获取当前连接的设备号
     */
    function getEquipmentNumberMap($product = ''){
        return json_decode(RedisMange::get($this->config->equipment_number),true);
    }

    /**
     * @return array|mixed
     * 获取当前运行中的服务端数量
     */
    function getChannelServer(){
        $worker_map = json_decode(RedisMange::get($this->config->server_count),true);
        $worker_map = $worker_map?$worker_map:[];
        return $worker_map;
    }

    /**
     * @return array|mixed
     * 获取当前连接的客户端数量
     */
    function getChannelClient(){
        $worker_map = json_decode(RedisMange::get($this->config->client_count),true);
        $worker_map = $worker_map?$worker_map:[];
        return $worker_map;
    }

    /**
     * @return mixed
     * 获取当前连接的设备数量
     */
    function getChannelConnect(){
        return RedisMange::get($this->config->connect_count);
    }

    function showCount(){
        $server_count = $this->getChannelServer();
        $client_count = $this->getChannelClient();
        $equipment_number_count = $this->getEquipmentNumberMap();
        if(is_array($this->getChannelServer())) $server_count = count($this->getChannelServer());
        if(is_array($this->getChannelClient())) $client_count = count($this->getChannelClient());
        if(is_array($this->getEquipmentNumberMap())) $equipment_number_count = count($this->getEquipmentNumberMap());
        $connect = $this->getChannelConnect();
        echo "serv";
        echo "\t";
        echo "client";
        echo "\t";
        echo "equip";
        echo "\t";
        echo "connect";
        echo "\t\r\n";
        echo $server_count;
        echo "\t";
        echo $client_count;
        echo "\t";
        echo $equipment_number_count;
        echo "\t";
        echo $connect."\n";

    }
}