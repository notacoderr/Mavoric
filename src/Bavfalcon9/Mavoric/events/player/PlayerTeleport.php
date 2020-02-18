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

class PlayerTeleport extends MavoricEvent {
    /** @var Player */
    private $player;
    /** @var Position */
    private $from;
    /** @var Position */
    private $to;

    public function __construct($e, Mavoric $mavoric, Player $player, Position $from, Position $to) {
        parent::__construct($e, $mavoric, $player);
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

    /** To Do: Since teleports are only issued by the server, check if the client sent the request or not. */
    public function isAuthoritative(): Bool {
        return true;
    }

    public function getNextAirBlock(): Vector3 {
        $level = $this->player->getLevel();
        $pos = $this->player->getPosition();
        $max = 256;

        if ($pos->y >= $max) {
            return new Vector3($pos->x, $max, $pos->z);
        }

        for ($test = $pos; $test < $max; $test++) {
            $block = $level->getBlockAt($pos->x, $test + 1, $pos->z);
            if ($block->getId() === 0) {
                return new Vector3($pos->x, $test + 1, $pos->z);
            }
        }

        return $pos;
    }
}