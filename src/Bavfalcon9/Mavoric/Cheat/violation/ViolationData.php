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
namespace Bavfalcon9\Mavoric\Cheat\Violation;

use pocketmine\Player;
use Bavfalcon9\Mavoric\Cheat\Cheat;
use Bavfalcon9\Mavoric\Events\Violation\ViolationChangeEvent;

class ViolationData {
    /** @var Array[] */
    private $levels;
    /** @var Player */
    private $player;

    public function __construct(Player $player) {
        $this->levels = [];
        $this->player = $player;
    }

    /**
     * Gets the violation level for a given cheat
     * @return string
     */
    public function getLevel(string $cheat): ?int {
        return (!isset($levels[$cheat])) ? 0 : $this->levels[$cheat];
    }

    /**
     * Increments the violation level
     * @param string $cheat - Cheat to increment
     * @param int $amount - How much to increment
     * @return Int
     */
    public function incrementLevel(string $cheat, int $amount = 1): ?int {
        if (!isset($this->levels[$cheat])) {
            echo "cheat created\n";
            $this->levels[$cheat] = 0;
        }
        $ev = new ViolationChangeEvent($this->player, $cheat, $amount, $this->levels[$cheat], true);
        $ev->call();

        if ($ev->isCancelled() === true) {
            return null;
        } else {
            $this->levels[$cheat] += $ev->getAmount();
            return $this->levels[$cheat];
        }
    }

    /** 
     * Deincrements a violation level
     * @param string $cheat - Cheat to deincrement
     * @param int $amount - How much to deincrement
     * @return int
     */
    public function deincrementLevel(string $cheat, int $amount = 1): int {
        if (!isset($this->levels[$cheat])) {
            $this->levels[$cheat] = 0;
        }
        $ev = new ViolationChangeEvent($this->player, $cheat, $amount, $this->levels[$cheat], false);
        $ev->call();

        if ($ev->isCancelled() === true) {
            return null;
        } else {
            $this->levels[$cheat] -= $ev->getAmount();
            return $this->levels[$cheat];
        }
    }

    /**
     * Gets the highest level of violations for all cheat
     * @return int - Cheat number
     */
    public function getHighestLevelByCheat(): ?int {
        $highest = null;
        foreach ($this->levels as $cheat) {
            if ($highest === null || $highest < $cheat) {
                $highest = $cheat;
            }
        }

        return $highest;
    }

    /**
     * Gets a percentage of probability of cheating with the current data
     * @return float
     */
    public function getCheatProbability(): float {
        $amount = count($this->levels);
        $probable = 0;

        if ($amount > 3) {

        }
    }

    /**
     * Clears the violation data
     * @return int
     */
    public function clear(): int {
        return $this->levels = [];
    }

}