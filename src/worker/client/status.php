<?php


namespace Bboyyue\Channel\worker\client;

use Illuminate\Support\Facades\Redis as RedisMange;

class status
{
    function addClientCount(){
        $count = RedisMange::get('alpha_channel_connect')?RedisMange::get('alpha_channel_connect'):0;
        $count++;
        RedisMange::set('alpha_channel_connect',$count);
    }
    function addClientWorker($worker_id){
        $worker_map = json_decode(RedisMange::get('alpha_channel_client'), true);
        $worker_map = isset($worker_map) && is_array($worker_map) ? array_push($worker_map, $worker_id) : [$worker_id];
        RedisMange::set('alpha_channel_client', json_encode($worker_map));
    }
}