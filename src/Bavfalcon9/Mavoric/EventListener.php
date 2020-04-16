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

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityMotionEvent;
use pocketmine\event\player\PlayerMoveEvent;
use Bavfalcon9\Mavoric\Events\Player\PlayerVelocityEvent;
use Bavfalcon9\Mavoric\Events\Violation\ViolationChangeEvent;

class EventListener implements Listener {
    /** @var Mavoric */
    private $mavoric;
    /** @var Loader */
    private $plugin;
    /** @var Mixed[] */
    private $kbSession;

    public function __construct(Mavoric $mavoric, Loader $plugin) {
        $this->mavoric = $mavoric;
        $this->plugin = $plugin;
        $this->kbSession = [];
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    /**
     * Notifies staff about violation level
     */
    public function onViolationChange(ViolationChangeEvent $ev): void {
        $violation = $ev->getViolation();
        $cNotifier = $this->mavoric->getCheckNotifier();
        $cNotifier->notify("§4[MAVORIC]§4: §c{$ev->getPlayer()->getName()} §7detected for §c{$ev->getCheat()}", "§8[§7{$violation->getCheatProbability()}§f% | §7VL §f{$ev->getCurrent()}§8]");
    }

    /**
     * Calls the knockback event, we can check packets to see if the player is being respective to them
     */
    public function onAttackEntity(EntityDamageByEntityEvent $ev): void {
        $damager = $ev->getDamager();
        $victim = $ev->getEntity();

        if (!($victim instanceof Player)) return;
        if ($ev->isCancelled()) return;

        $knockback = $ev->getKnockBack();
        $this->kbSession[$victim->getId()] = [microtime(true), $knockback];
    }

    /**
     * The server sends this to the client we can check this for kb
     */
    public function onMotion(EntityMotionEvent $ev): void {
        $entity = $ev->getEntity();
        // we're not gonna bother checking non-player entities, saves ticktime
        // clean this up and move to 
        if (!($entity instanceof Player)) return;
        if (!isset($this->kbSession[$entity->getId()])) return;

        $session = $this->kbSession[$entity->getId()];

        if ($session[0] + 1 < microtime(true)) {
            unset($this->kbSession[$entity->getId()]);
            return;
        }

        $motion = $ev->getVector();
        $previousVector = $entity;
        $base = $motion->y;

        $event = new PlayerVelocityEvent($entity, $motion, 0, 0, $base);
        $event->call();

        if ($event->isCancelled()) {
            $ev->setCancelled(true);
        }
        unset($this->kbSession[$entity->getId()]);
        return;
    }

    public function onPlayerMove(PlayerMoveEvent $ev): void {
        $player = $ev->getPlayer();
    }
}