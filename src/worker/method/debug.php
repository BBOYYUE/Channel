<?php


namespace Bboyyue\Channel\worker\method;

use Bboyyue\Channel\worker\event\onEnd;
use Channel\Client as ClientMange;

class debug extends onEnd
{
    public function run($data,$connection)
    {
        $debug = [
            'method'=>'send',
            'equipment_number'=>999
        ];
        if(is_array($data)||is_object($data))  $debug['message'] = json_encode($data);
        else $debug['message'] = $data;
        ClientMange::publish($data['channel'],$debug);
        return $data;
    }
}
