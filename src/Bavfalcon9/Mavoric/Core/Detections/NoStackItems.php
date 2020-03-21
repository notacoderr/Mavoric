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

namespace Bavfalcon9\Mavoric\Core\Detections;

use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\events\MavoricEvent;
use Bavfalcon9\Mavoric\events\packet\PacketRecieve;
use Bavfalcon9\Mavoric\events\player\InventoryTransaction;
use Bavfalcon9\Mavoric\Core\Utils\CheatIdentifiers;
use pocketmine\Player;
use pocketmine\inventory\ArmorInventory;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;

class NoStackItems implements Detection {
    /** @var Mavoric */
    private $mavoric;

    public function __construct(Mavoric $mavoric) {
        $this->mavoric = $mavoric;
    }

    public function onEvent(MavoricEvent $event): void {
        if (!$event instanceof InventoryTransaction) {
            return;
        }

        if ($event->hasIllegalStackedItem()) {
            $event->issueViolation(CheatIdentifiers::CODES['NoStackItems'], 50);
            $event->sendAlert('NoStackItems', 'Illegal inventory transaction, Stacked non-stackable item.');
        }
    }

    public function isEnabled(): Bool {
        return true;
    }
}