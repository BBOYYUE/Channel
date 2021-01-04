<?php


namespace Bboyyue\Channel\worker\client;

class container
{


    static function make($obj_name)
    {
        $container = self::check(config($obj_name))?config($obj_name):"container";
        return new $container;
    }

    static function check($obj_name)
    {
        return class_exists($obj_name);
    }
    function run(int $arg):string
    {
        return 123;
    }
    function send()
    {

    }
    function listen()
    {

    }
}