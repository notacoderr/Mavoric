<?php

class Analytics {
    private $mavoric;
    private $bans = [];
    private $kicks = [];
    private $detections = [];
    private $start;


    public __construct($mavoric) {
        $this->mavoric = $mavoric;
        $this->start = time();
    }

    public function addBan(String $name, String $reason) {
        array_push($this->bans, [$name, $reason]);
        return true;
    }

    public function addKick(String $name, String $reason) {
        array_push($this->kicks, [$name, $reason]);
        return true;
    }

    public function addDetection(String $name, String $event) {
        array_push($this->detections, [$name, $event, time()]);
        return true;
    }

    public function getStats() : Array {
        $stats = [
            "bans" => sizeof($this->bans),
            "kicks" => sizeof($this->kicks),
            "detections" => sizeof($this->detections)
        ];

        return $stats;
    }
}