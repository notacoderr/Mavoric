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
use Bavfalcon9\Mavoric\misc\Classes\CheatPercentile;
use Bavfalcon9\Mavoric\Bavfalcon9\Mavoric\Bans\BanHandler;

class ViolationCheck extends Task {
    private $mav;
    private $warned = [];
    private $lastAlert = [];
    private $seconds = 0;

    public function __construct(Mavoric $mavoric) {
        $this->mav = $mavoric;
    }

    public function onRun(int $tick) {
        // Do players
        // Add per cheat violation
        $this->seconds++;
        $this->mav->getPlugin()->reportHandler->checkReports();
        $this->mav->messageStaff('debug', null, "Violation check ran: Alive for §7{$this->seconds}§c seconds");
        if ($this->mav->getPlugin()->config->getNested('Autoban.disabled') === true) return true;
        $players = $this->mav->getPlugin()->getServer()->getOnlinePlayers();
        foreach ($players as $player) {
            $flag = $this->mav->getFlag($player);
            $top = $flag->getMostViolations();

            if ($top === Mavoric::Reach) {
                if ($flag->getViolations($top) < 5) {
                    $this->mav->messageStaff('debug', null, "§7{$player->getName()} §chad less than 5 reach violations checking cache time.");
                    if (isset($this->lastAlert[$player->getName()]) && $this->lastAlert[$player->getName()] + 2 <= time()) {
                        $this->mav->messageStaff('debug', null, "§7{$player->getName()} §chad less than 5 reach violations and met cache time.");
                        if (!$this->mav->getPlugin()->reportHandler->isReported($player->getName())) {
                            $this->mav->getFlag($player)->removeViolation(Mavoric::Reach, $flag->getViolations($top));
                            $this->mav->messageStaff('debug', null, "§7{$player->getName()} §cwas not reported and reach violations were lifted.");
                        }
                    }
                    if (!isset($this->lastAlert[$player->getName()])) {
                        $this->lastAlert[$player->getName()] = time();
                        $this->mav->messageStaff('debug', null, "§7{$player->getName()}§c now has cache data.");
                    }
                    if (isset($this->lastAlert[$player->getName()]) && $this->lastAlert[$player->getName()] + 2 <= time()) {
                        $this->mav->messageStaff('debug', null, "§7{$player->getName()}§c had old cache time data. This data was purged.");
                        unset($this->lastAlert[$player->getName()]);
                    }
                }
            }
            if ($flag->getTotalViolations() >= 45) {
                $this->mav->messageStaff('debug', null, "§7{$player->getName()}§c is cheating, checking for autobans.");
                if (!$this->mav->canAutoBan($flag->getMostViolations())) continue;
                $this->mav->messageStaff('debug', null, "§7{$player->getName()}§c is cheating, autoban enabled..");
                $reason = $this->mav->getCheat($flag->getMostViolations());
                $this->mav->banManager->saveBan($player->getName(), $flag->clone()->getFlagsByNameAndCount(), CheatPercentile::getPercentile($this->mav->getFlag($player)), 'MAVORIC', $reason);
                $flag->clearViolations();
                $this->mav->alert(null, 'alert-grant', $player, $reason);
                $this->mav->ban($player, $reason);
                continue;
            }

            if ($top !== -1) {
                $reason = $this->mav->getCheat($flag->getMostViolations());
                $count = $flag->getViolations($top);
                
                if ($flag->getViolations($top) >= 35) {
                    $this->mav->messageStaff('debug', null, "§7{$player->getName()}§c is cheating, checking for autobans.");
                    if (!$this->mav->canAutoBan($flag->getMostViolations())) continue;
                    $this->mav->messageStaff('debug', null, "§7{$player->getName()}§c is cheating, autoban enabled..");
                    $flag->clearViolations();
                    $this->mav->banManager->saveBan($player->getName(), $flag->clone()->getFlagsByNameAndCount(), CheatPercentile::getPercentile($this->mav->getFlag($player)), 'MAVORIC', $reason);
                    $this->mav->alert(null, 'alert-grant', $player, $reason);
                    $this->mav->ban($player, $reason);
                    continue;
                } 
            }

        }
    }
}