<?php
namespace Bboyyue\Channel\Exception;

class ErrorMessage extends BaseException
{
    public function getMsg(): string
    {
        return json_encode(['tapTip'=>500,'message'=>$this->getMessage(),'msg'=>'message不正确']);
    }
}