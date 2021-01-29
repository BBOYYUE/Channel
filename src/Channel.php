<?php

namespace Bboyyue\Channel;

class Channel
{
    static function load()
    {
        return [
            commands\ChannelClient::class,
            commands\ChannelServer::class,
        ];
    }
}