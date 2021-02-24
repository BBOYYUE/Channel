<?php


namespace Bboyyue\Channel\Worker;


class Client
{
    private int $connectId;
    private string $equipmentNumber;
    private array $equipmentNumberListenMap = [];
    private array $equipmentNumberSendMap = [];
    private array $equipmentNumberNormalMap = [];
    private string $lastMessageTime;

    public function __construct($id)
    {
        $this->connectId = $id;
    }

    public function getEquipmentNumberNormalMap():array
    {
        return $this->equipmentNumberNormalMap;
    }

    public function getEquipmentNumberListenMap():array
    {
        return $this->equipmentNumberListenMap;
    }

    public function getEquipmentNumberSendMap():array
    {
        return $this->equipmentNumberSendMap;
    }

    public function getConnectId(): int
    {
        return $this->connectId;
    }

    public function setLastMessageTime($val)
    {
        $this->lastMessageTime = $val;
    }

    public function setEquipmentNumberListenMap($val)
    {
        $this->equipmentNumberListenMap = [$val];
    }
    public function setEquipmentNumberSendMap($val)
    {
        $this->equipmentNumberSendMap = [$val];
    }
    public function setEquipmentNumberNormalMap($val)
    {
        if(!in_array($this->equipmentNumberNormalMap,$val)&&in_array($this->equipmentNumberSendMap,$val)&&in_array($this->equipmentNumberListenMap,$val)) {
            $this->equipmentNumberNormalMap = [$val];
        }
    }

    public function addEquipmentNumberListenMap($val)
    {
        if(!in_array($this->equipmentNumberListenMap,$val)) {
            $this->equipmentNumberListenMap[] = $val;
        }
    }
    public function addEquipmentNumberSendMap($val)
    {
        if(!in_array($this->equipmentNumberSendMap,$val)) {
            $this->equipmentNumberSendMap[] = $val;
        }
    }
    public function addEquipmentNumberNormalMap($val)
    {
        if(!in_array($this->equipmentNumberNormalMap,$val)&&in_array($this->equipmentNumberSendMap,$val)&&in_array($this->equipmentNumberListenMap,$val)) {
            $this->equipmentNumberNormalMap[] = $val;
        }
    }


    public function equipmentNumberMapHas($key): bool
    {
        if(in_array($this->equipmentNumberNormalMap,$key)||in_array($this->equipmentNumberListenMap,$key)||in_array($this->equipmentNumberSendMap,$key)){
            return true;
        }else{
            return false;
        }
    }

    public function delEquipmentNumberMap($val): void
    {
        if(!$this->equipmentNumberMapHas($val)) return;

        $this->delEquipmentNumberNormalMap($val);
        $this->delEquipmentNumberListenMap($val);
        $this->delEquipmentNumberSendMap($val);
    }
    public function delEquipmentNumberNormalMap($val)
    {
        if(in_array($this->equipmentNumberNormalMap,$val)){
            foreach ($this->equipmentNumberNormalMap as $k => $v){
                if($v == $val) unset($this->equipmentNumberSendMap[$k]);
            }
        }
    }
    public function delEquipmentNumberListenMap($val)
    {
        if(in_array($this->equipmentNumberListenMap,$val)){
            foreach ($this->equipmentNumberListenMap as $k => $v){
                if($v == $val) unset($this->equipmentNumberListenMap[$k]);
            }
        }
    }
    public function delEquipmentNumberSendMap($val)
    {
        if(in_array($this->equipmentNumberSendMap,$val)){
            foreach ($this->equipmentNumberSendMap as $k => $v){
                if($v == $val) unset($this->equipmentNumberSendMap[$k]);
            }
        }
    }
}