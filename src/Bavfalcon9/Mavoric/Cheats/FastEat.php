<?php

namespace Bavfalcon9\Mavoric\Cheats;

use Bavfalcon9\Mavoric\Main;
use Bavfalcon9\Mavoric\Mavoric;

use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;

use pocketmine\event\entity\{
    EntityDamageByEntityEvent
};
use pocketmine\{
    Player,
    Server
};

use pocketmine\event\player\PlayerItemConsumeEvent;

/* API CHANGE (Player) */
/**
 * DO not use this for newer items.
 */

class FastEat implements Listener {
    private $mavoric;
    private $plugin;

    private $lastConsumed = [];

    public function __construct(Main $plugin, Mavoric $mavoric) {
        $this->plugin = $plugin;
        $this->mavoric = $mavoric;
    }

    public function onConsume(PlayerItemConsumeEvent $event) {
        $player = $event->getPlayer();

        if (!isset($this->lastConsumed[$player->getName()])) {
            $this->lastConsumed[$player->getName()] = [
                "consumedAt" => microtime(true),
                "food" => $event->getItem()->getName()
            ];
        }

        $consumed = $this->lastConsumed[$player->getName()];

        if (microtime(true) - $consumed->consumedAt <= 2) {
            $event->setCancelled(true);
            $this->mavoric->getFlag($player)->addViolation(Mavoric::FastEat);
        } else {
            unset($this->lasConsumed[$player->getName()]);
            return;
        }
    }
}