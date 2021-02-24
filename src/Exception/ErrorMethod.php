<?php


class ErrorMethod extends BaseException
{
    public function getMsg(): string
    {
        return json_encode(['tapTip'=>500,'method'=>$this->getMessage(),'msg'=>'method不正确']);
    }
}