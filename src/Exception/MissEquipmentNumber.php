<?php
namespace Bboyyue\Channel\Exception;

class MissEquipmentNumber extends BaseException
{
    public function getMsg(): string
    {
        return json_encode(['tapType'=>500,'equipmentNumber'=>$this->getMessage(),'message'=>'设备不在线']);
    }
}