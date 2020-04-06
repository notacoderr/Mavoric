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
namespace Bavfalcon9\Mavoric\Cheat\violation;

class ViolationData {
    /** @var Array[] */
    private $levels = [
        
    ];

    /**
     * Gets the violation level (how probable it is they're)
     * @return Int
     */
    public function getLevel(int $cheat): ?int {
        return $this->levels[$cheat];
    }

    /**
     * Increments the violation level
     * @param int $cheat - Cheat to increment
     * @param int $amount - How much to increment
     * @return Int
     */
    public function incrementLevel(int $cheat, int $amount): ?int {
        return $this->levels[$cheat] += $amount;
    }

    /** 
     * Removes a violation level
     * @param int $amount - How much to deincrement
     * @return int
     */
    public function deincrementLevel(int $amount): int {
        return $this->level -= $amount;
    }

    /**
     * Clears the violation data
     * @return int
     */
    public function clear(): int {
        return $this->level = 0;
    }

}