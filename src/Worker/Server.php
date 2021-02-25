<?php
namespace Bboyyue\Channel\Worker;

use Workerman\Worker;

class Server
{
    private array $channel;
    private string $serverIp;
    private string $serverPort;
    private string $websocketIp;
    private string $websocketPort;
    private int $websocketProcessCount;
    public static array $equipmentNumberMap = [];

    public function __construct()
    {
        $this->channel = ['public'];
        $this->serverIp = "127.0.0.1";
        $this->serverPort = "2206";
        $this->websocketIp = "0.0.0.0";
        $this->websocketPort = "8086";
        $this->websocketProcessCount = 1;
    }

    public function getServerAddress(): array
    {
        return [$this->serverIp,$this->serverPort];
    }

    public function getWebsocketAddress(): string
    {
        return "websocket://".$this->websocketIp.":".$this->websocketPort;
    }
    public function getChannel():array
    {
        return $this->channel;
    }
    public function getEquipmentNumberMap(): array
    {
        return Server::$equipmentNumberMap;
    }

    public function addEquipmentNumberMap($key,$val)
    {
        if(isset($this->equipmentNumberMap[$key])) {
            if(!in_array($val,$this->equipmentNumberMap[$key])) {
                $this->equipmentNumberMap[$key][] = $val;
            }
        }else{
            $this->setEquipmentNumberMap($key,$val);
        }
    }

    public function setEquipmentNumberMap($key,$val)
    {
        Server::$equipmentNumberMap[$key] = [$val];
    }

    /*
     * 第一个foreach 遍历所有的设备号
     * 第二个foreach 遍历设备号关联的设备.
     */
    public function delEquipmentNumberMap($id,$connection):void
    {
        $equipmentNumberList = array_keys(Server::$equipmentNumberMap);
        if(in_array($id,$equipmentNumberList)) {
            foreach (Server::$equipmentNumberMap as $key => $val) {
                $connectIdList = array_keys($val);
                if(in_array($id,$connectIdList)) {
                    unset(Server::$equipmentNumberMap[$key][$id]);
                    if(Server::$equipmentNumberMap[$key] == []) {
                        foreach ($connection->worker->connections as $con) {
                            $client = $con->client;
                            $client->delEquipmentNumberMap($key);
                        }
                    }
                }
            }
        }
    }
}
