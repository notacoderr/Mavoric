<?php
/***
 *      __  __                       _      
 *     |  \/  |                     (_)     
 *     | \  / | __ ___   _____  _ __ _  ___ 
 *     | |\/| |/ _` \ \ / / _ \| '__| |/ __|
 *     | |  | | (_| |\ V / (_) | |  | | (__ 
 *     |_|  |_|\__,_| \_/ \___/|_|  |_|\___|
 *                                          
 *   THIS CODE IS TO NOT BE REDISTRUBUTED
 *   @author MavoricAC
 *   @copyright Everything is copyrighted to their respective owners.
 *   @link https://github.com/Olybear9/Mavoric                                  
 */

namespace Bavfalcon9\Mavoric\Detections;

use Bavfalcon9\Mavoric\Main;
use Bavfalcon9\Mavoric\Mavoric;
use pocketmine\event\Listener;
use Bavfalcon9\Mavoric\events\{
    MavoricEvent,
    player\PlayerMove
};
use pocketmine\{
    Player,
    Server
};

class Teleport implements Detection {
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
        if ($distance >= 2) {
            if ($player->getPing() >= 300) {
                $distance = round($distance, 2);
                $event->sendAlert('Teleport', "Traveled {$distance} blocks in one move");
                $event->issueViolation(Mavoric::CHEATS['Teleport']);
                return;
            } else {
                $distance = round($distance, 2);
                $event->sendAlert('Teleport', "Traveled {$distance} blocks in one move");
                $event->issueViolation(Mavoric::CHEATS['Teleport']);
                return;
            }
        }
    }

    public function isEnabled(): Bool {
        return false;
    }
}
