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

namespace Bavfalcon9\Mavoric\events\packet;

use pocketmine\Player;
use pocketmine\network\mcpe\protocol\DataPacket;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\events\MavoricEvent;

class PacketRecieve extends MavoricEvent {
    /** @var DataPacket */
    private $packet;
    /** @var Player */
    private $player;
    /** @var Bool */
    private $inBatch;

    public function __construct($e, Mavoric $mavoric, Player $player, DataPacket $packet, Bool $inBatch = false) {
        parent::__construct($e, $mavoric, $player);
        $this->packet = $packet;
    }

    public function isPartOfBatch(): Bool {
        return $this->inBatch;
    }

    public function getPacket(): DataPacket {
        return $this->packet;
    }
}