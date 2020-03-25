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

use Bavfalcon9\Mavoric\Main;
use Bavfalcon9\Mavoric\Core\Handlers\Attack\Attack;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\Player;

class AttackHandler implements Listener {
    /** @var Main */
    private $plugin;
    /** @var Mixed[] */
    private static $attacks = [];

    /**
     * Returns the most recent attack from an entity with the given ID
     * @param int $id - Entity Runtime ID
     * @return Array|Null - Hit data or nothing
     */
    public static function getLastAttack(int $id): ?Array {
        if (!isset(self::$attacks[$id])) {
            return null;
        } else {
            return self::$attacks[$id][count(self::$attacks[$id]) - 1];
        }
    }

    /**
     * Returns the most recent attack time from an entity with the given ID
     * @param int $id - Entity Runtime ID
     * @return int
     */
    public static function getLastAttackTime(int $id): int {
        return (self::getLastAttack($id) === null) ? -1 : self::getLastAttack($id)['time'];
    }

    /**
     * Returns the most recent damaged entity with the given ID
     * @param int $id - Entity Runtime ID
     * @return Array|Null
     */
    public static function getLastDamage(int $id): ?Array {
        $recent = null;

        foreach (self::$attacks as $damager=>$attacks) {
            foreach ($attacks as $attack) {
                if ($attack['entity'] === $id) {
                    if (!$recent) {
                        $recent = [
                            'time' => $attack['time'],
                            'id' => $damager,
                            'player' => $attack['player']
                        ];
                    }
                    if ($recent['time'] < $attacks['time']) {
                        $recent = [
                            'time' => $attack['time'],
                            'id' => $damager,
                            'player' => $attack['player']
                        ];      
                    }
                }
            }
        }

        return $recent;
    }

    /**
     * Returns the most recent damage time from an entity with the given ID
     * @param int $id - Entity Runtime ID
     * @return Array|Null
     */
    public static function getLastDamageTime(int $id): int {
        return (self::getLastDamage($id) === null) ? -1 : self::getLastDamage($id)['time'];
    }

    public function __construct(Main $plugin) {
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
        $this->plugin = $plugin;
    }

    /**
     * PM Event, triggered when an entity is damaged by another
     * @param EntityDamageByEntityEvent $event - Event
     * @return void
     */
    public function onAttack(EntityDamageByEntityEvent $event): void {
        $damager = $event->getDamager();
        $victim  = $event->getEntity();

        // This does NOT care about entity names, and carries entity runtime ids
        if (!isset(self::$attacks[$damager->getId()])) {
            self::$attacks[$damager->getId()] = [];
        }
        
        array_push(self::$attacks[$damager->getId()], [
            "time" => microtime(true),
            "player" => ($damager instanceof Player),
            "entity" => $victim->getId(),
            "entityPlayer" => ($victim instanceof Player)
        ]);

        $this->purgeOld();
    }

    /**
     * Purges old attack data (2 seconds or older)
     * @return void
     */
    private function purgeOld(): void {
        foreach (self::$attacks as $damager=>$attacks) {
            foreach ($attacks as $index=>$attack) {
                if ($attack['time'] + 2 <= microtime(true)) {
                    array_splice(self::$attacks[$damager], $index);
                }

                if (empty(self::$attacks[$damager])) {
                    unset(self::$attacks[$damager]);
                }
            }
        }
    }
}