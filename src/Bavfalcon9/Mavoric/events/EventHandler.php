<?php

namespace Bavfalcon9\Mavoric\events;

use pocketmine\event\Listener;
use pocketmine\Player;

use pocketmine\entity\projectile\Projectile;
use pocketmine\item\TieredTool;
use Bavfalcon9\Mavoric\Mavoric;
use pocketmine\math\Vector3;
use pocketmine\event\{
    server\DataPacketReceiveEvent,
    block\BlockBreakEvent,
    entity\EntityDamageByEntityEvent,
    entity\EntityTeleportEvent,
    entity\ProjectileLaunchEvent,
    player\PlayerInteractEvent,
    player\PlayerMoveEvent
};
use pocketmine\network\mcpe\protocol\{
    InventoryTransactionPacket,
    LevelSoundEventPacket,
    PlayerActionPacket
};
use Bavfalcon9\Mavoric\events\{
    player\PlayerAttack,
    player\PlayerBreakBlock,
    player\PlayerClick,
    player\PlayerMove,
    player\PlayerTeleport
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

    /** @deprecated - Broken */
    public function onInteract(PlayerInteractEvent $event): void {
        if ($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
            $this->breakTimes[$event->getPlayer()->getName()] = floor(microtime(true) * 20);
        }
        $e = new PlayerClick($this->mavoric, $event->getPlayer(), $event->getAction(), $event->getItem(), $event->getBlock(), $event->getTouchVector(), $event->getFace());
        $this->mavoric->broadcastEvent($e);
        return;
    }

    /**
     * @author Zedstar16 - CC OwnagePE
     * To Do: Add more events.
     */
    public function onClickCheck(DataPacketReceiveEvent $event): void {
        if ($event->getPacket()::NETWORK_ID === InventoryTransactionPacket::NETWORK_ID) {
            if ($event->getPacket()->transactionType === InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY) {
                $this->makeClickEvent($event->getPlayer(), false, false, true);
            }
        }
        if ($event->getPacket()::NETWORK_ID === LevelSoundEventPacket::NETWORK_ID) {
            if ($event->getPacket()->sound === LevelSoundEventPacket::SOUND_ATTACK_NODAMAGE) {
                $this->makeClickEvent($event->getPlayer(), true, false, true);
            }
        } 
        if ($event->getPacket()::NETWORK_ID === PlayerActionPacket::NETWORK_ID) {
            if ($event->getPacket()->action === PlayerActionPacket::ACTION_START_BREAK) {
                $this->breakTimes[$event->getPlayer()->getName()] = floor(microtime(true) * 20);
                $touch = new Vector3($event->getPacket()->x, $event->getPacket()->y, $event->getPacket()->z);
                $face = $event->getPacket()->face;
                //$this->makeClickEvent($event->getPlayer(), true, true, false, $touch, $face);
            }
        }
    }

    private function makeClickEvent(Player $player, $leftClick, $break, $entity, $touch = null, $face = 0): void {
        $type = 0;
        if ($leftClick && $break) $type = 0;
        if ($leftClick && !$break) $type = 2;
        if (!$entity && !$leftClick) $type = 3;
        if (!$leftClick && $entity) $type = 4;

        if ($touch === null) {
            $touch = new Vector3(abs($player->x), abs($player->y), abs($player->z));
        }
        $item = $player->getInventory()->getItemInHand();
        $block = $player->getLevel()->getBlock($touch);
        $e = new PlayerClick($this->mavoric, $player, $type, $item, $block, $touch, $face);
        $this->mavoric->broadcastEvent($e);
    }

    public function onPlayerMove(PlayerMoveEvent $event): void {
        $this->mavoric->broadcastEvent(new PlayerMove($this->mavoric, $event->getPlayer(), $event->getFrom(), $event->getTo()));
        return;
    }
    
    public function onPlayerTeleport(EntityTeleportEvent $event): void {
        if (!$event->getEntity() instanceof Player) {
            return;
        }

        $this->mavoric->broadcastEvent(new PlayerTeleport($this->mavoric, $event->getEntity(), $event->getFrom(), $event->getTo()));
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

        $calculatedTime = ceil(microtime(true) * 20) - $this->breakTimes[$player->getName()];
        unset($this->breakTimes[$player->getName()]);
        
        return $calculatedTime;
    }

}