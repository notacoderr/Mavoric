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
namespace Bavfalcon9\Mavoric\Cheat\Combat;

use pocketmine\event\Listener;
use Bavfalcon9\Mavoric\Cheat\Cheat;
use Bavfalcon9\Mavoric\Cheat\CheatManager;

class Reach extends Cheat implements Listener {
    /**
     * Whether or not the cheat is enabled
     * @return Bool
     */
    public function isEnabled(): Bool {
        return true;
    }

    /**
     * Gets the type of cheat
     * @return String
     */
    public function getType(): String {
        return 'Combat';
    }

    /**
     * Get the id of the cheat
     * @return int
     */
    public function getId(): int {
        return CheatManager::REACH;
    }

    /**
     * Gets the cheat name
     * @return String
     */
    public function getName(): String {
        return 'Reach';
    }
}