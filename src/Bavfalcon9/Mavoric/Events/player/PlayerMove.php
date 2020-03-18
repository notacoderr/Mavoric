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

namespace Bavfalcon9\Mavoric\events\player;

use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\level\Position;
use pocketmine\level\Location;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\events\MavoricEvent;

class PlayerMove extends MavoricEvent {
    /** @var Player */
    private $player;
    /** @var Position */
    private $from;
    /** @var Position */
    private $to;

    public function __construct($e, Mavoric $mavoric, Player $player, Position $from, Position $to) {
        parent::__construct($e, $mavoric, $player);
        $this->player = $player;
        $this->from = $from;
        $this->to = $to;
    }

    public function isTeleport(): Bool {
        return false;
    }

    public function isMoved(): Bool {
        return !$this->getDistance() === 0;
    }

    public function getDistance(): float {
        return $this->from->distance($this->to);
    }

    public function getFrom(): ?Vector3 {
        return $this->from;
    } 

    public function getTo(): ?Vector3 {
        return $this->to;
    }

    public function getNextAirBlock(): Vector3 {
        $level = $this->player->getLevel();
        $pos = $this->player->getPosition();
        $max = 256;

        if ($pos->y >= $max) {
            return new Vector3($pos->x, $max, $pos->z);
        }

        for ($test = $pos->y; $test < $max; $test++) {
            $block = $level->getBlockAt($pos->x, $test + 1, $pos->z);
            if ($block->getId() === 0) {
                return new Vector3($pos->x, $test + 1, $pos->z);
            }
        }

        return $pos;
    }

    public function getBlockNearPlayer(int $x = 0, int $y = 0, int $z = 0): ?Block {
        $near = new Vector3($this->player->x + $x, $this->player->y + $y, $this->player->z + $z);
        return $this->player->getLevel()->getBlock($near);
    }

    public function getBlocks(): Array {
        $player = $this->player;
        return [
            $player->getLevel()->getBlockAt($player->x, $player->y, $player->z),
            $player->getLevel()->getBlockAt($player->x, $player->y + 1, $player->z)
        ];
    }
}