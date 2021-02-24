<?php


class BaseException extends Exception
{
    public function getMsg(): string
    {
        return $this->getMessage();
    }
}