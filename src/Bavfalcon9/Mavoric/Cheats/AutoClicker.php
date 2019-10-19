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

class AutoClicker implements Listener {
    private $mavoric;
    private $plugin;
    private $counters = [];

    public function __construct(Main $plugin, Mavoric $mavoric) {
        $this->plugin = $plugin;
        $this->mavoric = $mavoric;
    }

    public function onDamage(EntityDamageByEntityEvent $event) {

        $cause = $event->getCause();
        $clicker = $event->getDamager();
        $amount = 24;//$this->mavoric->getCheatConfig()->getCpsLimit();

        if ($cause !== 1) return;
        if (!$clicker instanceof Player) return;
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
            $this->mavoric->getFlag($clicker)->addViolation(Mavoric::AutoClicker, 1);
            $this->mavoric->messageStaff('detection', $clicker, "AutoClicker", " [Clicked {$data['clicks']} times in a second]");
            $event->setCancelled();
        }

        if ($data['time'] + 1 <= time()) {
            unset($this->counters[$player]);
            return;
        } else {
            $this->counters[$player]['clicks']++;
        }

    }
}