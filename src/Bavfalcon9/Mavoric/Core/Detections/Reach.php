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
    }

    public function isEnabled(): Bool {
        return true;
    }
    
}