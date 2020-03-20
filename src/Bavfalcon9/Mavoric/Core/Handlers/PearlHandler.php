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
namespace Bavfalcon9\Mavoric\Core\Handlers;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDespawnEvent;
use Bavfalcon9\Mavoric\Main;
use Bavfalcon9\Mavoric\Core\Handlers\Pearl\PearlThrow;
use Bavfalcon9\Mavoric\entity\Pearl\events\PearlThrownEvent;
use Bavfalcon9\Mavoric\Core\Handlers\Pearl\PearlPurgeTask;

class PearlHandler implements Listener {
    /** @var PearlThrow[] */
    private $throws;
    /** @var Main */
    private $plugin;

    public function __construct(Main $plugin) {
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
        $plugin->getScheduler()->scheduleRepeatingTask(new PearlPurgeTask($this), 20 * 10);
        $this->plugin = $plugin;
        $this->throws = [];
    }

    /**
     * Triggered when a enderpearl is thrown
     * @param PearlThrownEvent $event - PearlThrownEvent
     * @return void
     */
    public function onThrow(PearlThrownEvent $event): void {
        $player = $event->getPlayer();
        $entity = $event->getEntity();

        if ($event->isCancelled()) {
            return;
        }

        $throw = new PearlThrow($player, $entity->getId());
        $this->throws[] = $throw;
        return;
    }

    /**
     * Triggered when a entity is despawned
     * @param EntityDespawnEvent $event - EntityDespawnEvent
     * @return void
     */
    public function onDespawn(EntityDespawnEvent $event): void {
        if ($event->isProjectile()) {
            $id = $event->getEntity()->getId();

            foreach ($this->throws as &$throw) {
                if ($throw->getPearlEntityId() === $id) {
                    $throw->setCompleted(microtime(true), $event->getEntity()->getPosition());
                }
            }
        }
    }

    /**
     * Clears throws from memory for performance reasons.
     * @return void
     */
    public function purge(): void {
        $purged = 0;
        foreach ($this->throws as &$throw) {
            if ($throw->getLandingTime() + 10 <= time()) {
                unset($this->throws[array_search($throw, $this->throws)]);
                $purged++;
            }
        }
        return;
    }

    /**
     * Returns a list of throws from a player
     * @param String $player - Player name
     * @return PearlThrow[] - Array of throws
     */
    public function getThrowsFrom(String $player): Array {
        $allThrows = [];

        foreach ($this->throws as $throw) {
            if ($throw->getPlayer()->getName() === $player) {
                $allThrows[] = $throw;
            }
        }

        return $allThrows;
    }

    /**
     * Returns the most recent throw from a player
     * @param String $player - Player name
     * @return PearlThrow|Null - The most recent thrown pearl from player
     */
    public function getMostRecentThrowFrom(String $player): ?PearlThrow {
        $throws = $this->getThrowsFrom($player);
        $recent = null;

        foreach ($throws as $throw) {
            if (!$recent) {
                $recent = $throw;
                continue;
            }

            if ($recent->getThrownTime() > $throw->getThrowTime()) {
                $recent = $throw;
                continue;
            }
        }

        return $recent;
    }
}