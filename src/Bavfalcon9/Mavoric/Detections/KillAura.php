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
use Bavfalcon9\Mavoric\events\MavoricEvent;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;

use pocketmine\event\entity\{
    EntityDamageByEntityEvent
};
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\math\Vector3;

use Bavfalcon9\Mavoric\entity\SpecterPlayer;


/* API CHANGE (Player) */

class KillAura implements Detection {
    private $mavoric;

    public function __construct(Mavoric $mavoric) {
        $this->mavoric = $mavoric;
    }

    public function onEvent(MavoricEvent $event) {
        if ($event->getType() !== MavoricEvent::PLAYER_ATTACK) {
            return;
        }

        $victim = $event->getPMEvent()->getEntity();
        $damager = $event->getPlayer();

        $AABB = $victim->getBoundingBox();
        $eyes = new Vector3();
    }

    public function isEnabled(): Bool {
        return false;
    }
}