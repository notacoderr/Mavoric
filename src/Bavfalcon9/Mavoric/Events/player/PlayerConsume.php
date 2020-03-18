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
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\events\MavoricEvent;

/**
 * Called when a player consumes an item.
 */
class PlayerConsume extends MavoricEvent {
    /** @var Player */
    private $player;
    /** @var Item */
    private $item;

    public function __construct($e, Mavoric $mavoric, Player $player, Item $item) {
        parent::__construct($e, $mavoric, $player);
        $this->player = $player;
        $this->item = $item;
    }

    public function getItem(): ?Item {
        return $this->item;
    }

    public function getTime(): int {
        // TO DO
        return -1;
    }
}