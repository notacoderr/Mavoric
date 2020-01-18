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

    public function __construct(Mavoric $mavoric, Player $target) {
        $this->mavoric = $mavoric;
        $this->player = $target;
    }

    public function setPMEvent($event) {
        $this->eventData = $event;
    }

    public function cancel(Bool $val=true): Bool {
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

    public function setCheating(Bool $val): Bool {
        $this->isCheating = $val;
        return $val;
    }

    public function getCheating(): Bool {
        return $this->isCheating;
    }

    public function issueViolation(int $cheat, int $count = 1): Flag {
        $flag = $this->mavoric->getFlag($this->player);
        $flag->addViolation($cheat, $count);
        return $flag;
    }

    public function sendAlert(String $cheat, String $details): Bool {
        $this->mavoric($this->player, $cheat, $details);
        return true;
    }
}