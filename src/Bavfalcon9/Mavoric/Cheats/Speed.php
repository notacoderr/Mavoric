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

use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\{
    Player,
    Server
};

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

class Speed implements Listener {
    private $mavoric;
    private $plugin;
    private $defaults = [];
    private $timings = [];
    private const VOID = -9;

    public function __construct(Main $plugin, Mavoric $mavoric) {
        $this->plugin = $plugin;
        $this->mavoric = $mavoric;
    }
    

    public function onMove(PlayerMoveEvent $event) {
        // I took this from my SpeedTest and I'm going to make it more movement based
        $player = $event->getPlayer();
        $block_below = $this->checkBlock($player);
        if (isset($this->timings[$player->getName()])) {
            if ($block_below === self::VOID) return;
            $pos = new LastPosition($player->getPosition());

            if (!$this->timings[$player->getName()]['previous']->isWholeMovement($pos)) return;

            $this->timings[$player->getName()]['blocks']++;
            $this->timings[$player->getName()]['previous'] = $pos;

            if ($this->timings[$player->getName()]['start'] + 2 <= microtime(true)) {
                // speed effect id: 1
                $speed = (!$player->hasEffect(1)) ? 0 : $player->getEffect(1)->getAmplifier() + 1;
                $distance = $this->timings[$player->getName()]['blocks'];
                $time = microtime(true) - $this->timings[$player->getName()]['start'];
                $bps = $distance / $time;

                if (!self::isNormalSpeed($distance, $time, $speed)) {
                    if ($this->mavoric->isSuppressed(Mavoric::Speed)) $event->setCancelled();
                    $this->mavoric->getFlag($player)->addViolation(Mavoric::Speed);
                    $this->mavoric->messageStaff('detection', $player, 'Speed', " [Traveled {$bps} blocks in a second]");
                }

                unset($this->timings[$player->getName()]);
            }
            return;
        } else {
            if ($block_below === SELF::VOID) return;
            $this->timings[$player->getName()] = [
                'blocks' => 0,
                'start' => microtime(true),
                'previous' => new LastPosition($player->getPosition())
            ];
            return;
        }
    }

    private function checkBlock(Player $p) {
        if ($p->y < 0) return self::VOID;
        if ($p->getY() - 1 < 0) return self::VOID;
        return $p->getLevel()->getBlockAt($p->getX(), $p->getY()-1, $p->getZ());
    }

    public static function isNormalSpeed(Float $distance, Float $time, Float $speedEffect=1): ?Bool {
        $speed = $distance / $time;
        return self::getSpeedEffectBPS($speedEffect) >= $speed;
    }

    public static function getSpeedEffectBPS(int $effect) {
        // AVG Speed BPS: 5
        $blocks = 1.3675 * $effect;
        return $blocks + 5;
    }
}
