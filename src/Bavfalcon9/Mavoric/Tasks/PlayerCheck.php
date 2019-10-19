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

namespace Bavfalcon9\Mavoric\Tasks;
use pocketmine\scheduler\Task;
use Bavfalcon9\Mavoric\Mavoric;
use pocketmine\Player;
use pocketmine\Level\Position;
use pocketmine\math\Vector3;
use Bavfalcon9\Mavoric\misc\Utils;

class PlayerCheck extends Task {
    private $mav;
    private $timer;
    private $target;
    private $npc; // Fake player entity, npc is easier to refer too.
    private $stopAt;
    private $circle = [];
    private $hasTeleported = false;

    public function __construct(Mavoric $mavoric, int $time, Player $target, String $fakePlayer) {
        /**
         * TODO: Allow types, EX: multiaura, killaura, etc
         */
        $this->mav = $mavoric;
        $this->stopAt = $time * 20;
        $this->target = $target;
        $this->npc = $fakePlayer;
    }

    public function onRun(int $tick) {
        try {
            $this->timer++;

            // Check timer
            if ($this->timer >= $this->stopAt) {
                $this->mav->killTask($this->npc);
                $fakePlayer = $this->mav->getPlugin()->getServer()->getPlayer($this->npc);
                if ($fakePlayer === null) return;
                else {
                    $fakePlayer->kill();
                    $fakePlayer->close('', 'Mavoric test closed.');
                }
            }

            $fakePlayer = $this->mav->getPlugin()->getServer()->getPlayer($this->npc);
            if ($fakePlayer === null) return;
            if ($this->target === null) return $this->timer = $this->stopAt;

            $oldPos = $this->target->getPosition();
            $newPos = new Position($this->target->x, $this->target->y + 3, $this->target->z, $this->target->getLevel());

            if ($fakePlayer->getLevel()->getName() !== $this->target->getLevel()->getName()) $fakePlayer->teleport($newPos);
            if ($fakePlayer->getLevel()->getFolderName() !== $this->target->getLevel()->getFolderName()) $fakePlayer->teleport($newPos);
            
            $fakePlayer->setNameTag('§4[AC] Mavoric ' . rand(10, 992929));
            $fakePlayer->setHealth(20);
            $fakePlayer->setScale(1);
            if (!$this->hasTeleported) {
                $fakePlayer->teleport($newPos);
                $this->hasTeleported = true;
            }

            //$speed = 1.5;
            //$circleVector = Util::circlePoint($this->target->getPosition(), 2,  $speed * $this->timer);// Returns vector3
            //$this->mav->updateMotion($fakePlayer, $circleVector, $this->target);
            $newPos = new Vector3($this->target->x + rand(-2, 2), $this->target->y + 2, $this->target->z + rand(-2, 2));
            $this->mav->updateMotion($fakePlayer, $newPos, $this->target);

        } catch (\Throwable $error) {
            return null;
        }

        // Reference https://github.com/falkirks/Specter/blob/master/src/specter/Specter.php pls thx

    }

    private function doCircleCheck($target) {
        $this->circle['count']++;
        $basePosition = $target->getPosition();

        if ($this->circle['count'] >= 40) {
            $this->circle['laps']++;
            $this->circle['count'] = 0;
        }
        
        
    }
}