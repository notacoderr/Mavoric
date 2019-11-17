<?php
/***
 *      __  __                       _      
 *     |  \/  |                     (_)     
 *     | \  / | __ ___   _____  _ __ _  ___ 
 *     | |\/| |/ _` \ \ / / _ \| '__| |/ __|
 *     | |  | | (_| |\ V / (_) | |  | | (__ 
 *     |_|  |_|\__,_| \_/ \___/|_|  |_|\___|
 *                                          
 *   THIS CODE IS TO NOT BE REDISTRUBUTED
 *   @author MavoricAC
 *   @copyright Everything is copyrighted to their respective owners.
 *   @link https://github.com/Olybear9/Mavoric                                  
 */

namespace Bavfalcon9\Mavoric\Cheats;

use Bavfalcon9\Mavoric\Main;
use Bavfalcon9\Mavoric\Mavoric;

use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;
use pocketmine\Level\Position;

use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\block\BlockIds;

use pocketmine\{
    Player,
    Server
};

/* API CHANGE (Player) */

class NoClip implements Listener {
    private $mavoric;
    private $plugin;
    private $teleported = [];
    private $teleportQueue = [];
    private $ender_pearls = [];
    private $slabs = [182,181,126,157,44,43,139,109,67,114,108,180,128,106,209,208,175,176,177,167,144,127,105,96,94,78,101,90,85];

    public function __construct(Main $plugin, Mavoric $mavoric) {
        $this->plugin = $plugin;
        $this->mavoric = $mavoric;
    }

    public function onMove(PlayerMoveEvent $event) {
        $player = $event->getPlayer();
        $into = $player->getPosition();
        $intoA = [$into->getX(), $into->getY()+1, $into->getZ()];
        $intoB = [$into->getX(), $into->getY(), $into->getZ()];
        $level = $player->getLevel();
        $blockAtA = $level->getBlock(new Position($intoA[0], $intoA[1], $intoA[2], $level), false);
        $blockAtB = $level->getBlock(new Position($intoB[0], $intoB[1], $intoB[2], $level), false);
        if ($player->isSpectator()) return false;
        if ($blockAtA->isSolid() || $blockAtB->isSolid()) {
            $cache = $this->pearledAway($player);
            $cache2 = $this->hasTeleported($player);
            if ($blockAtA->isTransparent() || $blockAtB->isTransparent()) return false;
            if ($cache !== false) {
                $player->teleport($cache['pos']);
                $player->sendMessage(Mavoric::EPEARL_LOCATION_BAD);
                return false;
            }
            if ($cache2 !== false) {
                $player->teleport($cache2['pos']);
                $player->sendMessage(Mavoric::EPEARL_LOCATION_BAD);
                return false;
            }

            if (in_array($blockAtA->getId(), $this->slabs) || in_array($blockAtB->getId(), $this->slabs)) return false;
            if ($blockAtA->getId() === BlockIds::SAND || $blockAtB->getId() === BlockIds::SAND) {
                $y = $player->getLevel()->getHighestBlockAt($player->x, $player->z) + 1;
                $player->teleport(new Position($player->x, $y, $player->z, $player->getLevel()));
                $this->mavoric->getFlag($player)->addViolation(Mavoric::NoClip, 2);
                $this->mavoric->messageStaff('detection', $player, 'NoClip', ' [IN SAND BUT TELEPORTED]');
                return;
            }
            if ($blockAtA->getId() === BlockIds::GRAVEL || $blockAtB->getId() === BlockIds::GRAVEL) {
                $y = $player->getLevel()->getHighestBlockAt($player->x, $player->z) + 1;
                $player->teleport(new Position($player->x, $y, $player->z, $player->getLevel()));
                $this->mavoric->getFlag($player)->addViolation(Mavoric::NoClip, 2);
                $this->mavoric->messageStaff('detection', $player, 'NoClip', ' [IN GRAVEL BUT TELEPORTED]');
                return;
            }
            if ($blockAtA->getId() === BlockIds::ANVIL || $blockAtB->getId() === BlockIds::ANVIL) {
                $y = $player->getLevel()->getHighestBlockAt($player->x, $player->z) + 1;
                $player->teleport(new Position($player->x, $y, $player->z, $player->getLevel()));
                $this->mavoric->getFlag($player)->addViolation(Mavoric::NoClip, 2);
                $this->mavoric->messageStaff('detection', $player, 'NoClip', ' [IN ANVIL BUT TELEPORTED]');
                return;
            }
            $this->mavoric->getFlag($player)->addViolation(Mavoric::NoClip, 2);
            $this->mavoric->messageStaff('detection', $player, 'NoClip');

            if ($this->mavoric->isSuppressed(Mavoric::NoClip)) return $player->teleport($event->getFrom());
        }
    }

    public function onTeleport(EntityTeleportEvent $event) {
        $player = $event->getEntity();

        if (!$player instanceof Player) return;
        foreach ($this->teleportQueue as $p=>$t) {
            if ($t['time'] + 3 >= time()) unset($this->teleportQueue[$p]);
        }

        if (!isset($this->teleportQueue[$player->getName()])) $this->teleportQueue[$player->getName()] = [
                'time' => microtime(true),
                'pos' => $event->getFrom()
            ];
        if (!isset($this->ender_pearls[$player->getName()])) return; // No Enderpearl.
        else {
            $this->teleported[$player->getName()] = [
                'thrownAt' => $this->ender_pearls[$player->getName()],
                'elapsed' => microtime(true) - $this->ender_pearls[$player->getName()],
                'pos' => $event->getFrom()
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

    private function pearledAway($p) {
        $p = $p->getName();
        if (empty($this->teleported)) return false;
        if (!isset($this->teleported[$p])) return false; // wtf lol
        if (microtime(true) - $this->teleported[$p]['thrownAt'] >= 3) {
            // Three seconds passed since teleport, ignore, but still return teleport if within 5 seconds?
            $cache = $this->teleported[$p];
            unset($this->teleported[$p]);

            if (microtime(true) - $cache['thrownAt'] >= 5) return false;
            else {
                return $cache;
            }
        } else {
            return $this->teleported[$p];
        }
    }
    private function hasTeleported($p) {
        $p = $p->getName();
        // Purge cache
        foreach ($this->teleportQueue as $p=>$t) {
            if ($t['time'] + 2 >= time()) unset($this->teleportQueue[$p]);
        }
        if (empty($this->teleportQueue)) return false;
        if (!isset($this->teleportQueue[$p])) return false; // wtf lol
        if (microtime(true) - $this->teleportQueue[$p]['time'] >= 2) {
            $cache = $this->teleportQueue[$p];
            unset($this->teleportQueue[$p]);
            return false;
        } else {
            return $this->teleportQueue[$p];
        }
    }
}