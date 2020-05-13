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
use Bavfalcon9\Mavoric\Cheat\Movement\MovementModule;

class CheatManager {
    public const MODULES = [
        'Movement',
        'Combat'
    ];
    /** @var Loader */
    private $plugin;
    /** @var Mavoric */
    private $mavoric;
    /** @var ModuleMap */
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
        foreach (self::MODULES as $module) {
            $this->modules[$module] = [];
            $cheats = \scandir($this->getPathBase() . $module);
            foreach ($cheats as $cheat) {
                $cheat = explode('.php', $cheat)[0];
                if (in_array($cheat, ['.', '..'])) continue;
                if (class_exists($this->getClassBase() . $module . "\\" . $cheat)) {
                    $class = '\\' . $this->getClassBase() . $module . '\\' . $cheat;
                    $detection = new $class($this->mavoric);
                    $this->plugin->getServer()->getPluginManager()->registerEvents($detection, $this->plugin);
                    $this->plugin->getLogger()->debug("Cheat Detection: [$module] $cheat enabled.");
                }
            }
        }
    }

    /**
     * Unregister all cheat modules from mavoric.
     * @return void
     */
    public function disableModules(): void {
        foreach ($this->modules as $moduleName => $cheats) {
            foreach ($cheats as $cheat) {
                // call an event?
                
            }
            unset($this->modules[$moduleName]);
        }

        $this->modules = [];
    }

    /**
     * Gets the base path for the cheats.
     * @return sting
     */
    protected function getPathBase(): string {
        return $this->plugin->getFilePath() . 'src/Bavfalcon9/Mavoric/Cheat/';
    }

    /**
     * Gets the base path for the cheats.
     * @return sting
     */
    protected function getClassBase(): string {
        return "Bavfalcon9\\Mavoric\\Cheat\\";
    }
}