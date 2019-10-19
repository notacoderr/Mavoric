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

        /* Kill Aura Check */
        if ($this->dueHeadCheck($damager, $entity) === true) {
            $this->mavoric->getFlag($damager)->addViolation(Mavoric::KillAura);
        }

        if ($this->dueSnapCheck($damager, $entity) === true) {
           $this->mavoric->getFlag($damager)->addViolation(Mavoric::KillAura); 
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
        $this->canSee($dam, $p);
        /*
        if (!$this->canSee($dam, $p)) return true; // Positive.
        else {
            
        }*/
        //$dam->sendMessage("Rotation: $dRot\nTilt: $dTilt\n"."Location: $dx, $dy, $dz");
        //$dam->sendMessage("Rotation: $pRot\nTilt: $pTilt\n"."Location: $px, $py, $pz");
        return false;
    }

    private function canSee($dam, $p) {
        $expectedLook = $this->getRotationTo($dam, $p);
        $actualLook = [$dam->getYaw(), $dam->getPitch()];
        if ($expectedLook[0] !== $dam->getYaw() && $expectedLook[1] !== $dam->getPitch()) return false;
        else return true;
    }
    private function getRotationTo($dam, $p) {
        // 2.524147334115 difference?
        $yaw = $dam->getYaw();
        $pitch = $dam->getPitch();
        $horizontal = sqrt(($p->getX() - $dam->getX()) ** 2 + ($p->getZ() - $dam->getZ()) ** 2);
        $vertical = $dam->getY() - $p->getY() + $p->getEyeHeight();
        $pitch = -atan2($vertical, $horizontal) / M_PI * 180;
        $xDist = $p->getX() - $dam->getX();
        $zDist = $p->getZ() - $dam->getZ();
        $yaw = atan2($zDist, $xDist) / M_PI * 180 - 90;
        if($yaw < 0){
            $yaw += 360.0;
        }

        return [$yaw, $pitch];
    }
    private function dueSnapCheck($dam, $ent) {

    }
}