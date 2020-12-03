<?php

namespace Bboyyue\Channel;

class Channel
{
    static function load()
    {
        return [
            commands\ChannelClient::class,
            commands\ChannelServer::class,
            commands\ChannelStatus::class,
//            Commands\Forward::class,
//            Commands\TestRelease::class,
//            Commands\TestSubscribe::class
        ];
    }
}