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
namespace Bavfalcon9\Mavoric\Cheat\Movement;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Cheat\Cheat;
use Bavfalcon9\Mavoric\Cheat\CheatManager;
use Bavfalcon9\Mavoric\Events\Player\PlayerVelocityEvent;

class Velocity extends Cheat {
    /** @var Mixed[] */
    private $moveTimes;
    /** @var Bool[] */
    private $processing;

    public function __construct(Mavoric $mavoric) {
        parent::__construct($mavoric, 'Velocity', 'Movement', 4, false);
        $this->moveTimes = [];
        $this->processing = [];
    }

    /**
     * Called when a entity is damaged by an entity
     */
    public function onVelocity(PlayerVelocityEvent $ev): void {
        $player = $ev->getPlayer();
        $this->moveTimes[$player->getName()] = [
            "last" => $ev->getDirection(),
            "tick" => $this->getServer()->getTick(),
            "moves" => 0
        ];
    }

    public function onPlayerMove(PlayerMoveEvent $ev): void {
        $player = $ev->getPlayer();

        if (!isset($this->moveTimes[$player->getName()])) return;
        if ($ev->isCancelled()) return;

        $this->moveTimes[$player->getName()]["moves"]++;
        $session = $this->moveTimes[$player->getName()];
        $maxTick = ($player->getPing() / 50) + 5;
        $elapsed = $this->getServer()->getTick() - $session['tick'];
        $to = clone $ev->getTo();
        $to->x = 0;
        $to->z = 0;
        $from = clone $ev->getFrom();
        $from->x = 0;
        $from->z = 0;
        $diff = $to->distance($from);
        
        if ($player->getInAirTicks() >= $maxTick) {
            unset($this->moveTimes[$player->getName()]);
            return;
        }

        if ($diff < ($session['last'] * 0.99)) {
            if ($maxTick >= $elapsed || $session["moves"] >= 5) {
                $this->increment($player->getName(), 1);
                #$violations = $this->mavoric->getViolationDataFor($player);
                #$violations->incrementLevel($this->getName(), 10);
                $msg = "§4[MAVORIC]: §c{$player->getName()} §7failed §c{$this->getName()}[{$this->getId()}]";
                $notifier = $this->mavoric->getVerboseNotifier();
                $notifier->notify($msg, "§8(§7Expected-§b".($session['last'] * 0.99)."§7, §7Actual-§b".$diff."§7, Ticks-§b{$elapsed}§7, MaxTicks-§b{$maxTick}§7, Ping-§b{$player->getPing()}§8)");
            }
        }
        unset($this->moveTimes[$player->getName()]);
    }
}