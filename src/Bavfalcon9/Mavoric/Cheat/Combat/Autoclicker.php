<?php
/***
 *      __  __                       _      
 *     |  \/  |                     (_)     
 *     | \  / | __ ___   _____  _ __ _  ___ 
 *     | |\/| |/ _` \ \ / / _ \| "__| |/ __|
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
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Cheat\Cheat;
use Bavfalcon9\Mavoric\Cheat\CheatManager;

class Autoclicker extends Cheat {
    /** @var int[] */
    private $cps;
    private $constant;

    public function __construct(Mavoric $mavoric) {
        parent::__construct($mavoric, "Autoclicker", "Combat", 3, true);
        $this->cps = [];
        $this->constant = [];
    }

    /**
     * @return void
     */
    public function onClickCheck(DataPacketReceiveEvent $ev): void {
        if ($ev->getPacket()::NETWORK_ID === InventoryTransactionPacket::NETWORK_ID) {
            if ($ev->getPacket()->transactionType === InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY) {
                $this->dueCheck($ev->getPlayer());
            }
        } else if ($ev->getPacket()::NETWORK_ID === LevelSoundEventPacket::NETWORK_ID) {
            if ($ev->getPacket()->sound === LevelSoundEventPacket::SOUND_ATTACK_NODAMAGE) {
                $this->dueCheck($ev->getPlayer());
            }
        } 
    }

    private function dueCheck(Player $player): void {
        if (!isset($this->cps[$player->getName()])) {
            $this->cps[$player->getName()] = [];
        }

        $time = microtime(true);

        array_push($this->cps[$player->getName()], microtime(true));

        $cps = count(array_filter($this->cps[$player->getName()],  function (float $t) use ($time) : bool {
            return ($time - $t) <= 1;
        }));
        
        if ($cps >= 100) {
            $this->increment($player->getName(), 1);
            $msg = "§4[MAVORIC]: §c{$player->getName()} §7failed §c{$this->getName()}[{$this->getId()}]";
            $notifier = $this->mavoric->getVerboseNotifier();
            $notifier->notify($msg, "§8(§7CPS-§b".$cps."§7, Ping-§b{$player->getPing()}§8)");
            if ($this->getViolation($player->getName()) % 2 === 0) {
                $violations = $this->mavoric->getViolationDataFor($player);
                $violations->incrementLevel($this->getName());
            }
        }

        if ($cps >= 22) {
            $this->increment($player->getName(), 1);
            $msg = "§4[MAVORIC]: §c{$player->getName()} §7failed §c{$this->getName()}[{$this->getId()}]";
            $notifier = $this->mavoric->getVerboseNotifier();
            $notifier->notify($msg, "§8(§7CPS-§b{$cps}§7, Ping-§b{$player->getPing()}§8)");
            if ($this->getViolation($player->getName()) % 2 === 0) {
                $violations = $this->mavoric->getViolationDataFor($player);
                $violations->incrementLevel($this->getName());
            }
        }
        
        if($cps >= 15){
            if($player->getPing() >= 250) return;
            if(!isset($this->constant[$player->getName()])) $this->constant[$player->getName()] = [];
            array_push($this->constant[$player->getName()], $cps);
            if(count($this->constant[$player->getName()]) === 10){
                if($this->testConstant($this->constant[$player->getName()], $cps) === true){
                    $this->increment($player->getName(), 1);
                    $msg = "§4[MAVORIC]: §c{$player->getName()} §7failed §c{$this->getName()}[{$this->getId()}]";
                    $notifier = $this->mavoric->getVerboseNotifier();
                    $notifier->notify($msg, "§8(§7CPS-§b{$cps}§7, Ping-§b{$player->getPing()}§8)");
                    if ($this->getViolation($player->getName()) % 2 === 0) {
                        $violations = $this->mavoric->getViolationDataFor($player);
                        $violations->incrementLevel($this->getName());
                    }
                }
            }
        }
    }
    
    private function testConstant(array $clicks, int $cps){
        $added = (int) array_sum($clicks);
        $values = (int) count($clicks);
        $average = round($added / $values, 0, PHP_ROUND_HALF_UP);
        /* you can add some stuff like i did here if you want to
        if($this->getPlugin()->debug === true) print_r($average . "\n"); */
        if($average === $cps){
            return true;
        } else {
            return false;
        }
    }
    
}
