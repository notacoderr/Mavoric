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
use Bavfalcon9\Mavoric\Core\Utils\MathUtils;
use Bavfalcon9\Mavoric\Core\Utils\LevelUtils;
use Bavfalcon9\Mavoric\Core\Utils\Math\Facing;
use Bavfalcon9\Mavoric\events\MavoricEvent;
use Bavfalcon9\Mavoric\Events\player\PlayerMove;
use pocketmine\Player;
/* use pocketmine\math\Facing; uncomment when api 4.0.0 */

class HighJump implements Detection {
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

            if ($player->getGamemode() === 1) {
                return;
            }

            if ($player->getGamemode() === 3) {
                return;
            }

            if ($player->getAllowFlight() === true) {
                return;
            }

            if (LevelUtils::getRelativeBlock(LevelUtils::getBlockWhere($player), Facing::UP)->getId() === 0) {
                if (MathUtils::getFallDistance($from, $to) < -0.6754) {
                    $event->sendAlert('HighJump', "Illegal jump, jumped too high.");
                    $event->issueViolation(Mavoric::CHEATS['HighJump']);
                    $event->cancel(false);
                    return;
                }
            }
        }
    }

    public function isEnabled(): Bool {
        return true;
    }
}