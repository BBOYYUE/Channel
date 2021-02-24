<?php


class ErrorEquipmentNumber extends BaseException
{
    public function getMsg(): string
    {
        return json_encode(['tapTip'=>500,'equipmentNumber'=>$this->getMessage(),'msg'=>'设备号不正确']);
    }
}