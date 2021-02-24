<?php


class ErrorData extends BaseException
{
    public function getMsg(): string
    {
        return json_encode(['tapTip'=>500,'data'=>$this->getMessage(),'msg'=>'消息格式不正确']);
    }
}