<?php
namespace Bboyyue\Channel\Exception;

class ErrorMethod extends BaseException
{
    public function getMsg(): string
    {
        return json_encode(['tapType'=>500,'method'=>$this->getMessage(),'msg'=>'method不正确']);
    }
}