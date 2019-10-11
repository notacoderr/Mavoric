<?php

namespace Bavfalcon9\Mavoric\Cheats;

use Bavfalcon9\Mavoric\Main;
use Bavfalcon9\Mavoric\Mavoric;

use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Player;
use pocketmine\Server;

/* API CHANGE (Player) */

class Reach implements Listener {
    private $mavoric;
    private $plugin;
    private $ender_pearls;
    private $teleported = [];
    private $teleportQueue = [];
    public function __construct(Main $plugin, Mavoric $mavoric) {
        $this->plugin = $plugin;
        $this->mavoric = $mavoric;
    }

    public function onDamage(EntityDamageByEntityEvent $event) {
        $entity = $event->getEntity();
        $damager = $event->getDamager();

        if ($event instanceof EntityDamageByChildEntityEvent) return;
        if (!$damager instanceof Player) return;
        if ($entity instanceof Player) {
            if ($entity->getPing() >= 400) return;
            if ($damager->getPing() >= 450) return;
        }

        if ($this->checkReach($damager, $entity) !== false) {
            if ($this->pearledAway($entity) === true) return;
            if ($this->pearledAway($damager) === true) return;
            if ($this->hasTeleported($entity) === true) return;
            if ($this->hasTeleported($damager) === true) return;
            if (!$damager->isCreative()) {
                    $total_reach = $this->checkReach($damager, $entity);
                    $this->mavoric->getFlag($damager)->addViolation(Mavoric::Reach);
                    $this->mavoric->messageStaff('detection', $damager, 'Reach', " [{$total_reach} blocks]");
            }
        }
    }

    public function onTeleport(EntityTeleportEvent $event) {
        $player = $event->getEntity();

        if (!$player instanceof Player) return;
        // Causes? Assume enderpearl thrown. Time to test :)
        // Purge old.
        foreach ($this->teleportQueue as $p=>$t) {
            if ($t + 3 >= time()) unset($this->teleportQueue[$p]);
        }

        if (!isset($this->teleportQueue[$player->getName()])) $this->teleportQueue[$player->getName()] = microtime(true);
        if (!isset($this->ender_pearls[$player->getName()])) return; // No Enderpearl.
        else {
            $this->teleported[$player->getName()] = [
                'thrownAt' => $this->ender_pearls[$player->getName()],
                'elapsed' => microtime(true) - $this->ender_pearls[$player->getName()]
            ]; // Possible add stuff?
            unset($this->ender_pearls[$player->getName()]);

            return;
        }
    }
    
    public function onInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $action = $event->getAction(); //check this
        if ($item->getId() !== 368) return;
        if ($event->isCancelled()) return;
        // Expect an enderpearl throw
        $this->ender_pearls[$player->getName()] = microtime(true);
        return;
    }

    private function checkReach($dam, $p) {
        $dx = $dam->getX();
        $dy = $dam->getY();
        $dz = $dam->getZ();
        $px = $p->getX();
        $py = $p->getY();
        $pz = $p->getZ();

        $distanceX = sqrt(pow(($px - $dx), 2) + pow(($py - $dy), 2));
        $distanceZ = sqrt(pow(($pz - $dz), 2) + pow(($py - $dy), 2));

        if ($dam->isCreative()) return false;
        if (abs($distanceX) >= 6.23 || abs($distanceZ >= 6.23)) return (abs($distanceX) > abs($distanceZ)) ? abs($distanceX) : abs($distanceZ);
        return false;
    }

    private function pearledAway($p) {
        $p = $p->getName();
        if (empty($this->teleported)) return false;
        if (!isset($this->teleported[$p])) return false; // wtf lol
        if (microtime(true) - $this->teleported[$p]['thrownAt'] >= 3) {
            // Three seconds passed since teleport, ignore, but still return teleport if within 5 seconds?
            $cache = $this->teleported[$p]['thrownAt'];
            unset($this->teleported[$p]);

            if (microtime(true) - $cache >= 5) return false;
            else {
                return true;
            }
        } else {
            return true;
        }
    }

    private function hasTeleported($p) {
        $p = $p->getName();
        // Purge cache
        foreach ($this->teleportQueue as $p=>$t) {
            if ($t + 2 >= time()) unset($this->teleportQueue[$p]);
        }
        if (empty($this->teleportQueue)) return false;
        if (!isset($this->teleportQueue[$p])) return false; // wtf lol
        if (microtime(true) - $this->teleportQueue[$p] >= 2) {
            $cache = $this->teleportQueue[$p];
            unset($this->teleportQueue[$p]);
            return false;
        } else {
            return true;
        }
    }

    
}