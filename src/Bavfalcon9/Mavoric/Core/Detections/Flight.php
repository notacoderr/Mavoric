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
use Bavfalcon9\Mavoric\events\MavoricEvent;
use Bavfalcon9\Mavoric\Math\PlayerCalculate;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\Player;

class Flight implements Detection {
    private $mavoric;
    private $plugin;
    private $checks = [];

    public function __construct(Mavoric $mavoric) {
        $this->plugin = $mavoric->getPlugin();
        $this->mavoric = $mavoric;
    }

    public function onEvent(MavoricEvent $event): void {
        
    }

    public function isEnabled(): Bool {
        return false;
    }

    public function onMove(PlayerMoveEvent $event) : void {
        $player = $event->getPlayer();
        $surroundings = PlayerCalculate::getSurroundings($player);
        $this->purgeOld(); // clear old data.
        if ($this->mavoric->getTpsCheck()->isLow()) return; // When tps is low, don't detect
        if ($player->getAllowFlight() === true) return;
        if ($player->getGamemode() === 3) return;
        if ($player->getGamemode() === 1) return;
        if (!PlayerCalculate::isOnGround($player) || PlayerCalculate::isAllAir($surroundings)) {
            if ($player->getInAirTicks() > 20 * 3) {
                if ($this->mavoric->isSuppressed(Mavoric::Flight)) $event->setCancelled();
                $this->mavoric->getFlag($player)->addViolation(Mavoric::Flight, 1);
                $this->mavoric->messageStaff('detection', $player, 'Flight');
            }

            // They're in the air, start doing checks.
            if ($player->getAllowFlight() === true) return;
            if ($player->getGamemode() === 3) return;
            if ($player->getGamemode() === 1) return;
            if (!isset($this->checks[$player->getName()])) {
                // They weren't previously detected..
                $this->checks[$player->getName()] = [
                    'position' => $player->getPosition(),
                    'time' => time()
                ];
                return;
            } else {
                $data = $this->checks[$player->getName()];

                if ($data['time'] + 1 <= time()) {
                    $this->checks[$player->getName()]['time'] = time();
                    // A half second has passed, do more checks
                    $highestBlock = $player->getLevel()->getHighestBlockAt($player->x, $player->z);
                    if (PlayerCalculate::isFallingNormal($data['position'], $player->getPosition(), $highestBlock)) {
                        /**
                         * TO DO: CHECK RANGE, MAKE SURE THAT POSITION TRAVELED ISNT THE SAME EVERY CHECK. THIS INDICATES HACKING.
                         */

                        unset($this->checks[$player->getName()]);
                        return;
                    }
                    if (!isset($data['ETA-Ground'])) {
                        $data['ETA-Ground'] = PlayerCalculate::estimateTime($player->getY());
                        $this->checks[$player->getName()]['ETA-Ground'] = PlayerCalculate::estimateTime($player->getY());
                    }
                    if ($player->getInAirTicks() > $data['ETA-Ground']) {
                        if (PlayerCalculate::isLagging($data['position'], $player->getPosition())) {
                        if (isset($data['lag-ticks']) && $data['lag-time'] >= 10) {
                            unset($this->checks[$player->getName()]);
                                $this->mavoric->kick($player, 'High Ping.');
                                $this->mavoric->messageStaff('custom', null, "{$player->getName()} was kicked because they were in air too long.");
                                return;
                            }
                            if (!isset($data['lag-ticks'])) {
                                $this->checks['lag-time'] = 1;
                                return;
                            } else {
                                $this->checks['lag-time']++;
                                return;
                            }
                        }
                        // Probably flying, add a violation
                        $player->teleport($data['position']);
                        $this->mavoric->getFlag($player)->addViolation(Mavoric::Flight, 1);
                        $this->mavoric->messageStaff('detection', $player, 'Flight', ' In air for: ' . $player->getInAirTicks()/20 . ' seconds.');
                        return;
                    }

                    // Assume they might be cheating, do more checks
                    if (!isset($data['ETA-Ground'])) {
                        $this->checks[$player->getName()]['ETA-Ground'] = PlayerCalculate::estimateTime($player->getY());
                    }
                    if (PlayerCalculate::isLagging($data['position'], $player->getPosition())) {
                        if (isset($data['lag-ticks']) && $data['lag-time'] >= 10) {
                            unset($this->checks[$player->getName()]);
                            $this->mavoric->kick($player, 'High Ping.');
                            $this->mavoric->messageStaff('custom', null, "{$player->getName()} was kicked because they were in air too long.");
                            return;
                        }
                        if (!isset($data['lag-ticks'])) {
                            $this->checks['lag-time'] = 1;
                            return;
                        } else {
                            $this->checks['lag-time']++;
                            return;
                        }
                    }
                    // Almost positive they're cheating....
                    $this->mavoric->getFlag($player)->addViolation(Mavoric::Flight, 1);
                    $this->mavoric->messageStaff('detection', $player, 'Flight', ' In air for: ' . $player->getInAirTicks()/20 . ' seconds.');
                    return;
                } else {
                    return;
                }
            }
        }
    }

    public function purgeOld() : void {
        foreach ($this->checks as $key=>$val) {
            if ($val['time'] + 30 <= time()) unset($this->checks[$key]);
        }
        return;
    }
}