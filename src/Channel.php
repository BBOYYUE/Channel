<?php

namespace Bboyyue\Channel;

class Channel
{
    static function load():array
    {
        return [
            commands\ChannelClient::class,
            commands\ChannelServer::class,
            commands\ChannelStatus::class,
        ];
    }
}