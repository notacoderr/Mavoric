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

namespace Bavfalcon9\Mavoric\events\player;

use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\level\Position;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\events\MavoricEvent;

class PlayerMove extends MavoricEvent {
    /** @var Player */
    private $player;
    /** @var Position */
    private $from;
    /** @var Position */
    private $to;

    public function __construct(Mavoric $mavoric, Player $player, Position $from, Position $to) {
        parent::__construct($mavoric, $player);
        $this->player = $player;
        $this->from = $from;
        $this->to = $to;
    }

    public function isMoved(): Bool {
        if (abs($this->getDistance()) >= 1) {
            return true;
        } else {
            return false;
        }
    }

    public function getDistance(): float {
        return $this->from->distance($this->to);
    }

    public function getFrom(): ?Vector3 {
        return $this->from;
    } 

    public function getTo(): ?Vector3 {
        return $this->to;
    }
}