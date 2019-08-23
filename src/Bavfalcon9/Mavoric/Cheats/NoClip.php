<?php

namespace Bavfalcon9\Mavoric\Cheats;

use Bavfalcon9\Mavoric\Main;
use Bavfalcon9\Mavoric\Mavoric;

use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;
use pocketmine\Level\Position;

use pocketmine\event\player\PlayerMoveEvent;

use pocketmine\{
    Player,
    Server
};

/* API CHANGE (Player) */

class NoClip implements Listener {
    private $mavoric;
    private $plugin;
    private $slabs = [126, 44, 43, 182, 106, 214, 209, 208, 175, 176, 177, 167, 144, 127, 105, 96, 94, 78, 101, 90, 85];

    public function __construct(Main $plugin, Mavoric $mavoric) {
        $this->plugin = $plugin;
        $this->mavoric = $mavoric;
    }

    public function onMove(PlayerMoveEvent $event) {
        $player = $event->getPlayer();
        $into = $player->getPosition();
        $intoA = [$into->getX(), $into->getY()+1, $into->getZ()];
        //$intoB = [$into->getX(), $into->getY()+1, $into->getZ()];
        $level = $player->getLevel();
        $blockAtA = $level->getBlock(new Position($intoA[0], $intoA[1], $intoA[2], $level), false);
        //$blockAtB = $level->getBlock(new Position($intoB[0], $intoB[1], $intoB[2], $level), false);
        if ($player->isSpectator()) return false;
        if ($blockAtA->isSolid()) {
            if (in_array($blockAtA->getId(), $this->slabs)) return false;
            $this->mavoric->getFlag($player)->addViolation(Mavoric::NoClip);
            //$this->mavoric->messageStaff('§4§lMavoric §f> §r§b'.$player->getName().'§7 detected for §bNoClip§7.');
        }
    }
}