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
use Bavfalcon9\Mavoric\Cheat\Combat\CombatModule;

class CheatManager {
    /** @var Loader */
    private $plugin;
    /** @var Mavoric */
    private $mavoric;
    /** @var CheatModule[] */
    private $modules;
    /** @var bool */
    private $registered = false;

    /**
     * @param Loader $loader
     */
    public function __construct(Mavoric $mavoric, Loader $loader, Bool $autoRegister = false) {
        $this->mavoric = $mavoric;
        $this->plugin = $loader;

        if ($autoRegister) {
            $this->registerModules();
        }
    }

    /**
     * Registers all cheat modules
     * @return void
     */
    public function registerModules(): void {
        if ($this->registered === true) {
            //throw new \Exception("Cheats are already registered.");
            return;
        } else {
            $this->modules[] = new CombatModule();
            $this->registered = true;
            foreach ($this->modules as $module) {
                $module->registerAll($this->mavoric, $this->plugin, []);
            }
        }
    }

    public function disableModules(): void {
        $this->registered = false;
        foreach ($this->modules as $module) {
            $module->unregisterAll($this->mavoric, $this->plugin);
        }
    }
}