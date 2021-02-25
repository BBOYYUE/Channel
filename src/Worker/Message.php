<?php


namespace Bboyyue\Channel\Worker;




use Bboyyue\Channel\Exception\ErrorData;
use Bboyyue\Channel\Exception\ErrorEquipmentNumber;
use Bboyyue\Channel\Exception\ErrorMessage;
use Bboyyue\Channel\Exception\ErrorMethod;

class Message
{
    use MessageCheck;

    private string $equipmentNumber;
    private string $method;
    private int $tapType = 0;
    private Object $message;
    private string $channel = 'private';
    private array $data;


    public function __construct($data)
    {
        try {
            if(!$this->checkString($data)) throw new \Exception(' ');
            $data = json_decode($data);
            $equipmentNumber = $data->equipment_number;
            $method = $data->method;
            $message = $data->message;
            $this->setEquipmentNumber($equipmentNumber);
            $this->setMethod($method);
            $this->setMessage($message);
            $this->setData([
                'method'=>$this->method,
                'channel'=>$this->channel,
                'equipment_number'=>$this->equipmentNumber,
                'tapType' =>$this->tapType,
                'message' =>$this->message
            ]);

        } catch (ErrorEquipmentNumber $e){
            throw $e;
        } catch (ErrorMethod $e){
            throw $e;
        } catch (ErrorMessage $e){
            throw $e;
        } catch (\Exception $e) {
            throw new ErrorData($data);
        }


    }
    public function setEquipmentNumber($equipmentNumber){
        try {
            if(!$this->checkString($equipmentNumber)) throw new \Exception($equipmentNumber);
            $this->equipmentNumber = $equipmentNumber;
        }catch (\Exception $e){
            throw new ErrorEquipmentNumber($e->getMessage());
        }
    }

    public function setMethod($method)
    {
        try {
            if(!$this->checkString($method)||!in_array($method,['query','listen','close','send']))  throw new \Exception(' ');
            $this->method = $method;
        }catch (\Exception $e){
            throw new ErrorMethod($e->getMessage());
        }
    }

    public function setMessage($message)
    {

        try {
            if($message == ' '||is_null($message)) return $this->message = new messageData();
            if($this->checkString($message)){
                $message = json_decode($message);
            }

            $this->message = new messageData();
            foreach ($message as $key=>$val){
                $this->message->$key = $val;
            }
            $this->message->equipment_number = $this->equipmentNumber;

            if ($this->method === 'query') {
                $tapType =  $this->message->tapType;
                $this->setTapType($tapType);
            }

        }catch (\Exception $e){
            throw new ErrorMessage($e->getMessage());
        }
    }

    public function setTapType($tapType)
    {
        $this->tapType = $tapType;
    }

    public function setData($data){
        $this->data = $data;
    }

    public function addData($key,$val): array
    {
        $this->data[$key] = $val;
    }
    public function getData(): array
    {
        return $this->data;
    }
    public function getChannel(): string
    {
        return $this->channel;
    }
    public function getMessage(): string
    {
        return $this->message;
    }
    public function getMethod(): string
    {
        return $this->method;
    }
    public function getEquipmentNumber(): string
    {
        return $this->equipmentNumber;
    }
}

class MessageData{
    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}