<?php

namespace Bavfalcon9\Mavoric\events;

use pocketmine\event\Listener;

use pocketmine\event\{
    block\BlockBreakEvent,
    entity\EntityDamageByEntityEvent,
    entity\ProjectileLaunchEvent
};

class EventHandler implements Listener {
    private $mavoric;
    private $plugin;

    public function __construct(Mavoric $mavoric) {
        $this->mavoric = $mavoric;
        $this->plugin = $mavoric->getPlugin();
        $mavoric->getServer()->getPluginManager()->registerEvents($this, $this->plugin);
    }

    /**
     * ENTITIES
     */

    public function onEntityDamage(EntityDamageByEntityEvent $event): void {

    }

}