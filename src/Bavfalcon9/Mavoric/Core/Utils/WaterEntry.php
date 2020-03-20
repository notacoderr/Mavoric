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

namespace Bavfalcon9\Mavoric\Core\Utils;

use pocketmine\Player;
use pocketmine\block\Block;
use pocketmine\block\StillWater;
use pocketmine\block\Water;
use pocketmine\block\WaterLily;

class WaterEntry {
    /** @var Player */
    private $player;
    /** @var int */
    private $entryTime;
    /** @var int */
    private $exitTime;

    public function __construct(Player $player) {
        $this->player = $player;
        $this->entryTime = microtime(true);
        $this->exitTime = -1;
    }

    /**
     * @return int - The time of exiting water.
     */
    public function setExited(): int {
        return $this->exitTime = microtime(true);
    }

    /**
     * @return int - the time of exiting the water.
     */
    public function getExitTime(): int {
        return $this->exitTime;
    }

    /**
     * @return int - the time of entering the water
     */
    public function getEntryTime(): int {
        return $this->entryTime;
    }

    /**
     * Gets the total time in water, if they are still in water, returns how long they have been in it.
     * @return int - the total time in the water.
     */
    public function getTimeInWater(): int {
        $time = $this->exitTime - $this->entryTime;
        
        return ($time < 0) ? microtime(true) - $this->entryTime : $time;
    }

    /** 
     * Check whether a block is a water block.
     * @param Block $block - Block to check
     * @return Bool
     */
    public static function isWater(Block $block): Bool {
        return ($block instanceof StillWater || $block instanceof Water || $block instanceof WaterLily);
    }
}