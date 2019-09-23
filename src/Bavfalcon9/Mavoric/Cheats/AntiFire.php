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

/* API CHANGE (Player) */

class AntiFire implements Listener {
    private $mavoric;
    private $plugin;

    public function __construct(Main $plugin, Mavoric $mavoric) {
        $this->plugin = $plugin;
        $this->mavoric = $mavoric;
    }
}