<?php
namespace Bboyyue\Channel\Exception;

use Exception;

class BaseException extends Exception
{
    public function getMsg(): string
    {
        return $this->getMessage();
    }
}