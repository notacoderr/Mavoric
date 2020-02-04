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

use Bavfalcon9\Mavoric\Main;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\events\{
    MavoricEvent,
    player\PlayerMove
};

use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;

use pocketmine\event\player\PlayerMoveEvent;

// to complete....
class Speed implements Detection {
    /** @var Mavoric */
    private $mavoric;
    /** @var Array */
    private $timings = [];
    /** @var int */
    private $lastTick = 0;

    public function __construct(Mavoric $mavoric) {
        $this->mavoric = $mavoric;
    }

    public function onEvent(MavoricEvent $event): void {
        /** @var PlayerMove */
        if ($event instanceof PlayerMove) {
            $playerName = $event->getPlayer()->getName();
            $tickDiff = $event->getTick() - $this->lastTick;

            if (!isset($this->timings[$playerName])) {
                $this->timings[$playerName] = null;
            }

            $pos = $event->getPlayer()->getPosition();

            if ($event->getPlayer()->getPosition()->distance($pos) >= 0.2) {
                $event->alertStaff('Speed', 'Illegal movement, moved to quickly');
                $event->issueViolation(Mavoric::CHEATS['Speed']);
            }
        }
    }

    public function isEnabled(): Bool {
        return true;
    }
}
