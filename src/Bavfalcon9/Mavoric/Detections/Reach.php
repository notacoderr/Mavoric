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

use Bavfalcon9\Mavoric\Main;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\events\{
    MavoricEvent,
    player\PlayerAttack,
    player\PlayerTeleport,
    player\PlayerMove,
    player\PlayerClick
};
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;

use pocketmine\Player;
use pocketmine\Server;

class Reach implements Detection {
    /** @var Mavoric */
    private $mavoric;
    /** @var Main */
    private $plugin;
    /** @var Array */
    private $ender_pearls = [];
    /** @var Array */
    private $teleported = [];
    /** @var Array */
    private $teleportQueue = [];

    public function __construct(Mavoric $mavoric) {
        $this->plugin = $mavoric->getPlugin();
        $this->mavoric = $mavoric;
    }

    public function onEvent(MavoricEvent $event): void {
        /**
         * @var PlayerAttack
         */
        if ($event instanceof PlayerAttack) {
            $damager = $event->getAttacker();
            $entity = $event->getVictim();

            $amt = 6;
            if ($damager->getPing() >= 230) {
                $amt = $damager->getPing() / 34;
                if ($damager->getPing() >= 500) {
                    $amt = $damager->getPing() / 50;
                }
            }

            if ($entity instanceof Player) {
                if ($entity->getPing() >= 230) {
                    $amt = $entity->getPing() / 34;
                    if ($entity->getPing() >= 500) {
                        $amt = $entity->getPing() / 52;
                    }
                }
            }

            if ($event->getDistance() >= $amt) {
                if ($this->pearledAway($entity) === true) return;
                if ($this->pearledAway($damager) === true) return;
                if ($this->hasTeleported($entity) === true) return;
                if ($this->hasTeleported($damager) === true) return;
                if (!$damager->isCreative()) {
                    $event->issueViolation(Mavoric::CHEATS['Reach']);
                    $event->sendAlert('Reach', 'Illegal hit while attacking ' . $entity->getName() . ' over distance ' . round($event->getDistance(), 2) . ' blocks');
                    return;
                }
            }
        }
        /**
         * @var PlayerTeleport event
         */
        if ($event instanceof PlayerTeleport) {
            $player = $event->getPlayer();

            foreach ($this->teleportQueue as $p=>$t) {
                if ($t['time'] + 3 >= time()) unset($this->teleportQueue[$p]);
            }

            if (!isset($this->teleportQueue[$player->getName()])) $this->teleportQueue[$player->getName()] = [
                    'time' => microtime(true),
                    'pos' => $event->getFrom()
                ];
            if (!isset($this->ender_pearls[$player->getName()])) {
                return;
            } else {
                $this->teleported[$player->getName()] = [
                    'thrownAt' => $this->ender_pearls[$player->getName()],
                    'elapsed' => microtime(true) - $this->ender_pearls[$player->getName()],
                    'pos' => $event->getFrom()
                ];

                unset($this->ender_pearls[$player->getName()]);
                return;
            }
        }

        /**
         * @var PlayerClick event
         */
        if ($event instanceof PlayerClick) {
            if (!$event->isRightClick()) return;
            $player = $event->getPlayer();

            if ($event->getItem()->getId() === 368) {
                $this->ender_pearls[$player->getName()] = microtime(true);   
            }
            return;
        }
    }

    public function isEnabled(): Bool {
        return true;
    }

    private function pearledAway($p) {
        $p = $p->getName();
        if (empty($this->teleported)) return false;
        if (!isset($this->teleported[$p])) return false; // wtf lol
        if ((microtime(true) - (int) $this->teleported[$p]['thrownAt']) >= 6) {
            // Three seconds passed since teleport, ignore, but still return teleport if within 5 seconds?
            $cache = $this->teleported[$p]['thrownAt'];
            unset($this->teleported[$p]);

            if (microtime(true) - (int) $cache >= 6) return false;
            else {
                return true;
            }
        } else {
            return true;
        }
    }

    private function hasTeleported($p) {
        $p = $p->getName();
        // Purge cache
        
        foreach ($this->teleportQueue as $pl=>$t) {
            if (((int) $t + 3) >= time()) unset($this->teleportQueue[$pl]);
        }
        if (empty($this->teleportQueue)) return false;
        if (!isset($this->teleportQueue[$p])) return false; // wtf lol

        if ((microtime(true) - (int) $this->teleportQueue[$p]) >= 5) {
            $cache = $this->teleportQueue[$p];
            unset($this->teleportQueue[$p]);
            return false;
        } else {
            return true;
        }
    }

    
}