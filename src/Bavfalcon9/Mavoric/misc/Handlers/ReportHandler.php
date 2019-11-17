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

namespace Bavfalcon9\Mavoric\misc\Handlers;

use Bavfalcon9\Mavoric\misc\Classes\CheatPercentile;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Main;
use pocketmine\Player;

class Reporthandler {
    private $reports = [];
    private $mavoric;
    private $plugin;

    public function __construct(Mavoric $mavoric, Main $plugin) {
        $this->plugin = $plugin;
        $this->mavoric = $mavoric;
    }

    public function submitReport($player, Array $cheats, String $reporter) : ?Bool {
        if (!$player) return null;
        if ($player instanceof Player) $player = $player->getName();
        if (!isset($this->reports[$player])) {
            $this->reports[$player] = [
                'total' => 1,
                'cheats' => $cheats,
                'reporters' => [$reporter]
            ];
            return true;
        } else {
            if (in_array($reporter, $this->reports[$player]['reporters'])) return false;
            $this->reports[$player]['total']++;
            array_push($this->reports[$player]['cheats'], $cheats);
            array_push($this->reports[$player]['reporters'], $reporter);
            return true;
        }
    }

    public function retractReport($player, $reporter): ?Bool {
        if (!isset($this->reports[$player])) return null;
        if (!in_array($reporter, $this->reports[$player]['reporters'])) return false;
        else {
            array_splice($this->reports[$player]['reporters'], array_search($reporter, $this->reports[$player]['reporters']));
            if (empty($this->reports[$player]['reporters'])) unset($this->reports[$player]);
            return true;
        }
    }

    public function checkReports() {
        foreach ($this->reports as $player=>$report) {
            if ($this->shouldBan($player)) {
                $p = $this->plugin->getServer()->getPlayer($player);
                if (!$p) {
                    unset($this->reports[$player]);
                    continue;
                }
                $flag = $this->mavoric->getFlag($p);
                $this->mavoric->banManager->saveBan($player, $flag->getFlagsByNameAndCount(), CheatPercentile::getPercentile($flag), 'MAVORIC [Report]', Mavoric::getCheatName($flag->getMostViolations()));
                $this->mavoric->ban($p, Mavoric::getCheatName($flag->getMostViolations()));
                foreach ($report['reporters'] as $reporter) {
                    $reporter = $this->plugin->getServer()->getPlayer($reporter);
                    if ($reporter === null) continue;
                    else {
                        $reporter->sendMessage('§a[MAVORIC-REPORT] A cheater you reported has been banned, thanks for reporting!');
                        continue;
                    }
                }
                unset($this->reports[$player]);
            } else continue;
        } 
    }

    public function getReport($player) : ?Array {
        if (!isset($this->reports[$player])) return null;
        else return $this->reports[$player];
    }

    public function shouldBan($player): Bool {
        $online = sizeof($this->plugin->getServer()->getOnlinePlayers());
        if (!isset($this->reports[$player])) return false;
        $player = $this->plugin->getServer()->getPlayer($player);
        if (!$player) return false;
        $flag = $this->mavoric->getFlag($player);
        if ($flag->getTotalViolations() >= 20) return true;
        else return false;
    }

    public function isReported($player): Bool {
        return array_key_exists($player, $this->reports);
    }
}