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
use Bavfalcon9\Mavoric\events\{
    MavoricEvent,
    player\PlayerMove
};

use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;

use pocketmine\event\player\PlayerMoveEvent;

// to complete....
class Speed implements Detection {
    /** @var Mavoric */
    private $mavoric;
    /** @var Array */
    private $timings = [];
    /** @var int */
    private $lastTick = 0;

    public function __construct(Mavoric $mavoric) {
        $this->mavoric = $mavoric;
    }

    public function onEvent(MavoricEvent $event): void {
        /** @var PlayerMove */
        if ($event instanceof PlayerMove) {
            $player = $event->getPlayer();
            $to = $event->getTo();
            $from = $event->getFrom();
            $distance = abs($to->distance($from));
            $allowed = 2;
            if ($player->isCreative() || $player->isSpectator()) {
                return;
            }
            if ($player->getEffect(1) !== null) {
                if ($player->getEffect(1)->getEffectLevel() === 1) {
                    $allowed = 2.3;
                } else {
                    $allowed = $player->getEffect(1)->getEffectLevel() * 1.6;
                }
            }
            // fix falling, flying and knockback
            if ($player->getAllowFlight()) {
                $allowed = 4;
            }

            if (($from->y > $to->y) && $distance < 6) {
                return;
            }
            if (($from->y < $to->y)  && $distance < 6) {
                return;
            }
            if ($distance >= $allowed) {
                if ($player->getPing() >= 300) {
                    $distance = round($distance, 2);
                    #$player->sendMessage('Evaluation failed. Shard: ' . rand(1, 99999));
                    #$event->issueViolation(Mavoric::CHEATS['Speed']);
                    return;
                } else {
                    $distance = round($distance, 2);
                    $event->sendAlert('Speed', "Illegal movement, Moved {$distance} blocks too quickly.");
                    $event->issueViolation(Mavoric::CHEATS['Speed']);
                    return;
                }
            }
        }
    }

    public function isEnabled(): Bool {
        return false;
    }
}
