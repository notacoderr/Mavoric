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

namespace Bavfalcon9\Mavoric\events;

use pocketmine\Player;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\misc\Flag;

class MavoricEvent {
    private $mavoric;
    private $type;
    private $eventData = null;
    private $player;
    private $isCancelled = false;
    private $isCheating = false;

    public function __construct($e, Mavoric $mavoric, Player $target) {
        $this->mavoric = $mavoric;
        $this->player = $target;
        $this->eventData = $e;
    }

    public function cancel(Bool $val = true): Bool {
        if ($this->mavoric->getTpsCheck()->isHalted()) {
            return false;
        }
        
        $this->eventData->setCancelled($val);
        $this->isCancelled = $val;
        return $val;
    }

    public function getPlayer(): Player {
        return $this->player;
    }

    public function getPMEvent() {
        return $this->eventData;
    }

    public function getServer(): Server {
        return $this->mavoric->getServer();
    }

    public function getTick(): int {
        return $this->mavoric->getServer()->getTick();
    }

    public function setCheating(Bool $val): Bool {
        $this->isCheating = $val;
        return $val;
    }

    public function getCheating(): Bool {
        return $this->isCheating;
    }

    public function issueViolation(int $cheat, int $count = 1): Flag {
        if ($this->mavoric->getTpsCheck()->isHalted()) {
            return $this->mavoric->getFlag(null);
        }

        $flag = $this->mavoric->getFlag($this->player);
        $flag->addViolation($cheat, $count);
        $this->cancel(true); // SUPPRESSION FOR EACH ALERT JAJAJAJA

        return $flag;
    }

    public function alertStaff(String $cheat, String $details): Bool {
        #$this->mavoric->getPlugin()->getLogger()->notice('DEPRECATED METHOD CALLED -> MavoricEvent::alertStaff()');
        if ($this->mavoric->getTpsCheck()->isHalted()) {
            return false;
        }
        return $this->sendAlert($cheat, $details);
    }

    public function sendAlert(String $cheat, String $details): Bool {
        if ($this->mavoric->getTpsCheck()->isHalted()) {
            return false;
        }
        $cheat = Mavoric::CHEATS[$cheat];
        $this->mavoric->alertStaff($this->player, $cheat, $details);
        return true;
    }
}