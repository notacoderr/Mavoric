<?php
/***
 *      __  __                       _      
 *     |  \/  |                     (_)     
 *     | \  / | __ ___   _____  _ __ _  ___ 
 *     | |\/| |/ _` \ \ / / _ \| '__| |/ __|
 *     | |  | | (_| |\ V / (_) | |  | | (__ 
 *     |_|  |_|\__,_| \_/ \___/|_|  |_|\___|
 *                                          
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  @author Bavfalcon9
 *  @link https://github.com/Olybear9/Mavoric                                  
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
        if (empty($this->raw)) {
            $this->time = time(),
            $this->accuracy = 0;
            $this->percentile = 0;
            $this->confirmed = false;
            $this->violations = [];
            $this->raw = [
                'violations' => [],
                'moderator' => false,
                'pecentile' => 0
            ];
        } else {
            $this->time = $this->raw['time'];
            $this->accuracy = 0;
            $this->percentile = $this->raw['percentile'];
            $this->confirmed = (!$this->raw['moderator']) ? false : true;
            $this->violations = count($this->raw->violations);
            
            foreach ($this->raw['events'] as $event) {
                array_push($this->events, new BanEventData($event));
            }
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

        $JSON = [
            "time" => time(),
            "accuracy" => 0,
            "percentile" => $this->getPercentile(),
            "confirmed" => $this->confirmed,
            "violations" => (!$this->raw['violations']) ? [] : $this->raw['violations'],
            "events" => [],
            "moderator" => $this->['moderator']
        ];

        return json_encode($JSON);
    }

    public function addViolation(Float $violation) {
        $this->raw['violations'] = $violation;
        return true;
    }

    public function addEvent(BanEventData $event) {
        array_push($this->events, $event);
    }

    public function newEvent($event) {
        
    }

    public function setEvents(Array $events) {
        $this->events = $events;
    }

    public function setPercentile(float $val) {
        $this->percentile = $val;
        return $val;
    }

    public function setModerator(String $mod) {
        $this->raw['moderator'] = $mod;
    }
}