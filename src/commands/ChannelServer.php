<?php


namespace Bboyyue\Channel\Commands;

use Bboyyue\Channel\Worker\Worker;
use Illuminate\Console\Command;
use Bboyyue\Channel\worker\server;

/**
 * Class Accept
 * @package Bboyyue\WebsocketBridge\Commands
 * 接收器
 */
class ChannelServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Channel:server {action} {--d}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'alpha channel server';

    /*
     * 指定监听的端口
     */
    protected $accept = '8084';


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
        $argv[2] = $this->option('d') ? '-d' : '';

        $server = new server('127.0.0.1','2206');
        return $server->listen();
    }
}