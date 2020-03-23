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

namespace Bavfalcon9\Mavoric\Tasks;
use pocketmine\scheduler\Task;

use Bavfalcon9\Mavoric\Core\Banwaves\BanWave;
use Bavfalcon9\Mavoric\Mavoric;

class BanWaveTask extends Task {
    /** @var BanWave */
    private $banWave;
    /** @var Mavoric */
    private $mavoric;
    /** @var Array */
    private $bans = [];
    /** @var int */
    private $index = 0;
    
    public function __construct(Mavoric $mavoric, BanWave $wave) {
        $this->mavoric = $mavoric;
        $this->banWave = $wave;
        $players = $wave->getPlayers();
        foreach ($players as $player=>$banData) {
            array_push($this->bans, [
                'name' => $player,
                'banData' => $banData
            ]);
        }   
    }

    public function onRun(int $tick) {
        if (!isset($this->bans[$this->index])) {
            $this->getHandler()->cancel();
        } else {
            $wave = $this->banWave;
            $player = $this->bans[$this->index]['name'];
            $banData = $this->bans[$this->index]['banData'];
            $banList = $this->getServer()->getNameBans();
            $banList->addBan($player, '§4'.$banData['reason'] . ' | Wave ' . $wave->getNumber(), null, 'Mavoric');
            if ($this->getServer()->getPlayer($player)) {
                $this->mavoric->getFlag($this->getServer()->getPlayer($player))->clearViolations();
                $this->getServer()->getPlayer($player)->close('', $banData['reason'] . ' | Wave ' . $wave->getNumber());
            }
            $this->getServer()->broadcastMessage('§4' . $player . ' banned for: ' . $banData['reason'] . ' | Wave ' . $wave->getNumber() . ' until forever');
            $this->index++;
        }
    }

    private function getServer() {
        return $this->mavoric->getServer();
    }
}