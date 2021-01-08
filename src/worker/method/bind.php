<?php


namespace Bboyyue\Channel\worker\method;


use Bboyyue\Channel\worker\event\onBegin;

class bind extends onBegin
{
    public function run($data,$connection){
        global $equipment_number_map;

        $equipment_number = $data['equipment_number'];
        if(!isset($equipment_number_map[$equipment_number])||!isset($equipment_number_map[$equipment_number][$connection->id])) {
            $equipment_number_map[$equipment_number][$connection->id] = $connection;
        }
        return $data;
    }
}