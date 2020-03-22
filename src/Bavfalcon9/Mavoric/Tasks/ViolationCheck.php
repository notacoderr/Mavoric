<?php
/***
 *      __  __                       _      
 *     |  \/  |                     (_)     
 *     | \  / | __ ___   _____  _ __ _  ___ 
 *     | |\/| |/ _` \ \ / / _ \| '__| |/ __|
 *     | |  | | (_| |\ V / (_) | |  | | (__ 
 *     |_|  |_|\__,_| \_/ \___/|_|  |_|\___|
 *                                          
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  @author Bavfalcon9
 *  @link https://github.com/Olybear9/Mavoric                                  
 */

namespace Bavfalcon9\Mavoric\Tasks;
use pocketmine\scheduler\Task;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Core\Utils\CheatIdentifiers;
use Bavfalcon9\Mavoric\Core\Miscellaneous\CheatPercentile;
use Bavfalcon9\Mavoric\Bavfalcon9\Mavoric\Bans\BanHandler;

class ViolationCheck extends Task {
    private $mavoric;
    private $warned = [];
    private $lastAlert = [];
    private $seconds = 0;

    public function __construct(Mavoric $mavoric) {
        $this->mavoric = $mavoric;
    }

    public function onRun(int $tick) {
        // Do players
        // Add per cheat violation
        $this->seconds++;
        $this->mavoric->getPlugin()->reportHandler->checkReports();
        $players = $this->mavoric->getPlugin()->getServer()->getOnlinePlayers();

        if ($this->mavoric->settings->isAutoBanEnabled()) {
            $max = $this->mavoric->settings->getConfig()->getNested('Autoban.max-violations') ?? 64;

            foreach ($players as $player) {
                $flag = $this->mavoric->getFlag($player);
                $top = $flag->getMostViolations();

                if ($flag->getTotalViolations() >= $max) {
                    $reason = CheatIdentifiers::getCheatName($flag->getMostViolations());
                    $this->mavoric->banManager->saveBan($player->getName(), $flag->clone()->getFlagsByNameAndCount(), CheatPercentile::getPercentile($this->mavoric->getFlag($player)), 'MAVORIC', $reason);
                    $this->mavoric->issueBan($player, null, $flag->toBanwaveData());
                    continue;
                }
            }
            return;
        }

        if ($this->mavoric->settings->isBanWaveEnabled()) {
            $violations = $this->mavoric->settings->getConfig()->getNested('Banwaves.violations') ?? 54;
            $max = $this->mavoric->settings->getConfig()->getNested('Banwaves.max-violations') ?? 240;
            $waveHandler = $this->mavoric->getWaveHandler();
            $currentWave = $waveHandler->getCurrentWave();

            if ($max === false) {
                $max = -69;
            }

            foreach ($players as $player) {
                if ($currentWave->isIssued()) {
                    $currentWave = $waveHandler->getWave($currentWave->getNumber() + 1);
                }

                $flag = $this->mavoric->getFlag($player);
                $top = $flag->getMostViolations();
                $this->mavoric->getCheatName($top);

                if ($flag->getTotalViolations() >= $violations) {
                    $flags = $flag->getFlagsByNameAndCount();
                    if (!$currentWave->hasPlayer($player->getName())) {
                        $this->mavoric->messageStaff(Mavoric::INFORM, $player->getName() . ' has been automatically added to wave: ' . $currentWave->getNumber());
                    }
                    $currentWave->addPlayer($player->getName(), '§4[AC] Illegal Client Modifications or Abuse.', $flags, $flag->getTotalViolations());
                }

                if ($flag->getTotalViolations() >= $max) {
                    $flags = $flag->getFlagsByNameAndCount();
                    $data = $currentWave->addPlayer($player->getName(), '§4[AC] Illegal Client Modifications or Abuse.', $flags, $flag->getTotalViolations());
                    $this->mavoric->issueBan($player, $currentWave, $data);
                }
            }

            return;
        }
    }
}