<?php


namespace Bboyyue\Channel\worker\method;

use Bboyyue\Channel\worker\event\onBegin;
use Channel\Client as ClientMange;

class forward extends onBegin
{
    public function run($data,$connection){
        ClientMange::publish($data['channel'],$data);
        return $data;
    }
}