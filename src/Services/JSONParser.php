<?php

namespace App\Services;

class JSONParser
{
    var $path;
    var $timestamps = [];
    var $occupancies = [];
    var $datetimes = [];
    var $total_ins = [];
    var $total_outs = [];

    public function setPath($newPath)
    {
        $this->path = $newPath;
    }

    public function parse()
    {
        $data = json_decode(file_get_contents(__DIR__ . $this->path), true);

        foreach ($data['data'] as $entry) {

            // not rly necessary
            $timestamp = new \DateTime($entry['datetime']['date']);
            $time = $timestamp->format("H:i");

            array_push($this->datetimes, $timestamp);
            array_push($this->timestamps, $time);
            array_push($this->occupancies, $entry['occupancy']);
            array_push($this->total_ins, $entry['total_in']);
            array_push($this->total_outs, $entry['total_out']);
        }
    }

    public function getTimeStamps()
    {
        return $this->timestamps;
    }

    public function getOccupancies()
    {
        return $this->occupancies;
    }

    public function getDateTimes()
    {
        return $this->datetimes;
    }

    public function getTotalIns()
    {
        return $this->total_ins;
    }

    public function getTotalOuts()
    {
        return $this->total_outs;
    }
}