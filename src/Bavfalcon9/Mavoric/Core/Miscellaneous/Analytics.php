<?php
/***
 *      __  __                       _      
 *     |  \/  |                     (_)     
 *     | \  / | __ ___   _____  _ __ _  ___ 
 *     | |\/| |/ _` \ \ / / _ \| '__| |/ __|
 *     | |  | | (_| |\ V / (_) | |  | | (__ 
 *     |_|  |_|\__,_| \_/ \___/|_|  |_|\___|
 *                                          
 *   THIS CODE IS TO NOT BE REDISTRUBUTED
 *   @author MavoricAC
 *   @copyright Everything is copyrighted to their respective owners.
 *   @link https://github.com/Olybear9/Mavoric                                  
 */
class Analytics {
    private $mavoric;
    private $bans = [];
    private $kicks = [];
    private $detections = [];
    private $start;


    public function __construct($mavoric) {
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