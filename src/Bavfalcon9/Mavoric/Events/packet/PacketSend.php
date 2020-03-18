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

namespace Bavfalcon9\Mavoric\events\packet;

use pocketmine\Player;
use pocketmine\network\mcpe\protocol\DataPacket;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\events\MavoricEvent;

class PacketSend extends MavoricEvent {
    /** @var DataPacket */
    private $packet;
    /** @var Player */
    private $player;
    /** @var Bool */
    private $inBatch;

    public function __construct($e, Mavoric $mavoric, Player $player, DataPacket $packet, Bool $inBatch = false) {
        parent::__construct($e, $mavoric, $player);
        $this->packet = $packet;
    }

    public function isPartOfBatch(): Bool {
        return $this->inBatch;
    }

    public function getPacket(): DataPacket {
        return $this->packet;
    }
}