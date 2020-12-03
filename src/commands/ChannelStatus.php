<?php


namespace Bboyyue\Channel\Commands;

use Bboyyue\Channel\Worker\Worker;
use Illuminate\Console\Command;
use Bboyyue\Channel\worker\status;

/**
 * Class Accept
 * @package Bboyyue\WebsocketBridge\Commands
 * 接收器
 */
class ChannelStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Channel:status {action} {--option}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '使用一个端口监听控制端请求';

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
        $action = $this->argument('action');
        switch ($action){
            case 'count':
                $status = new status();
                return $status->showCount();
        }
    }
}