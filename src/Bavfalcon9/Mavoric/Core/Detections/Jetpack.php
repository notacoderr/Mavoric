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
use Bavfalcon9\Mavoric\Core\Utils\CheatIdentifiers;
use pocketmine\event\Listener;
use Bavfalcon9\Mavoric\events\{
    MavoricEvent,
    player\PlayerMove
};
use pocketmine\{
    Player,
    Server
};

class Jetpack implements Detection {
    private $mavoric;
    private $plugin;
    private $disabled = true;

    public function __construct(Mavoric $mavoric) {
        $this->plugin = $mavoric->getPlugin();
        $this->mavoric = $mavoric;
    }
    

    public function onEvent(MavoricEvent $event): void {
        if (!$event instanceof PlayerMove) return;
            $player = $event->getPlayer();
            $to = $event->getTo();
            $from = $event->getFrom();
            $distance = abs($to->distance($from));
            if ($distance >= 2.5) {
                if ($player->getPing() >= 300) {
                    $distance = round($distance, 2);
                    $event->sendAlert('Jetpack', "Illegal movement, jetpack detected with {$distance} blocks in few moves.");
                    $event->issueViolation(CheatIdentifiers::CODES['Jetpack']);
                    return;
                } else {
                    $distance = round($distance, 2);
                    $event->sendAlert('Jetpack', "Illegal movement, jetpack detected with {$distance} blocks in few moves.");
                    $event->issueViolation(CheatIdentifiers::CODES['Jetpack']);
                    return;
                }
            }
    }

    public function isEnabled(): Bool {
        return false;
    }
}