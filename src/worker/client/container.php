<?php


namespace Bboyyue\Channel\worker\client;

use Bboyyue\Channel\worker\config;
use Bboyyue\Channel\worker\event\forTapType;
use Bboyyue\Channel\worker\event\onBegin;
use Bboyyue\Channel\worker\event\onEnd;
use Bboyyue\Channel\worker\exception\configException;
use Bboyyue\Channel\worker\exception\containerException;

class container
{
    protected $onBegin;
    protected $onEnd;
    protected $forTapType;
    protected $data;
    protected $message;
    protected $method;
    protected $channel = 'channel';
    protected $equipment_number;
    protected $tapType;
    protected $errorMsg;

    public function __construct($data){
        $this->data = $data;
        $this->checkData();
    }
    public function checkData()
    {
        try {
            if ($this->checkString($this->data)) {
                $this->data = json_decode($this->data);
            }
            if ($this->checkString($this->data->method)) {
                $this->method = $this->data->method;
            }

            if ($this->checkString($this->data->equipment_number)) {
                $this->equipment_number = $this->data->equipment_number;
            }

            if ($message = $this->checkJson($this->data->message)) {
                $this->message = $message;
            }

            if (isset($this->message->tapType)&&intval($this->message->tapType) > 0) {
                $this->tapType = $this->message->tapType;
            }

            $this->data = [
                'method'=>$this->method,
                'channel'=>'channel',
                'equipment_number'=>$this->equipment_number,
                'tapType' =>$this->tapType,
                'message' =>$this->message
            ];

        }catch (\Exception $e){
            $this->errorMsg = $e->getMessage();
        }
    }

    protected function checkString($data){
        try {
            if (is_string($data)||is_int($data)) return true;
            else throw new \Exception("数据格式不正确!".$data);
        }catch (\Exception $e){
            $this->errorMsg = $e->getMessage();
        }
    }

    protected function checkJson($data){
        try {
            if(is_string($data)&&!empty($data)) $data = json_decode($data);
            elseif(is_object($data)) $data = $data;
            else $data = '';

            return $data;
        }catch (\Exception $e){
            $this->errorMsg = "message 数据格式不正确!";
        }
    }
    public function getEquipmentNumber()
    {
        return $this->data['equipment_number'];
    }
    public function onBegin($connection)
    {
        try {
            foreach ($this->onBegin as $key => $val) {
                $obj = new $val;
                if ($obj instanceof onBegin) {
                    $this->data = $obj->run($this->data,$connection);
                    if ($error = $obj->getErrorMsg()) $this->errorMsg = $error;
                }else{
                    throw new containerException("不符合规范的 onBegin 事件类" . $val);
                }
            }
        }catch (\Exception $e){
            $this->errorMsg = $e->getMessage();
        }
    }

    public function onEnd($connection)
    {
        try {
            foreach ($this->onEnd as $key => $val) {
                $obj = new $val;
                if ($obj instanceof onEnd) {
                    $this->data = $obj->run($this->data,$connection);
                    if ($error = $obj->getErrorMsg()) $this->errorMsg = $error;
                } else {
                    throw new containerException("不符合规范的 onEnd 事件类" . $val);
                }
            }
        }catch (\Exception $e){
            $this->errorMsg = $e->getMessage();
        }
    }
    public function forTapType($connection)
    {
        try {
            if ($this->tapType) {
                if (isset($this->forTapType[$this->tapType])) {
                    $val = $this->forTapType[$this->tapType];
                    $obj = new $val;
                } else {
                    throw new containerException("无效的 tapType " . $this->tapType);
                }
                if ($obj instanceof forTapType) {
                    $this->data = $obj->run($this->data,$connection);
                    if ($error = $obj->getErrorMsg()) $this->errorMsg = $error;
                }else{
                    throw new containerException("不符合规范的 tapType 事件类" . $this->tapType);
                }
            }
        }catch (\Exception $e){
            $this->errorMsg = $e->getMessage();
        }
    }

    public function getData()
    {
        return $this->data;
    }

    public static function make(config $config,$data):container
    {
        $container = new container($data);

        try {
            if ($container->method && $config->checkMethod($container->method)) {
                $config->setMethod($container->method);
                $container->onBegin = $config->getOnBegin();
                $container->forTapType = $config->getForTapType();
                $container->onEnd = $config->getOnEnd();
            } else {
                throw new configException('方法不存在');
            }
            if ($config->getErrorMsg()) throw new configException($config->getErrorMsg());
        }catch (\Exception $e){
            if($e instanceof configException) $config->setError($e->getMessage());
            $container->errorMsg = $e->getMessage();
        }
        return $container;
    }

    public function getErrorMsg()
    {
        return $this->errorMsg;
    }
}