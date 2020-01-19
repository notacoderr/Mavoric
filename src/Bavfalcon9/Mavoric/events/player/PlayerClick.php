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
use pocketmine\item\EnderPearl;
use pocketmine\math\Vector3;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\events\MavoricEvent;

/**
 * Called when a player Clicks/Taps something.
 * @deprecated Deprecated until api interaction is fixed.
 */
class Event extends MavoricEvent {
    /** @var Player */
    private $player;
    /** @var Item */
    private $item;
    /** @var Block */
    private $block;
    /** @var Vector3 */
    private $location;
    /** @var Bool */
    private $rightClick = false;
    /** @var Bool */
    private $leftClick = false;
    /** @var Bool */
    private $clickedAir = false;
    /** @var Bool */
    private $clickedEnt = false;
    /** @var Bool */
    private $clickedBlk = false;

    public function __construct(Mavoric $mavoric, Player $player, int $type, Item $item, Block $block, Vector3 $location, int $face) {
        parent::__construct($mavoric, $player);
        $this->player = $player;
        $this->block = $block;
        $this->item = $item;
        $this->location = $location;
        $this->face = $face;

        switch ($type) {
            case 0:
                $this->leftClick = true;
                $this->clickedBlk = true;
                break;
            case 1:
                $this->rightClick = true;
                $this->clickedBlk = true;
                default;
                break;
            case 2:
                $this->leftClick = true;
                $this->clickedAir = true;
                break;
            case 3:
                $this->rightClick = true;
                $this->clickedAir = true;
                break;
            case 4:
                $this->clickedEnt = true;
                $this->leftClick = true;
                $this->rightClick = true;
                break;
        }
    }

    public function isAir(): Bool {
        return $this->clickedAir;
    }

    public function isEntity(): Bool {
        return $this->clickedEnt;
    }

    public function isLeftClick(): Bool {
        return $this->leftClick;
    }

    public function isRightClick(): Bool {
        return $this->rightClick;
    }

    public function getBlock(): Block {
        return $this->block;
    }

    public function getItem(): Item {
        return $this->item;
    }

    public function getLocation(): Vector3 {
        return $this->location;
    }

    public function getFace(): int {
        return $this->face;
    }

    /** 
     * Needs to be updated.
     * @deprecated
     */
    public function thewEnderPearl(): Bool {
        $inventory = $this->player->getInventory();
        return ($this->isRightClick() && $inventory->getItemInHand() instanceof EnderPearl);
    }
}