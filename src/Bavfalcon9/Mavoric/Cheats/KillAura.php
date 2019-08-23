<?php

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

class KillAura implements Listener {
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
                $this->mavoric->messageStaff('§4§lMavoric §f> §r§b'.$damager->getName().'§7 detected for §bKill Aura§7.');
            }
        }


        /* Multi Aura Check */
        if (isset($this->queue[$damager->getName()])) {

            $multiAura = $this->queue[$damager->getName()];
            if (!in_array($entity->getName(), $multiAura['targets'])) array_push($this->queue[$damager->getName()]['targets'], $entity->getName());    
            if (sizeof($multiAura['targets']) >= 2 && ($multiAura['time'] + 0.25) >= time()) {
                $f = $this->mavoric->getFlag($damager)->getTotalViolations();
                $this->mavoric->getFlag($damager)->addViolation(Mavoric::KillAura);
                $this->mavoric->startTask($damager, 90);
                //$this->mavoric->messageStaff('§4§lMavoric §f> §r§b'.$damager->getName().'§7 detected for §bKill Aura§7.');
            }
            if (($multiAura['time'] + 0.35) <= time()) {
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

        

        /* Kill Aura Check */
        if ($this->dueHeadCheck($damager, $entity) === true) {
            $this->mavoric->getFlag($damager)->addViolation(Mavoric::KillAura);
        }

    }

    private function dueHeadCheck($dam, $p) {
        $dRot = $dam->getYaw();
        $dTilt = $dam->getPitch();
        $dx = $dam->getX();
        $dy = $dam->getY();
        $dz = $dam->getZ();

        $pRot = $p->getYaw();
        $pTilt = $p->getPitch();
        $px = $p->getX();
        $py = $p->getY();
        $pz = $p->getZ();
        $rez1 = $dx - $px;
        $rez2 = $dy - $py;
        $rez3 = $dz - $pz;
        //$dam->sendMessage("Rotation: $dRot\nTilt: $dTilt\n"."Location: $dx, $dy, $dz");
        //$dam->sendMessage("Rotation: $pRot\nTilt: $pTilt\n"."Location: $px, $py, $pz");
        return false;
    }
}