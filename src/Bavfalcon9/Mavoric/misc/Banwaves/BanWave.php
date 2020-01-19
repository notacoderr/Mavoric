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
                'issuedAt' => null
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

        return $this->players[$name];
    }

    public function removePlayer(String $name): Bool {
        if (!isset($this->players[$name])) {
            return false;
        } else {
            unset($this->players[$name]);
            return true;
        }
    }

    public function getPlayers(): Array {
        return $this->players;
    }

    public function getPlayerCount(): int {
        return sizeof(array_keys($this->data['players']));
    }

    public function isIssued(): Bool {
        return $this->data['issued'];
    }

    public function setIssued(Bool $bool = false): Bool {
        $this->data['issued'] = $bool;

        if ($bool) {
            $this->data['issuedAt'] = time();
        }

        return $bool;
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