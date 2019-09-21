<?php

namespace Bavfalcon9\Mavoric\misc;
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
use pocketmine\Player;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\Listener;
// Queue for later.
class LastPosition {
    private $pos;
    public function __construct($pos) {
        $this->pos = $pos;
    }
    public function getXYZ() {
        return [$this->pos->getX(), $this->pos->getY(), $this->pos->getZ()];
    }
    public function isWholeMovement(LastPosition $last) {
        $last = $last->getXYZ();
        $pos = $this->getXYZ();
        return !(floor($last[0]) === floor($pos[0]) && floor($last[2]) === floor($pos[2]));
    }
}
class SpeedTest implements Listener {
    private $plugin;
    private $timings = [];
    private const VOID = -9;

    public function __construct($pl) {
        $this->plugin = $pl;
    }

    public function onMove(PlayerMoveEvent $event) {
        $player = $event->getPlayer();
        $start = [35, 5];
        $stop = [35, 14];
        $block_below = $this->checkBlock($player);
        if (isset($this->timings[$player->getName()])) {
            if ($block_below === self::VOID) return;
            $pos = new LastPosition($player->getPosition());

            if ($block_below->getId() === $stop[0] && $block_below->getDamage() === $stop[1]) return $this->postTimings($player);
            if (!$this->timings[$player->getName()]['previous']->isWholeMovement($pos)) return;
            $this->timings[$player->getName()]['blocks']++;
            $this->timings[$player->getName()]['previous'] = $pos;

            return;
        } else {
            if ($block_below->getId() === $start[0] && $block_below->getDamage() === $start[1]) {
                $this->timings[$player->getName()] = [
                    'blocks' => 0,
                    'start' => microtime(true),
                    'previous' => new LastPosition($player->getPosition())
                ];
                $player->sendMessage('§aTEST HAS STARTED');
            }
            return;
        }
    }

    private function checkBlock(Player $p) {
        if ($p->getY() - 1 < 0) return self::VOID;
        return $p->getLevel()->getBlockAt($p->getX(), $p->getY()-1, $p->getZ());
    }

    public function postTimings(Player $p) {
        if (!isset($this->timings[$p->getName()])) return;
        $times = $this->timings[$p->getName()];
        unset($this->timings[$p->getName()]);
        $diff = microtime(true) - $times['start'];
        var_dump($diff);
        return $p->sendMessage('§cTIME: ' . floor($diff) . ' seconds | TRAVELED: ' . $times['blocks'] . ' | SPEED: ' . $times['blocks'] / $diff . ' Blocks/s');
    }


}