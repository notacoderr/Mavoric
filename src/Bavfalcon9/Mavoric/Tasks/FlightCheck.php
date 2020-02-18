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
use Bavfalcon9\Mavoric\Mavoric;

class FlightCheck extends Task {
    private $mav;
    private $warned = [];

    public function __construct(Mavoric $mavoric) {
        $this->mav = $mavoric;
    }

    public function onRun(int $tick) {
        // Do players
        // Add per cheat violation
        $players = $this->mav->getPlugin()->getServer()->getOnlinePlayers();
        foreach ($players as $player) {
            $flag = $this->mav->getFlag($player);
            $top = $flag->getMostViolations();

            if ($flag->getTotalViolations() >= 15) {
                $reason = $this->mav->getCheat($flag->getMostViolations());
                $flag->clearViolations();
                if ($top === Mavoric::NoClip) return $this->mav->kick($player, $reason);
                $this->mav->ban($player, $reason);
                return true;
            }

            if ($top !== -1) {
                $reason = $this->mav->getCheat($flag->getMostViolations());
                $count = $flag->getViolations($top);

                if ($flag->getViolations($top) >= 10) {
                    $flag->clearViolations();
                    if ($top === Mavoric::NoClip) return $this->mav->kick($player, $reason);
                    $this->mav->ban($player, $reason);
                    return true;
                } 
                if ($flag->getViolations($top) >= 4) {
                    if ($this->mav->hasTaskFor($player)) return false;
                    $flag->clearViolations();
                    //$this->mav->startTask($player, 90);
                    return true;
                }
            }

        }
    }
}