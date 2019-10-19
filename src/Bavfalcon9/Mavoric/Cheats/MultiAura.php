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

use pocketmine\event\entity\{
    EntityDamageByEntityEvent
};
use pocketmine\{
    Player,
    Server
};
use Bavfalcon9\Mavoric\entity\SpecterPlayer;


/* API CHANGE (Player) */

class MultiAura implements Listener {
    private $mavoric;
    private $plugin;
    private $queue = [];

    public function __construct(Main $plugin, Mavoric $mavoric) {
        $this->plugin = $plugin;
        $this->mavoric = $mavoric;
    }

    public function onDamage(EntityDamageByEntityEvent $event) {
        /**
         * Multi-Aura Is still beta, please excuse the bugs with it :)
         */
        $entity = $event->getEntity();
        $damager = $event->getDamager();

        // Do player check.
        //if (!$entity instanceof Player) return;
        if (!$damager instanceof Player) return;

        if ($this->mavoric->hasTaskFor($damager)) {
            if ($entity instanceof SpecterPlayer) {
                if (!$this->mavoric->getTaskFor($damager) === $entity->getName()) return;
                $this->mavoric->getFlag($damager)->addViolation(Mavoric::MultiAura);
                $this->mavoric->messageStaff('detection', $damager, 'MultiAura');
                //$this->mavoric->messageStaff('§4§lMavoric §f> §r§b'.$damager->getName().'§7 detected for §bKill Aura§7.');
            }
        }

        /* Multi Aura Check */
        if (isset($this->queue[$damager->getName()])) {

            $multiAura = $this->queue[$damager->getName()];
            $distance = $this->getDistance($damager, $entity);
            if (($distance[0] <= 1.5 || $distance[1] <= 1.5) && ($distance[0] >= -1.5 || $distance[1] >= -1.5)) return;
            if (!in_array($entity->getName(), $multiAura['targets'])) array_push($this->queue[$damager->getName()]['targets'], $entity->getName());    
            if (sizeof($multiAura['targets']) >= 2 && ($multiAura['time'] + 0.25) >= time()) {
                $f = $this->mavoric->getFlag($damager)->getTotalViolations();
                $this->mavoric->getFlag($damager)->addViolation(Mavoric::MultiAura);
                $this->mavoric->startTask($damager, 3); # - DO NOT RUN TASK ON PRODUCTION SERVERS, THIS FEATURE IS IN DEV
                $this->mavoric->messageStaff('detection', $damager, 'MultiAura');
            }
            if (($multiAura['time'] + 0.25) <= time()) {
                $this->queue[$damager->getName()] = [
                    "time" => time(),
                    "targets" => []
                ];
            }
        } else {
            $this->queue[$damager->getName()] = [
                "time" => time(),
                "targets" => [$entity->getName()]
            ];
        }
    }
    private function getDistance($dam, $p) {
        $dx = $dam->getX();
        $dy = $dam->getY();
        $dz = $dam->getZ();
        $px = $p->getX();
        $py = $p->getY();
        $pz = $p->getZ();

        $distanceX = sqrt(pow(($px - $dx), 2) + pow(($py - $dy), 2));
        $distanceZ = sqrt(pow(($pz - $dz), 2) + pow(($py - $dy), 2));
        return [$distanceX, $distanceZ];
    }
}