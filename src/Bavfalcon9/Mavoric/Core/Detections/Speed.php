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
use Bavfalcon9\Mavoric\events\MavoricEvent;
use Bavfalcon9\Mavoric\events\player\PlayerMove;
use Bavfalcon9\Mavoric\Events\packet\PacketRecieve;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\event\player\PlayerMoveEvent;

class Speed implements Detection {
    /** @var Mavoric */
    private $mavoric;
    /** @var String=>int[] */
    private $lastTime;

    public function __construct(Mavoric $mavoric) {
        $this->mavoric = $mavoric;
        $this->lastTime = [];
    }

    public function onEvent(MavoricEvent $event): void {
        /** @var PlayerMove */
        if ($event instanceof PlayerMove) {
            $player = $event->getPlayer();
            $to = clone $event->getTo();
            $from = clone $event->getFrom();
            $lastTime = (isset($this->lastTime[$player->getName()])) ? $this->lastTime[$player->getName()] : -1;

            // Fix falling
            $from->y = 0;
            $to->y = 0;

            $distance = $to->distance($from);
            $allowed = 0.82567;

            if ($player->getPing() >= 200) {
                $allowed += ($player->getPing() * 0.0009);
            }

            // .9 is way to lenient for the allowed movements

            if ($player->isCreative() || $player->isSpectator() || $player->getAllowFlight()) {
                return;
            }

            if ($player->getEffect(1) !== null) {
                // Not tested fully, but definitely allows effects.
                if ($player->getEffect(1)->getEffectLevel() === 0) {
                    $allowed = $allowed;
                    // lol
                } else {
                    $allowed = ($player->getEffect(1)->getEffectLevel() + 0.45);
                }
            }

            // if the last MOVE packet sent was sent more than 2 secs ago
            if (microtime(true) >= $lastTime + 2) return;

            if ($distance >= $allowed) {
                $distance = round($distance, 2) * 100;
                $event->sendAlert('Speed', "Illegal movement, Moved too far in a single instance.");
                $event->issueViolation(CheatIdentifiers::CODES['Speed']);
                return;
            }
        }

        /** @var PacketRecieve */
        if ($event instanceof PacketRecieve) {
            $pk = $event->getPacket();
            $player = $event->getPlayer();

            if ($pk instanceof MovePlayerPacket) {
                $this->lastTime[$player->getName()] = microtime(true);
            }
        }
    }

    public function isEnabled(): Bool {
        return true;
    }
}
