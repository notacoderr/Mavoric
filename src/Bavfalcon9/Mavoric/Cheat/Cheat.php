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
namespace Bavfalcon9\Mavoric\Cheat;

abstract class Cheat {
    /**
     * Whether or not the cheat detection is enabled
     */
    abstract public function isEnabled(): Bool;

    /**
     * Get the category of the cheat
     */
    abstract public function getType(): String;

    /**
     * Get the API to use
     *
     * abstract public function getAPI(): int;
     */

    /**
     * Gets the cheat id.
     */
    abstract public function getId(): int;

    /**
     * Gets the name of the cheat
     */
    abstract public function getName(): String;

}