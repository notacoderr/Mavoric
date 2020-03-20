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
use pocketmine\math\AxisAlignedBB;
use pocketmine\Player;

class Jesus implements Detection {
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

        // check blatant jesus
        $player = $event->getPlayer();


        // check not so blatant jesus
        $AABB = $player->getBoundingBox();
        
        if (WaterEntry::isWater($event->getBlocks()[0])) {
            $blockAt = $event->getBlocks()[0];
            $blockAbove = $event->getBlocks()[1];
            #$player->addActionBarMessage("§aIn water");
            var_dump($AABB->isVectorInside($blockAbove));
            if ($blockAt->collidesWithBB($AABB)) {
                // collision with water, check air.
                if ($blockAbove->getId() === 0 && $blockAbove->collidesWithBB($AABB)) {
                    // collision with air, they are inbetween water and air.
                    $distanceFromWater = $blockAt->distance($player->getPosition());

                    $player->addActionBarMessage("Distance from water block: $distanceFromWater");
                } 
            }
        } else {
            #$player->addActionBarMessage("§cNot in water");
        }
    }

    public function isEnabled(): Bool {
        return true;
    }
}