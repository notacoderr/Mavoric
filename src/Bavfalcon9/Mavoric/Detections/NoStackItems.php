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


 // Pretty much a big fuck you, to all hackers, oh sweety.
namespace Bavfalcon9\Mavoric\Detections;

use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\events\MavoricEvent;
use Bavfalcon9\Mavoric\events\packet\PacketRecieve;
use Bavfalcon9\Mavoric\events\player\InventoryTransaction;
use pocketmine\Player;
use pocketmine\inventory\ArmorInventory;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;

class NoStackItems implements Detection {
    /** @var Mavoric */
    private $mavoric;

    public function __construct(Mavoric $mavoric) {
        $this->mavoric = $mavoric;
    }

    public function onEvent(MavoricEvent $event): void {
        if (!$event instanceof InventoryTransaction) {
            return;
        }

        if ($event->hasIllegalStackedItem()) {
            $event->issueViolation(Mavoric::CHEATS['NoStackItems'], 50);
            $event->sendAlert('NoStackItems', 'Illegal inventory transaction, Stacked non-stackable item.');
        }
    }

    public function isEnabled(): Bool {
        return true;
    }
}