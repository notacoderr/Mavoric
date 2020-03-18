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
use pocketmine\math\Vector3;
use pocketmine\item\Item;
use pocketmine\block\Block;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\events\MavoricEvent;

/**
 * Called when a player breaks a block
 */
class PlayerBreakBlock extends MavoricEvent {
    /** @var Player */
    private $player;
    /** @var Block */
    private $block;
    /** @var int */
    private $timeTaken;

    public function __construct($e, Mavoric $mavoric, Player $player, Block $block, int $timeTaken) {
        parent::__construct($e, $mavoric, $player);
        $this->player = $player;
        $this->block = $block;
        $this->timeTaken = $timeTaken;
    }

    public function getItem(): ?Item {
        return $this->getUsedTool();
    }

    public function getUsedTool(): ?Item {
        $inventory = $this->player->getInventory();
        return $inventory->getItemInHand();
    }

    public function getDistance(): float {
        $pos1 = $this->player->getPosition() ?? new Vector3(0,0,0);
        $pos2 = $this->block->asVector3() ?? new Vector3(0,0,0);
        return $pos1->distance($pos2);
    }

    public function getBlock(): Block {
        return $this->block;
    }

    public function getTime(): int {
        return $this->timeTaken;
    }
}