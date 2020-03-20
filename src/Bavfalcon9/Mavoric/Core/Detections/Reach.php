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
            $pearlHandler = $this->mavoric->getPearlHandler();
            $amt = 6;
            
            $throws = [$pearlHandler->getMostRecentThrowFrom($damager->getName()), $pearlHandler->getMostRecentThrowFrom($entity->getName())];

            if ($throws[0] !== null) {
                if ($throws[0]->getLandingTime() + 4 <= time()) {
                    $pos = $throws[0]->getLandingLocation();
                    
                }
            }

            if ($event->getDistance() >= $amt) {
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