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

namespace Bavfalcon9\Mavoric\Detections;

use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\events\MavoricEvent;
use Bavfalcon9\Mavoric\events\player\PlayerMove;
use pocketmine\Player;

use pocketmine\block\{
    StillWater, Water, WaterLily
};

class Jesus implements Detection {
    /** @var Mavoric */
    private $mavoric;

    public function __construct(Mavoric $mavoric) {
        $this->mavoric = $mavoric;
    }

    public function onEvent(MavoricEvent $event): void {
        if (!$event instanceof PlayerMove) {
            return;
        }

        $block_below = $event->getBlockNearPlayer(0, -1, 0);
        $block_above = $event->getBlockNearPlayer(0, 1, 0);

        if ($block_below instanceof StillWater || $block_below instanceof Water || $block_below instanceof WaterLily) {
           if ($block_above->getId() === 0) {
                $event->issueViolation(Mavoric::CHEATS['Jesus'], 1);
                $event->sendAlert('Jesus', 'Illegal movement, walked on water');
                return;
           } 
        }

        return;
    }

    public function isEnabled(): Bool {
        return false;
    }
}