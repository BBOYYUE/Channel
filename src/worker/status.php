<?php
namespace Bboyyue\AlphaChannel\worker;
use Workerman\Worker;
use Channel\Client as ClientMange;
use Illuminate\Support\Facades\Redis as RedisMange;

class status{
    function getEquipmentNumberMap($product = ''){
        return json_decode(RedisMange::get('alpha_channel_equipment_number'),true);
    }

    function getChannelServer(){
        $worker_map = json_decode(RedisMange::get('alpha_channel_server'),true);
        $worker_map = $worker_map?$worker_map:[];
        return $worker_map;
    }
    function getChannelClient(){
        $worker_map = json_decode(RedisMange::get('alpha_channel_client'),true);
        $worker_map = $worker_map?$worker_map:[];
        return $worker_map;
    }
    function getChannelConnect(){
        return RedisMange::get('alpha_channel_connect');
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