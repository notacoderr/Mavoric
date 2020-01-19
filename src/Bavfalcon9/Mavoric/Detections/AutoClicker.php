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

    public function onEvent(MavoricEvent $event) {
        if (!$event instanceof PlayerClickEvent) return;

        $clicker = $event->getPlayer();

        $amount = (!$this->plugin->config->getNested('Cheats.AutoClicker.max-cps')) ? 24 : $this->plugin->config->getNested('Cheats.AutoClicker.max-cps');
        if (!is_numeric($amount)) $amount = 22;

        $player = $clicker->getName();
        if (!isset($this->counters[$player])) {
            $this->counters[$player] = [
                'clicks' => 1,
                'time' => time()
            ];
        }

        $data = $this->counters[$player];
        // Data checks.
        if ($data['time'] + 10 <= time()) {
            unset($this->counters[$player]);
            return;
        }
        // AntiCheat checks
        if ($data['clicks'] >= $amount) {
            $event->issueViolation(Mavoric::AutoClicker);
            $event->issueMessage($clicker, 'AutoClicker', 'Clicked faster than usual with ' . $data['clicks'] . ' clicks per second');
        }

        if ($data['time'] + 1 <= time()) {
            unset($this->counters[$player]);
            return;
        } else {
            $this->counters[$player]['clicks']++;
        }

    }

    public function isEnabled(): Bool {
        return true;
    }
}