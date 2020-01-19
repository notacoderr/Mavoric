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

namespace Bavfalcon9\Mavoric\Detections;

use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\events\MavoricEvent;
use Bavfalcon9\Mavoric\events\player\PlayerMove;
use pocketmine\Player;

use pocketmine\block\{
    StillWater, Water, WaterLily
};

class Jesus extends Detection {
    /** @var Mavoric */
    private $mavoric;

    public function __construct(Mavoric $mavoric) {
        $this->mavoric = $mavoric;
    }

    public function onEvent(MavoricEvent $event): void {
        if (!$event instanceof PlayerMove) {
            return;
        }

        $block_below = $event->getBlockNearPlayer(0, -1, 0);
        $block_above = $event->getBlockNearPlayer(0, 1, 0);

        if ($block_below instanceof StillWater || $block_below instanceof Water || $block_below instanceof WaterLily) {
           if ($block_above->getId() === 0) {
                $event->issueViolation(Mavoric::CHEATS['Jesus'], 1);
                $event->alertStaff('Jesus', 'Illegal movement, walked on water');
                return;
           } 
        }

        return;
    }

    public function isEnabled(): Bool {
        return true;
    }
}