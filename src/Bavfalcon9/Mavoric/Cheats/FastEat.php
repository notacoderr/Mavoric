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
                "consumedAt" => time(),
                "food" => $event->getItem()->getName(),
                "consumed" => []
            ];
        }

        $consumed = $this->lastConsumed[$player->getName()];
        array_push($this->lastConsumed[$player->getName()]['consumed'], $event->getItem()->getName());

        if (time() - $consumed['consumedAt'] <= 2) {
            $t = time() - $consumed['consumedAt'];
            $food = sizeof($consumed['consumed']);

            if ($food <= 1) return;
            $event->setCancelled();
            $player->setFood(0);
            $this->mavoric->getFlag($player)->addViolation(Mavoric::FastEat, 20);
            $this->mavoric->messageStaff('detection', $player, 'FastEat');
            return;
        } else {
            unset($this->lastConsumed[$player->getName()]);
            return;
        }
    }
}