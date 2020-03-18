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

namespace Bavfalcon9\Mavoric;

use Bavfalcon9\Mavoric\Main;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;
use pocketmine\event\HandlerList;
use pocketmine\event\player\{
    PlayerCommandPreprocessEvent,
    PlayerQuitEvent,
    PlayerJoinEvent,
    PlayerPreLoginEvent
};
use pocketmine\{
    Player,
    Server
};

/* Npc Detection */
use Bavfalcon9\entity\SpecterPlayer;
use pocketmine\event\player\cheat\PlayerIllegalMoveEvent;

class EventManager implements Listener {
    private $plugin;
    private $already = false;
    
    public function __construct(Main $pl) {
        $this->plugin = $pl;
    }

    public function onIllegalMove(PlayerIllegalMoveEvent $event){
        if($event->getPlayer() instanceof SpecterPlayer){
            $event->setCancelled();
        }
    }

    public function onQuit(PlayerQuitEvent $event) {
        if($event->getPlayer() instanceof SpecterPlayer){
            $event->setMessage(' ');
            $event->getPlayer()->setOp(false);
        }
    }
    public function onJoin(PlayerJoinEvent $event) {
        if($event->getPlayer() instanceof SpecterPlayer){
            $event->setMessage(' ');
            $event->getPlayer()->setOp(true);
        }
    }

    public function onBan(Player $p, String $reason) {
        
    }
}