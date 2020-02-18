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
use pocketmine\item\Armor;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\inventory\Inventory;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\events\MavoricEvent;

/**
 * Called when a player does anything in their inventory
 */
class InventoryTransaction extends MavoricEvent {
    /** @var Player */
    private $player;
    /** @var InventoryTransaction */
    private $transaction;
    /** @var Inventory - Player inventory */
    private $inventory;

    public function __construct($e, Mavoric $mavoric, Player $player, $transaction) {
        parent::__construct($e, $mavoric, $player);
        $this->transaction = $transaction;
        $this->inventory = $transaction->getSource()->getInventory();
    }

    public function getFirstInventory(): ?Inventory {
        return $this->transaction->getInventories()[0] ?? null;
    }

    public function getInventory(int $num = 1): ?Inventory {
        $num -= 1;
        $inventories = $this->transaction->getInventories();
        
        if (!isset($inventories[$num])) return null;

        return $inventories[$num];
    }

    public function getPlayerInventory(): Inventory {
        return $this->inventory;
    }

    public function hasIllegalStackedItem(): Bool {
        $items = $this->inventory->getContents();

        foreach ($items as $item) {
            $allowed = $item->getMaxStackSize();
            $actual = $item->getCount();

            if ($item instanceof Armor) continue;

            if ($actual > $allowed) {
                return true;
            }
        }

        return false;
    }
}