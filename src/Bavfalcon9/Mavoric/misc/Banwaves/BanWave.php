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

namespace Bavfalcon9\Mavoric\misc\Banwaves;

class BanWave {
    /** @var Array */
    private $data;
    /** @var String */
    private $path;
    /** @var Array */
    private $players = [];

    public function __construct(String $jsonData, String $path, Bool $isNew = true) {
        $this->path = $path;
        if ($isNew) {
            $this->data = [
                'players' => [],
                'issued' => false,
                'issuedAt' => null,
                'issuedAtDate' => null
            ];
        } else {
            $this->data = json_decode($jsonData, true);
            $this->players = $this->data['players'];
        }
    }

    public function getNumber(): int {
        return (int) explode('wave-', str_replace('.json', '', $this->path))[1];
    }

    public function addPlayer(String $name, String $reason, Array $cheats, int $count): Array {
        $time = date_create();

        if (isset($this->players[$name])) {
            $cheats = array_merge($this->players[$name]['cheats'], $cheats);
            $count += $this->players[$name]['VC'];
            $time = $this->players[$name]['time'];
        }
        $this->players[$name] = [
            "cheats" => $cheats,
            "reason" => $reason,
            "VC" => $count,
            "time" => $time
        ];
        $this->save();
        return $this->players[$name];
    }

    public function removePlayer(String $name): Bool {
        if (!isset($this->players[$name])) {
            return false;
        } else {
            unset($this->players[$name]);
            $this->save();
            return true;
        }
    }

    public function getPlayers(): Array {
        return $this->players;
    }

    public function getPlayerCount(): int {
        return sizeof(array_keys($this->data['players']));
    }

    public function hasPlayer(String $name): Bool {
        return isset($this->players[$name]);
    }

    public function isIssued(): Bool {
        if ($this->data === null || $this->data['issued'] === null) return false;
        return $this->data['issued'];
    }

    public function setIssued(Bool $bool = false): Bool {
        $this->data['issued'] = $bool;

        if ($bool) {
            $this->data['issuedAt'] = time();
            $this->data['issuedAtDate'] = date_create();
        }

        return $bool;
    }
    public function issuedAt(): ?int {
        return $this->data['issuedAt'];
    }

    public function issuedAtDate(): String {
        if (!isset($this->data['issuedAtDate'])) {
            return 'Invalid';
        }
        if ($this->data['issuedAtDate'] instanceof DateTime) {
            return $this->data['issuedAtDate']->format('Y-m-d H:i:s');
        }
        if ($this->data['issuedAtDate'] instanceof String) {
            return $this->data['issuedAtDate'];
        }
        if (is_array($this->data['issuedAtDate'])) {
            return $this->data['issuedAtDate']['date'] . ' (' .$this->data['issuedAtDate']['timezone'].')';
        }

        return 'Invalid';
    }

    public function save(): Bool {
        try {
            $this->data['players'] = $this->players;
            file_put_contents($this->path, json_encode($this->data));
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }
}