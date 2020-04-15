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

use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Loader;

abstract class CheatModule {
    /**
     * Whether or not the module is enabled
     */
    abstract public function isEnabled(): bool;

    /**
     * Gets the list of cheats in a module
     */
    abstract public function getAll(): array;

    /**
     * Called when mavoric is loaded or module is enabled
     */
    abstract public function registerAll(Mavoric $mavoric, Loader $plugin, array $disabled): void;

    /**
     * Called when mavoric is unloaded or module is disabled
     */
    abstract public function unregisterAll(Mavoric $mavoric, Loader $plugin): void;
}