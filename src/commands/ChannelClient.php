<?php


namespace Bboyyue\Channel\Commands;

use Bboyyue\Channel\Worker\Worker;
use Illuminate\Console\Command;
use Bboyyue\Channel\worker\client;

/**
 * Class Accept
 * @package Bboyyue\WebsocketBridge\Commands
 * 接收器
 */
class ChannelClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Channel:client {action} {--option}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'alpha channel client';

    /*
     * 指定监听的端口
     */


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        global $argv;
        $action = $this->argument('action');

        $argv[0] = __FILE__;
        $argv[1] = $action;
        $argv[2] = $this->option('option') ? $this->option('option') : '';

        $agreement = $this->ask('Please enter agreement');
        $address = $this->ask('Please enter ip address');
        $port = $this->ask('Please enter port');

//        $server_agreement = $this->ask('Please enter server agreement:');
        $server_agreement = 'websocket';
        $server_address = $this->ask('Please enter server ip address');
        $server_port = $this->ask('Please enter server port');
        return client::listen($agreement.'://'.$address.":".$port,$server_agreement.'://'.$server_address.":".$server_port);
    }
}