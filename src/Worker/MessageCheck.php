<?php


namespace Bboyyue\Channel\Worker;


trait MessageCheck
{

    private function checkString($data): bool
    {
        if (is_string($data)||is_int($data)) return true;
        else return false;
    }
}