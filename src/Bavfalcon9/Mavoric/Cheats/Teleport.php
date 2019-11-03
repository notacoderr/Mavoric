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

namespace Bavfalcon9\Mavoric\Cheats;

use Bavfalcon9\Mavoric\Main;
use Bavfalcon9\Mavoric\Mavoric;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\{
    Player,
    Server
};

class Teleport implements Listener {
    private $mavoric;
    private $plugin;
    private $disabled = true;

    public function __construct(Main $plugin, Mavoric $mavoric) {
        $this->plugin = $plugin;
        $this->mavoric = $mavoric;
    }
    

    public function onMove(PlayerMoveEvent $event) {
        if ($this->disabled) return;
        $player = $event->getPlayer();
        $to = $event->getTo();
        $from = $event->getFrom();
        $distance = abs($to->distance($from));
        if ($distance >= 5) {
            if ($player->getPing() >= 300) {
                if ($this->mavoric->isSuppressed(Mavoric::Teleport)) $event->setCancelled();
                $this->mavoric->messageStaff('detection', $player, 'Teleport', " [Traveled {$distance} blocks in one move but is lagging]");
                return;
            } else {
                if ($this->mavoric->isSuppressed(Mavoric::Teleport)) $event->setCancelled();
                $this->mavoric->getFlag($player)->addViolation(Mavoric::Teleport);
                $this->mavoric->messageStaff('detection', $player, 'Teleport', " [Traveled {$distance} blocks in one move]");
                return;
            }
        }
    }
}
