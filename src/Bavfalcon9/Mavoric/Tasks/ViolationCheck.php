<?php

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

            if ($flag->getTotalViolations() >= 40) {
                $reason = $this->mav->getCheat($flag->getMostViolations());
                if ($top === Mavoric::Reach) return;
                $flag->clearViolations();
                $this->mav->alert(null, 'alert-grant', $player, $reason);
                $this->mav->ban($player, $reason);
                return true;
            }

            if ($top !== -1) {
                if ($top === Mavoric::Reach) return;
                
                $reason = $this->mav->getCheat($flag->getMostViolations());
                $count = $flag->getViolations($top);
                //if ($this->mav->hasTaskFor($player)) return false;
                
                if ($flag->getViolations($top) >= 35) {
                    $flag->clearViolations();
                    $this->mav->alert(null, 'alert-grant', $player, $reason);
                    $this->mav->ban($player, $reason);
                    return true;
                } 
                
                if ($flag->getViolations($top) >= 5) {
                    if ($this->mav->hasTaskFor($player)) return false;
                    //$flag->clearViolations();
                    //$this->mav->startTask($player, 90);
                    return true;
                }
            }

        }
    }
}