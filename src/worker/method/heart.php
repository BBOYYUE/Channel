<?php


namespace Bboyyue\Channel\worker\method;


use Bboyyue\Channel\worker\event\forTapType;

class heart extends forTapType
{
    public function run($data,$connection){
        $heart = [
            'tapType'=>113,
            'message'=>"你现在在线!"
        ];
        $connection->send(json_encode($heart));
        return $data;
    }
}