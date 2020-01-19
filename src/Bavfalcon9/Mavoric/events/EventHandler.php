<?php

namespace Bavfalcon9\Mavoric\events;

use pocketmine\event\Listener;
use pocketmine\Player;

use pocketmine\entity\projectile\Projectile;
use pocketmine\item\TieredTool;

use pocketmine\event\{
    block\BlockBreakEvent,
    entity\EntityDamageByEntityEvent,
    entity\ProjectileLaunchEvent,
    player\PlayerInteractEvent,
};

use Bavfalcon9\Mavoric\events\{
    player\PlayerAttack,
    player\PlayerClick,
    player\PlayerBreakBlock
};

class EventHandler implements Listener {
    private $mavoric;
    private $plugin;
    private $breakTimes = [];

    public function __construct(Mavoric $mavoric) {
        $this->mavoric = $mavoric;
        $this->plugin = $mavoric->getPlugin();
        $mavoric->getServer()->getPluginManager()->registerEvents($this, $this->plugin);
    }

    /** 
     * PLAYER
     */
    
    public function onInteract(PlayerInteractEvent $event): void {
        if ($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
            $this->breakTimes[$event->getPlayer()->getName()] = floor(microtime(true) * 20);
        }

        $e = new PlayerClick($this->mavoric, $event->getPlayer(), $event->getAction(), $event->getItem(), $event->getBlock(), $event->getTouchVector(), $event->getFace());
        $this->mavoric->broadcastEvent($e);
        return;
    }
    

    /**
     * ENTITIES
     */
    public function onEntityDamage(EntityDamageByEntityEvent $event): void {
        $damager = $event->getDamager();
        $entity = $event->getEntity();
        $isProjectile = ($entity instanceof Projectile);

        if ($damager instanceof Player) {
            $this->mavoric->broadcastEvent(new PlayerAttack($this->mavoric, $damager, $entity, $isProjectile));
        }

        if ($entity instanceof Player) {
            $this->mavoric->broadcastEvent(new PlayerDamage($this->mavoric, $damager, $entity, $isProjectile)); 
        }
    }


    /**
     * BLOCKS
     */
    public function onBlockBreak(BlockBreakEvent $event) {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $item = $event->getItem();
        $time = $this->getBreakTime($player);

        $this->mavoric->broadcastEvent(new PlayerBreakBlock($this->mavoric, $player, $block, $time));
        return;
    }

    /**
     * Checks by Mavoric
     */

    private function getBreakTime(Player $player): int {
        if (!isset($this->breakTimes[$player->getName()])) {
            return -1;
        }

        $calculatedTime = ciel(microtime(true) * 20) - $this->breakTimes[$player->getName()];
        unset($this->breakTimes[$player->getName()]);
        
        return $calculatedTime;
    }

}