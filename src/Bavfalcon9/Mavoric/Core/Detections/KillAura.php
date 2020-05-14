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
use Bavfalcon9\Mavoric\Events\MavoricEvent;
use Bavfalcon9\Mavoric\Events\player\PlayerAttack;
use Bavfalcon9\Mavoric\Events\packet\PacketRecieve;
use Bavfalcon9\Mavoric\Events\player\PlayerMove;
use Bavfalcon9\Mavoric\Core\Handlers\AttackHandler;
use Bavfalcon9\Mavoric\Core\Utils\CheatIdentifiers;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
/**
 * To note:
 * - Horion: 
 *   Checks nearest entity starting from where the player stands.
 *   This behavior is not randomized making it easier to detect.
 *   Head snaps, movement is not smooth.
 *   Horion prefers hitting behind the player before hitting infront of the player
 * - 
 */
class KillAura implements Detection {
    private $mavoric;

    public function __construct(Mavoric $mavoric) {
        $this->mavoric = $mavoric;
    }

    public function onEvent(MavoricEvent $event): void {
        /** @var PlayerAttack */
        if ($event instanceof PlayerAttack) {
            $victim = $event->getVictim();
            $damager = $event->getAttacker();

            $AABB = $victim->getBoundingBox();
            $eyes = new Vector3();
        }

        /** @var PlayerMove */
        if ($event instanceof PlayerMove) {
            // Snap check
            $before = clone $event->getFrom();
            $after =  clone $event->getTo();

            $diff = MathUtils::getDifferenceFrom360($before->getYaw(), $after->getYaw());
            
            $before->y = 0;
            $after->y = 0;

            /**
              if (floor($before->distance($after)) < 1) {
                   // do nothing
                   // get last attack and check entity ids
                   return;
              }
             */

            if ($diff >= 100) {
                $lastAttack = AttackHandler::getLastAttack($event->getPlayer()->getId());
                if ($lastAttack === null) {
                    return;
                } else {
                    if ($lastAttack['time'] + 0.5 > microtime(true)) {
                        return;
                    } else {
                        $event->issueViolation(CheatIdentifiers::CODES['KillAura']);
                        $event->sendAlert('KillAura', 'Illegal head movement and attack ' . $event->getPlayer()->getName() . ' moved their head too quickly.');
                    }
                }
            }
        }

        /** @var PacketRecieve */
        if ($event instanceof PacketRecieve) {
            $packet = $event->getPacket();
            if ($packet instanceof InventoryTransactionPacket) {
                $type = $packet->transactionType;
                if ($type === InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY) {
                    # Entity runtime id's
                    return; // check this later
                }
            } 
        }
    }

    public function isEnabled(): Bool {
        return true;
    }
}