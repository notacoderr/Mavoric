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

use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;
use Bavfalcon9\Mavoric\events\MavoricEvent;
use Bavfalcon9\Mavoric\events\player\PlayerAttack;
use Bavfalcon9\Mavoric\Core\Utils\CheatIdentifiers;

class MultiAura implements Detection {
    private $mavoric;
    private $plugin;
    private $queue = [];

    public function __construct(Mavoric $mavoric) {
        $this->plugin = $mavoric->getPlugin();
        $this->mavoric = $mavoric;
    }

    public function onEvent(MavoricEvent $event): void {
        if (!$event instanceof PlayerAttack) return;

        $victim = $event->getVictim();
        $damager = $event->getPlayer();

        if (isset($this->queue[$damager->getId()])) {
            $multiAura = $this->queue[$damager->getId()];
            $distance = $damager->getPosition()->distance($victim->getPosition());
            if (($distance[0] <= 1.5 || $distance[1] <= 1.5) && ($distance[0] >= -1.5 || $distance[1] >= -1.5)) return;
            if (!in_array($victim->getId(), $multiAura['targets'])) array_push($this->queue[$damager->getId()]['targets'], $victim->getId());    
            if (sizeof($multiAura['targets']) >= 2 && ($multiAura['time'] + 0.20) >= microtime(true)) {
                $inTime = microtime(true) - ($multiAura['time']);
                $event->issueViolation(CheatIdentifiers::CODES['MultiAura']);
                $event->sendAlert('MultiAura', 'Illegal attack, hit ' . sizeof($multiAura['targets']) . ' entities in ' . $inTime . ' seconds');
            }
            if (($multiAura['time'] + 0.25) <= microtime(true)) {
                $this->queue[$damager->getId()] = [
                    "time" => microtime(true),
                    "targets" => []
                ];
            }
        } else {
            $this->queue[$damager->getId()] = [
                "time" => microtime(true),
                "targets" => [$victim->getId()]
            ];
        }
    }

    public function isEnabled(): Bool {
        return true;
    }
}