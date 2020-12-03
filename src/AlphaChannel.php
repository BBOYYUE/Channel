<?php

namespace Bboyyue\AlphaChannel;

class AlphaChannel
{
    static function load()
    {
        return [
            commands\AlphaChannelClient::class,
            commands\AlphaChannelServer::class,
            commands\AlphaChannelStatus::class,
//            Commands\Forward::class,
//            Commands\TestRelease::class,
//            Commands\TestSubscribe::class
        ];
    }
}