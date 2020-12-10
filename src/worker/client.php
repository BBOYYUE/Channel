<?php

namespace Bboyyue\Channel\worker;

use Workerman\Worker;
use Channel\Client as ClientMange;
use Illuminate\Support\Facades\Redis as RedisMange;
use Illuminate\Support\Str;
use Workerman\Lib\Timer;

class client
{

	/**
	 * @param string $client
	 * @param string $server
	 * @param string $product
	 */
	static function listen($client = 'websocket://0.0.0.0:1234', $server = 'websocket://127.0.0.1:2206', $product = '',$crt='',$key='')
	{

		$server_agreement = Str::before($server, ':');
		$server_ip = Str::after($server, '://');
		$server_port = Str::after($server_ip, ":");
		$server_ip = Str::before($server_ip, ":");
		$context = array(
				'ssl' => array(
					// 请使用绝对路径
					'local_pk'                => '/etc/letsencrypt/live/www.alphavisual.cn/privkey.pem', // 也可以是crt文件
					'local_cert'                  => '/etc/letsencrypt/live/www.alphavisual.cn/fullchain.pem',
					'verify_peer'                => false,
					'verify_peer_name' =>false
					)
				);

		//$worker = new Worker($client,$context);
		$worker = new Worker('websocket://0.0.0.0:8086',$context);
		$worker->transport = 'ssl';
		$worker->count = 4;
		// 全局群组到连接的映射数组
		$worker->onWorkerStart = function ($worker) use ($product, $server_agreement, $server_ip, $server_port) {
			$worker_id = $worker->id;
			$worker_map = json_decode(RedisMange::get('alpha_channel_client'), true);
			$worker_map = isset($worker_map) && is_array($worker_map) ? array_push($worker_map, $worker_id) : [$worker_id];
			RedisMange::set('alpha_channel_client', json_encode($worker_map));

			$channel = 'alpha_channel_' . $product;
			// 频道暂时不区分项目了
			$channel = 'alpha_channel';


			// Channel客户端连接到Channel服务端
			$server_ip = '127.0.0.1';
			$server_port = '2206'; 
			ClientMange::connect($server_ip, $server_port);

			// 监听全局分组发送消息事件
			ClientMange::on($channel, function ($event_data) use ($channel) {
					global $equipment_number_map;
					if(is_array($equipment_number_map)) $equipment_numbers = array_keys($equipment_number_map);
					else $equipment_numbers = 0;
					$equipment_number = $event_data['equipment_number'];
					$message = json_encode(json_decode($event_data['message']));
					if (isset($equipment_number_map[$equipment_number])) {
					foreach ($equipment_number_map[$equipment_number] as $con) {
						$con->send($message);
					}
					}
					RedisMange::set('alpha_channel_equipment_number',json_encode($equipment_numbers));
			});
			Timer::add(10, function()use($worker){
        			$time_now = time();
        			foreach($worker->connections as $connection) {
            				// 有可能该connection还没收到过消息，则lastMessageTime设置为当前时间
            				if (empty($connection->lastMessageTime)) {
                				$connection->lastMessageTime = $time_now;
                				continue;
            				}
            				// 上次通讯时间间隔大于心跳间隔，则认为客户端已经下线，关闭连接
            				if ($time_now - $connection->lastMessageTime > 55) {
                				$connection->close();
            				}
	    			}
	    		});
		};

		$worker->onMessage = function ($con, $data) {
			global $equipment_number_map;
			$con->lastMessageTime = time();
			$data = json_decode($data, true);
			$cmd = isset($data['method']) ? $data['method'] : 'listen';
			// 设备号
			$equipment_number = isset($data['equipment_number']) ? $data['equipment_number'] : 0;
			// 项目id
			$product = isset($data['product']) ? $data['product'] : '';
			// 频道
			$channel = 'alpha_channel_' . $product;
			// 频道暂时不区分项目了
			$channel = 'alpha_channel';
			// 设备列表
			switch ($cmd) {
				// 需要监听某个频道
				case "listen":
					// 通过设备id获取链接id.
					$equipment_number_map[$equipment_number][$con->id] = $con;
					// 保存链接的设备ID.
					$con->equipment_number = isset($con->equipment_number) ? $con->equipment_number : array();
					// 保存这个设备在内个频道
					$con->chanenl_id[$equipment_number] = $channel;
					break;
					// 需要往某个频道发送消息
				case "send":
					// 往指定的频道发消息
					if(!is_string($data['message'])) $data['message']=json_encode($data['message']);
					ClientMange::publish($channel, array(
								'equipment_number' => $equipment_number,
								'message' => $data['message']
								));
					break;
			}
		};
		// 这里很重要，连接关闭时把连接从全局群组数据中删除，避免内存泄漏
		$worker->onClose = function ($con) use ($product) {
			global $equipment_number_map;
			// 遍历连接加入的所有群组，从group_con_map删除对应的数据
			if (isset($con->equipment_number)) {
				foreach ($con->equipment_number as $equipment_number) {
					unset($equipment_number_map[$equipment_number][$con->id]);
					if (empty($equipment_number_map[$equipment_number])) {
						unset($equipment_number_map[$equipment_number]);
					}
				}
			}

			$count = RedisMange::get('alpha_channel_connect')?RedisMange::get('alpha_channel_connect'):0;
			$count--;
			RedisMange::set('alpha_channel_connect',$count);
		};
		$worker->onConnect = function($connection)
		{
			$count = RedisMange::get('alpha_channel_connect')?RedisMange::get('alpha_channel_connect'):0;
			$count++;
			RedisMange::set('alpha_channel_connect',$count);
		};
		Worker::runAll();
	}


}

// 全局的保存链接的变量
$equipment_number_map = array();
