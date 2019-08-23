<?php

namespace Bavfalcon9\Mavoric\Cheats;

use Bavfalcon9\Mavoric\Main;
use Bavfalcon9\Mavoric\Mavoric;

use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;

use pocketmine\event\entity\{
    EntityDamageByEntityEvent
};

use pocketmine\event\player\PlayerMoveEvent;

use pocketmine\{
    Player,
    Server
};

/* API CHANGE (Player) */

class Flight implements Listener {
    private $mavoric;
    private $plugin;

    public function __construct(Main $plugin, Mavoric $mavoric) {
        $this->plugin = $plugin;
        $this->mavoric = $mavoric;
    }

    public function onMove(PlayerMoveEvent $event) {
        $isFlying = $this->checkFlight($event);
        $player = $event->getPlayer();
        if ($isFlying === true) {
           // $this->mavoric->messageStaff('§4§lMavoric §f> §r§b'.$player->getName().'§7 is using: §bFlight.');
        }
    }

	public function checkFlight($event) {
		$player = $event->getPlayer();
		$oldPos = $event->getFrom();
        $newPos = $event->getTo();
        if ($this->isFalling($player, $event)) {
            return false;
        } else {
            return true;
        }
        if (!$this->isFalling($player, $event)) {
            if(!$player->isCreative() && !$player->isSpectator() && !$player->getAllowFlight()) {
                // They are flying up
               // if ($oldPos->getY() >= $newPos->getY()){
                if($player->GetInAirTicks() > 40 && $player->getPing() <= 350) {
                    $maxY = $player->getLevel()->getHighestBlockAt(floor($newPos->getX()), floor($newPos->getZ()));
                    if($newPos->getY() - 3 > $maxY){
                        return true;
                    }
                } else {
                    return false;
                }
                //}
            }
        }
        return false;
    }

    public function isFalling(Player $player, $event) {
        // TODO: check if the player is falling at 3.5 blocks a tick
        $to = $event->getTo();
        $from = $event->getFrom();

        // Simple check for if the player is falling
        if ($to->getY() >= $from->getY()) return false;
        else return true;

    }
}