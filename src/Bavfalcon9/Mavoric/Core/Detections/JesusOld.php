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
use Bavfalcon9\Mavoric\events\player\PlayerMove;
use Bavfalcon9\Mavoric\Core\Utils\WaterEntry;
use pocketmine\Player;

class JesusOld implements Detection {
    /** @var Mavoric */
    private $mavoric;
    /** @var WaterEntries[] */

    public function __construct(Mavoric $mavoric) {
        $this->mavoric = $mavoric;
    }

    public function onEvent(MavoricEvent $event): void {
        if (!$event instanceof PlayerMove) {
            return;
        }

        /** @var Player */
        $player = $event->getPlayer();
        /** @var Block */
        $blockAt = $event->getBlockNearPlayer(0, 0, 0);
        /** @var Block */
        $blockBelow = $event->getBlockNearPlayer(0, -1, 0);
        /** @var Block */
        $blockAbove = $event->getBlockNearPlayer(0, 1, 0);

        
        if (WaterEntry::isWater($blockBelow)) {
           if (!isset($this->entries[$player->getName()])) {
               $this->entries[$player->getName()] = new WaterEntry($player);
           }

           $entry = &$this->entries[$player->getName()];

           if ($entry->getExitTime() !== -1) {
               $this->entries[$player->getName()] = new WaterEntry($player);
           }
           
           if ($blockAbove->getId() === 0 && WaterEntry::isWater($blockBelow) && $entry->getTimeInWater() > 2) {
                $event->sendAlert('Jesus', "Illegal movement, walking on water for {$entry->getTimeInWater()} seconds.");
                $event->issueViolation(Mavoric::CHEATS['Jesus']);
                return;
           }
        } else {
            if (isset($this->entries[$player->getName()])) {
                $entry = &$this->entries[$player->getName()];

                if ($blockAt->getId() === 0 && $blockAbove->getId() === 0) {
                    $entry->setExited();
                }
            }
        }

        return;
    }

    public function isEnabled(): Bool {
        return true;
    }
}