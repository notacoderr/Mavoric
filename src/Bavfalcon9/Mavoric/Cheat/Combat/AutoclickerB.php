<?php

/***
 *      __  __                       _      
 *     |  \/  |                     (_)     
 *     | \  / | __ ___   _____  _ __ _  ___ 
 *     | |\/| |/ _` \ \ / / _ \| "__| |/ __|
 *     | |  | | (_| |\ V / (_) | |  | | (__ 
 *     |_|  |_|\__,_| \_/ \___/|_|  |_|\___|
 *                                          
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  @author Bavfalcon9
 *  @link https://github.com/Bavfalcon9/Mavoric                                  
 */

namespace Bavfalcon9\Mavoric\Cheat\Combat;

use pocketmine\Player;
use pocketmine\Server;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Cheat\Cheat;
use Bavfalcon9\Mavoric\Events\Player\PlayerClickEvent;

class AutoclickerB extends Cheat{

    /** @var int[] */
    private $cps;
    /** @var int[] */
    private $clicks;
    /** @var int[] */
    private $level;

    public function __construct(Mavoric $mavoric, int $id = -1) {
        parent::__construct($mavoric, "AutoclickerB", "Combat", $id, false);
        $this->cps = [];
        $this->clicks = [];
        $this->level = [];
    }

    public function onClick(PlayerClickEvent $ev): void {
        $this->macroCheck($ev->getPlayer());
    }

    private function macroCheck(Player $player): void {
        if (!isset($this->cps[$player->getName()])) {
            $this->cps[$player->getName()] = [];
        }

        $time = microtime(true);

        array_push($this->cps[$player->getName()], microtime(true));

        $cps = count(array_filter($this->cps[$player->getName()],  function (float $t) use ($time) : bool {
            return ($time - $t) <= 1;
        }));

        if ($player->getPing() >= 200) return;
        if ($player->getServer()->getTicksPerSecond() <= 17) return;

        if (!isset($this->clicks[$player->getName()])) $this->clicks[$player->getName()] = [];
        if (!isset($this->level[$player->getName()])) $this->level[$player->getName()] = 1;

        array_push($this->clicks[$player->getName()], $cps);
        if (count($this->clicks[$player->getName()]) === 20) {
            if ($this->macroTest($this->clicks[$player->getName()], $cps, $player) === true) {
                $this->level[$player->getName()] = 2;
                $this->increment($player->getName(), 1);
                $this->notifyAndIncrement($player, 2, 1, [
                    "CPS" => $cps,
                    "Ping" => $player->getPing()
                ]);
            }


            Server::getInstance()->getLogger()->debug($player->getName() . "'s CPS: " . implode(", ", $this->clicks[$player->getName()]));
            unset($this->clicks[$player->getName()]);
            $this->clicks[$player->getName()] = [];
        }
    }

    private function macroTest(array $clicks, int $cps, Player $player): bool {
        $min = min($clicks);
        $max = max($clicks);

        if ($max - $min <= 2) {
            if (count(array_unique($clicks)) <= 3) {
                if (end($clicks) === $cps) {
                    $this->level[$player->getName()] = $this->level[$player->getName()] + 1;
                    if ($this->level[$player->getName()] === 5) return true;
                    else return false;
                }
                // this is repetitive, remove 
                if ($this->level[$player->getName()] !== 1) $this->level[$player->getName()] = $this->level[$player->getName()] - 1;
                return false;
            }
            // this is repetitive, remove 
            if ($this->level[$player->getName()] !== 1) $this->level[$player->getName()] = $this->level[$player->getName()] - 1;
            return false;
        }
        // this is repetitive, remove 
        if ($this->level[$player->getName()] !== 1) $this->level[$player->getName()] = $this->level[$player->getName()] - 1;
        return false;
    }

}