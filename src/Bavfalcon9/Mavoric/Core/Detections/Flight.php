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

use Bavfalcon9\Mavoric\Main;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Core\Utils\CheatIdentifiers;
use Bavfalcon9\Mavoric\Core\Utils\MathUtils;
use Bavfalcon9\Mavoric\Core\Utils\LevelUtils;
use Bavfalcon9\Mavoric\Core\Utils\Math\Facing;
use Bavfalcon9\Mavoric\events\MavoricEvent;
use Bavfalcon9\Mavoric\Events\player\PlayerMove;
use pocketmine\Player;
use pocketmine\block\{
    Slab, SnowLayer, Stair, Transparent
};
/* use pocketmine\math\Facing; uncomment when api 4.0.0 */

class Flight implements Detection {
    private $mavoric;
    private $plugin;
    private $checks = [];
    private $highest = 0;

    public function __construct(Mavoric $mavoric) {
        $this->plugin = $mavoric->getPlugin();
        $this->mavoric = $mavoric;
    }

    public function onEvent(MavoricEvent $event): void {
        if ($event instanceof PlayerMove) {
            $player = $event->getPlayer();
            $to = clone $event->getTo();
            $from = clone $event->getFrom();
            $distance = $to->distance($from);
            
            if ($player->getAllowFlight() === true) {
                return;
            }

            if (LevelUtils::getRelativeBlock(LevelUtils::getBlockWhere($player), Facing::UP)->getId() === 0) {
                $blockAtPlayer = LevelUtils::getBlockWhere($player);
                $blockBelow = LevelUtils::getRelativeBlock($blockAtPlayer, Facing::DOWN);

                // Should fix false alarms when traveling up steps
                if ($blockBelow instanceof Slab || $blockBelow instanceof Stair || $blockBelow instanceof SnowLayer) {
                    return;
                }
                // fix blantant flight
                if (MathUtils::getFallDistance($from, $to) === 0) {
                    // They aren't falling
                    if ($distance > 0.6) {
                        $event->sendAlert('Flight', "Illegal movement, moved in air without falling.");
                        $event->issueViolation(CheatIdentifiers::CODES['Flight']);
                        $event->cancel(false);
                        return;
                    }
                }

                // unnatrual falls, this is most likely a result of jetpack
                if (MathUtils::getFallDistance($from, $to) > 3.4952) {
                    $event->sendAlert('Flight', "Illegal movement, falling too quickly.");
                    $event->issueViolation(CheatIdentifiers::CODES['Flight']);
                    $event->cancel(false);
                    return;
                }

                // air jump
                if (LevelUtils::getRelativeBlock(LevelUtils::getBlockWhere($player), Facing::DOWN)->getId() === 0) {
                    $realtive = LevelUtils::getRelativeBlock(LevelUtils::getBlockWhere($player), Facing::DOWN);
                    if (LevelUtils::getRelativeBlock($realtive, FACING::DOWN)->getId() === 0) {
                        /*
                         * 0 because anything less than 0 means the fall distance was positive,
                         * and last time i checked you can't jump on air
                         */
                        if (MathUtils::getFallDistance($from, $to) <= 0) {
                            $event->sendAlert('Flight', "Illegal movement, jumped on air.");
                            $event->issueViolation(CheatIdentifiers::CODES['HighJump']);
                            $event->cancel(false);
                            return;
                        }
                    }
                }
            }
        }
    }

    public function isEnabled(): Bool {
        return true;
    }
}