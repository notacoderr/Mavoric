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

namespace Bavfalcon9\Mavoric\Detections;

use Bavfalcon9\Mavoric\Main;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\events\MavoricEvent;
use Bavfalcon9\Mavoric\events\player\PlayerClick;
use Bavfalcon9\Mavoric\events\player\PlayerMove;
use Bavfalcon9\Mavoric\events\player\PlayerTeleport;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;
use pocketmine\Level\Position;
use pocketmine\block\BlockIds;

use pocketmine\{
    Player,
    Server
};

/* API CHANGE (Player) */

class NoClip implements Detection {
    private $mavoric;
    private $plugin;
    private $teleported = [];
    private $teleportQueue = [];
    private $ender_pearls = [];
    private $slabs = [182,181,126,157,44,43,139,109,67,114,108,180,128,106,209,208,175,176,177,167,144,127,105,96,94,78,101,90,85];

    public function __construct(Mavoric $mavoric) {
        $this->plugin = $mavoric->getPlugin();
        $this->mavoric = $mavoric;
    }

    public function onEvent(MavoricEvent $event): void {
        /**
         * @var PlayerMove event
         */
        if ($event instanceof PlayerMove) {
            if (!$event->isMoved()) return;
            $blockA = $event->getBlocks()[1];
            $blockB = $event->getBLocks()[0];

            if ($blockA->isSolid() || $blockB->isSolid()) {
                if ($event->isTeleport()) {
                    return;
                }
                if ($this->pearledAway($event->getPlayer())) {
                    $player->teleport($this->pearledAway($event->getPlayer())['pos']);
                    $player->sendMessage(Mavoric::EPEARL_LOCATION_BAD);
                    return;
                }

                if (in_array($blockA->getId(), $this->slabs) || in_array($blockB->getId(), $this->slabs)) {
                    return;
                }

                if ($blockA->getId() === BlockIds::SAND || $blockB->getId() === BlockIds::SAND) {
                    $y = $event->getNextAirBlock()->y;
                    $player->teleport(new Position($player->x, $y, $player->z, $player->getLevel()));
                    return;
                }
                if ($blockA->getId() === BlockIds::GRAVEL || $blockB->getId() === BlockIds::GRAVEL) {
                    $y = $event->getNextAirBlock()->y;
                    $player->teleport(new Position($player->x, $y, $player->z, $player->getLevel()));
                    return;
                }
                if ($blockA->getId() === BlockIds::ANVIL || $blockB->getId() === BlockIds::ANVIL) {
                    $y = $event->getNextAirBlock()->y;
                    $player->teleport(new Position($player->x, $y, $player->z, $player->getLevel()));
                    return;
                }
                
                $event->issueViolation(Mavorics::CHEATS['NoClip']);
                $event->sendAlert(Mavoric::CHEATS['NoClip'], 'Illegal movement, player moved while in a block');
            }
        }

        /**
         * @var PlayerTeleport event
         */
        if ($event instanceof PlayerTeleport) {
            $player = $event->getPlayer();

            foreach ($this->teleportQueue as $p=>$t) {
                if ($t['time'] + 3 >= time()) unset($this->teleportQueue[$p]);
            }

            if (!isset($this->teleportQueue[$player->getName()])) $this->teleportQueue[$player->getName()] = [
                    'time' => microtime(true),
                    'pos' => $event->getFrom()
                ];
            if (!isset($this->ender_pearls[$player->getName()])) {
                return;
            } else {
                $this->teleported[$player->getName()] = [
                    'thrownAt' => $this->ender_pearls[$player->getName()],
                    'elapsed' => microtime(true) - $this->ender_pearls[$player->getName()],
                    'pos' => $event->getFrom()
                ];

                unset($this->ender_pearls[$player->getName()]);
                return;
            }
        }

        /**
         * @var PlayerClick event
         */
        if ($event instanceof PlayerClick) {
            $player = $event->getPlayer();

            if ($event->threwEnderPearl()) {
                $this->ender_pearls[$player->getName()] = microtime(true);   
            }
            return;
        }
    }

    /** 
     * @return Bool
     */
    public function isEnabled(): Bool {
        return true;
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