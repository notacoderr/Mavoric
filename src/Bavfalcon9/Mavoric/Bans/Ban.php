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

namespace Bavfalcon9\Mavoric\Bans;

class Ban {
    /** @var String - Raw ban data*/
    private $raw;
    private $time;
    private $accuracy;
    private $percentile;
    private $confirmed;
    private $events;
    private $violations;
    private $new = false;

    public function __construct($banData) {
        $this->raw = $banData;
        $this->constuctData();
    }

    private function constructData() {
        if (empty($this->raw)) return $this->new = true;
        $this->time = $this->raw['time'];
        $this->accuracy = 0;
        $this->percentile = $this->raw['percentile'];
        $this->confirmed = (!$this->raw['moderator']) ? false : true;
        $this->violations = count($this->raw->violations);
        
        foreach ($this->raw['events'] as $event) {
            array_push($this->events, new BanEventData($event));
        }
    }

    public function save(String $directory): ?Bool {
        $data = $this->getJSON();
        try {
            file_put_contents($directory, $data);
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function getTime(): ?float {
        return $this->time;
    }

    public function getAccuracy(): ?float {
        return $this->accuracy;
    }

    public function getPercentile(): ?float {
        return $this->percentile;
    }

    public function getEvents(): ?Array {
        return $this->events;
    }

    public function getJSON(): ?String {
        /**
         * Why did i make this a thing :cccc
         */
    }

    public function addEvent(BanEventData $event) {
        array_push($this->events, $event);
    }

    public function newEvent($event) {
        
    }

    public function setEvents(Array $events) {
        $this->events = $events;
    }
}