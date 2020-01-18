<?php

namespace Bavfalcon9\Mavoric\events;

use pocketmine\event\Listener;
use pocketmine\Player;

use pocketmine\entity\projectile\Projectile;

use pocketmine\event\{
    block\BlockBreakEvent,
    entity\EntityDamageByEntityEvent,
    entity\ProjectileLaunchEvent
};

use Bavfalcon9\Mavoric\events\{
    player\PlayerAttack
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
        $damager = $event->getDamager();
        $entity = $event->getEntity();
        $isProjectile = ($entity instanceof Projectile);

        if ($damager instanceof Player) {
            $this->mavoric->broadcastEvent(new PlayerAttack($this->mavoric, $damager, $entity, false));
        }

        if ($entity instanceof Player) {
            $this->mavoric->broadcastEvent(new PlayerDamage($this->mavoric, $damager, $entity, $isProjectile)); 
        }

    }

}