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
 *  @link https://github.com/Bavfalcon9/Mavoric                                  
 */
namespace Bavfalcon9\Mavoric\Cheat\Combat;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Cheat\Cheat;
use Bavfalcon9\Mavoric\Cheat\CheatManager;

class Reach extends Cheat {
    public function __construct(Mavoric $mavoric, int $id = -1) {
        parent::__construct($mavoric, 'Reach', 'Combat', $id, true);
    }

    /**
     * @param EntityDamageByEntityEvent $ev
     * @return void
     */
    public function onAttack(EntityDamageByEntityEvent $ev): void {
        $damager = $ev->getDamager();
        $damaged = $ev->getEntity();

        if (!($damager instanceof Player)) return;
        if ($ev instanceof EntityDamageByChildEntityEvent) return;
        if ($damager->isCreative()) return;
        
        $allowed = ($damager->getPing() >= 200) ? 6 + ($damager->getPing() * 0.003) : 6.2;
        
        if ($damager->distance($damaged) > $allowed) {
            $this->increment($damager->getName(), 1); // increments Cheat flag
            $this->notifyAndIncrement($damager, 4, 1, [
                "Entity" => $damaged->getId(),
                "Distance" => $damager->distance($damaged),
                "Ping" => $damager->getPing()
            ]);
            return;
        }
    }    
}