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
namespace Bavfalcon9\Mavoric\Cheat\Combat;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Cheat\Cheat;
use Bavfalcon9\Mavoric\Cheat\CheatManager;

class Reach extends Cheat {
    public function __construct(Mavoric $mavoric) {
        parent::__construct($mavoric, 'Reach', 'Combat', 1, true);
    }

    /**
     * Called when a entity is damaged by an entity
     */
    public function onAttack(EntityDamageByEntityEvent $ev): void {
        $damager = $ev->getDamager();
        $damaged = $ev->getEntity();

        if (!($damager instanceof Player)) return;
        if ($ev instanceof EntityDamageByChildEntityEvent) return;
        if ($damager->isCreative()) return;
        
        $allowed = ($damager->getPing() >= 200) ? 6 + ($damager->getPing() * 0.003) : 6.2;
        
        if ($damager->distance($damaged) > $allowed) {
            $this->increment($damager->getName(), 1);
            $this->suppress($ev);
            if ($this->getViolation($damager->getName()) % 4 === 0) {
                $msg = "§4[MAVORIC]: §c{$damager->getName()} §7failed §c{$this->getName()}[{$this->getId()}]";
                $violations = $this->mavoric->getViolationDataFor($damager);
                $violations->incrementLevel($this->getName());
                $notifier = $this->mavoric->getVerboseNotifier();
                $notifier->notify($msg, "§8(§7Entity-§b{$damaged->getId()}§7, §7Distance-§b{$damager->distance($damaged)}§7, Ping-§b{$damager->getPing()}§8)");
            }
        }
    }    
}