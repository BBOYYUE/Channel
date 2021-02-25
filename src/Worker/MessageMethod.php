<?php


namespace Bboyyue\Channel\Worker;

use Channel\Client as ClientMange;
use ErrorEquipmentNumber;
use MissEquipmentNumber;

class MessageMethod
{
    public static function run($message,$server,$connection){
        switch ($message->getMethod()){
            case "query":
                MessageMethod::query($message,$connection);
                break;
            case "listen":
                MessageMethod::listen($message,$server,$connection);
                break;
            case "send":
                MessageMethod::send($message, $server, $connection);
                break;
            case "close":
                MessageMethod::close($message,$server,$connection);
                break;
        }
    }
    public static function query($message,$connection)
    {

    }

    /**
     * @param $message
     * @param $server
     * @param $connection
     */
    public static function send($message,$server,$connection)
    {
        try {
            $client = $connection->client;
            $equipmentNumber = $message->getEquipmentNumber();
            if (isset(Server::$equipmentNumberMap[$equipmentNumber])) {

                /*
                 * 如果是受保护的频道,那么消息会转发给所有可以接收消息的人.
                 * 如果是公开的会转发给所有人
                 * 私有的话会转发给指定的设备号.
                 */
                if ($message->getMethod() == 'protected'){
                    $message->addData('clientList',$client->getEquipmentNumberSendMap());
                }

                ClientMange::publish($message->getChannel(), $message->getData());
            } else {
                throw new MissEquipmentNumber($equipmentNumber);
            }

            // add 方法会自动验证是否满足条件
            $client->addEquipmentNumberSendMap($equipmentNumber);
            $client->addEquipmentNumberNormalMap($equipmentNumber);

        }catch (MissEquipmentNumber $e){
            $connection->send($e->getMsg());
        }catch (ErrorEquipmentNumber $e){
            $connection->send($e->getMsg());
        }
    }

    /**
     * @param $message
     * @param $server
     * @param $connection
     */
    public static function listen($message,$server,$connection)
    {
        try {
            $client = $connection->client;
            $equipmentNumber = $message->getEquipmentNumber();

            /*
             * 如果是私有的,那么只有当前连接能够监听这个设备号.
             * 如果是公开和受保护的,则没有限制.
             */

            if ($message->getMethod() == 'private') {
                if(isset(Server::$equipmentNumberMap[$equipmentNumber])){
                    $clients = array_values($server->getEquipmentNumberMap()[$equipmentNumber]);
                    foreach ($clients as $client){
                        $client->close();
                    }
                }
                $server->setEquipmentNumberMap($equipmentNumber,[$client->getConnectId() => $connection]);
                $client->setEquipmentNumberListenMap($equipmentNumber);
                $client->setEquipmentNumberNormalMap($equipmentNumber);
            }else{
                $server->addEquipmentNumberMap($equipmentNumber, [$client->getConnectId() => $connection]);
                $client->addEquipmentNumberListenMap($equipmentNumber);
                $client->addEquipmentNumberNormalMap($equipmentNumber);
            }
        }catch (ErrorEquipmentNumber $e){
            $connection->send($e->getMsg());
        }
    }
    public static function close($message,$server,$connection)
    {
        $connection->close();
    }
}