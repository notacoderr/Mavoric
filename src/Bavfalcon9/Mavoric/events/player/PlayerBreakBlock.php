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
use pocketmine\math\Vector3;
use pocketmine\item\Item;
use pocketmine\block\Block;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\events\MavoricEvent;

class PlayerBreakBlock extends MavoricEvent {
    /** @var Player */
    private $player;
    /** @var Block */
    private $block;
    /** @var int */
    private $timeTaken;

    public function __construct(Mavoric $mavoric, Player $player, Block $block, int $timeTaken) {
        parent::__construct($mavoric, $player);
        $this->player = $player;
        $this->block = $block;
        $this->timeTaken = $timeTaken;
    }

    public function getUsedTool(): ?Item {
        $inventory = $this->player->getInventory();
        return $this->player->getItemInHand();
    }

    public function getDistance(): float {
        $pos1 = $this->player->getPosition() ?? new Vector3(0,0,0);
        $pos2 = $this->block->asVector3() ?? new Vector3(0,0,0);
        return $pos1->distance($pos2);
    }

    public function getBlock(): Block {
        return $this->block;
    }

    public function getTime(): int {
        return $this->timeTaken;
    }
}