<?php


namespace Bboyyue\Channel\worker\event;


class onBegin
{
    private $errorMsg;
    public function run($data,$connection){}
    public function getErrorMsg()
    {
        return $this->errorMsg;
    }
}