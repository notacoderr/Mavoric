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
use Bavfalcon9\Mavoric\events\player\PlayerAttack;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;
use pocketmine\Server;

/* API CHANGE (Player) */

class AutoClicker implements Detection {
    private $mavoric;
    private $plugin;
    private $counters = [];

    public function __construct(Mavoric $mavoric) {
        $this->plugin = $mavoric->getPlugin();
        $this->mavoric = $mavoric;
    }

    public function onEvent(MavoricEvent $event): void {
        if ($event instanceof PlayerClick) {
            if ($event->isRightClick()) return;
            if ($event->clickedBlock()) return;

            $clicker = $event->getPlayer();
            $this->dueCheck($clicker);
        }

        if ($event instanceof PlayerAttack) {
            $clicker = $event->getAttacker();
            $this->dueCheck($clicker);
        }
    }

    public function isEnabled(): Bool {
        return true;
    }

    private function dueCheck($clicker) {
        $amount = (!$this->plugin->config->getNested('Cheats.AutoClicker.max-cps')) ? 24 : $this->plugin->config->getNested('Cheats.AutoClicker.max-cps');
        if (!is_numeric($amount)) $amount = 24;

        $player = $clicker->getName();
        $time = microtime(true);

        if (!isset($this->counters[$player])) {
            $this->counters[$player] = [];
        }

        array_unshift($this->counters[$player], $time);
        
        /*
        if (count($this->counters[$player]) >= 50) {
            $event->issueViolation(Mavoric::CHEATS['AutoClicker']);
            $event->sendAlert('AutoClicker', 'Interacted to quickly with over' . count($this->counters[$player]) . ' clicks per second');
            array_pop($this->counters[$player]);
        }*/

        if (!empty($this->counters[$player])) {
            $cps = count(array_filter($this->counters[$player], static function (float $t) use ($time) : bool {
                return ($time - $t) <= 1;
            }));
        }

        // AntiCheat checks
        if ($cps >= $amount) {
            $event->issueViolation(Mavoric::CHEATS['AutoClicker']);
            $event->sendAlert('AutoClicker', 'Interacted too quickly with ' . $cps . ' clicks per second');
        }
    }
}