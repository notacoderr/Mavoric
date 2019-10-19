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

class ViolationCheck extends Task {
    private $mav;
    private $warned = [];
    private $seconds = 0;

    public function __construct(Mavoric $mavoric) {
        $this->mav = $mavoric;
    }

    public function onRun(int $tick) {
        // Do players
        // Add per cheat violation
        $this->seconds++;
        $players = $this->mav->getPlugin()->getServer()->getOnlinePlayers();
        foreach ($players as $player) {
            $flag = $this->mav->getFlag($player);
            $top = $flag->getMostViolations();

            if ($top === Mavoric::Reach) {
                if ($flag->getViolations($top) < 5) $this->mav->getFlag($player)->removeViolation(Mavoric::Reach, $flag->getViolations($top));
            }
            if ($flag->getTotalViolations() >= 45) {
                $reason = $this->mav->getCheat($flag->getMostViolations());
                $flag->clearViolations();
                $this->mav->alert(null, 'alert-grant', $player, $reason);
                $this->mav->ban($player, $reason);
                continue;
            }

            if ($top !== -1) {
                // if ($top === Mavoric::Reach) return;
                
                $reason = $this->mav->getCheat($flag->getMostViolations());
                $count = $flag->getViolations($top);
                //if ($this->mav->hasTaskFor($player)) return false;
                
                if ($flag->getViolations($top) >= 35) {
                    $flag->clearViolations();
                    $this->mav->alert(null, 'alert-grant', $player, $reason);
                    $this->mav->ban($player, $reason);
                    continue;
                } 
                /*
                if ($flag->getViolations($top) >= 5) {
                    if ($this->mav->hasTaskFor($player)) continue;
                    //$flag->clearViolations();
                    //$this->mav->startTask($player, 90);
                    continue;
                }*/
            }

        }
    }
}