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
use Bavfalcon9\Mavoric\Core\Utils\LevelUtils;
use Bavfalcon9\Mavoric\Core\Utils\Math\Facing;
use pocketmine\math\AxisAlignedBB;
use pocketmine\Player;

class Jesus implements Detection {
    /** @var Mavoric */
    private $mavoric;
    /** @var WaterEntries[] */
    private $entries;

    public function __construct(Mavoric $mavoric) {
        $this->mavoric = $mavoric;
        $this->entries = [];
    }

    public function onEvent(MavoricEvent $event): void {
        if (!$event instanceof PlayerMove) {
            return;
        }

        // check blatant jesus
        $player = $event->getPlayer();


        // check not so blatant jesus
        $AABB = $player->getBoundingBox();
        
        if (WaterEntry::isWater($event->getBlocks()[0])) {
            // we can assume this is a water entry
            if (!isset($this->entries[$player->getName()])) {
                $this->entries[$player->getName()] = [
                    "entry" => new WaterEntry($player),
                    "exits" => 0
                ];
            } else {
                if ($this->entries[$player->getName()]['entry']->getExitTime() + 1 >= microtime(true)) {
                    $this->entries[$player->getName()] = [
                        "entry" => new WaterEntry($player),
                        "exits" => 0
                    ];
                }
            }

            $entry = &$this->entries[$player->getName()];

            $blockAt = $event->getBlocks()[0];
            $blockAbove = $event->getBlocks()[1];
            $relative = LevelUtils::getRelativeBlock($blockAbove, FACING::UP);
            $aboveAABB = new AxisAlignedBB(
                $blockAbove->x,
                $blockAbove->y,
                $blockAbove->z,
                $blockAbove->x + 1,
                $blockAbove->y + 1,
                $blockAbove->z + 1
            );
        }
    }

    public function isEnabled(): Bool {
        return false;
    }
}