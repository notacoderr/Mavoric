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

use Bavfalcon9\Mavoric\Cheat\Combat\Reach;
use Bavfalcon9\Mavoric\Cheat\Combat\MultiAura;
use Bavfalcon9\Mavoric\Cheat\CheatModule;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Loader;
use pocketmine\event\Listener;

class CombatModule extends CheatModule {
    /** @var Cheat[] */
    private $cheats;

    /**
     * Whether or not the module is enabled.
     * @return Bool
     */
    public function isEnabled(): bool {
        return true;
    }

    /**
     * Gets the list of cheats in a module
     */
    public function getAll(): array {
        return $this->cheats;
    }

    /**
     * Called when mavoric is loaded
     */
    public function registerAll(Mavoric $mavoric, Loader $plugin, array $disabled): void {
        $this->cheats[] = new Reach($mavoric);
        $this->cheats[] = new MultiAura($mavoric);

        foreach ($this->cheats as $cheat) {
            $plugin->getServer()->getPluginManager()->registerEvents($cheat, $plugin);
            $plugin->getLogger()->debug("Cheat: " . $cheat->getName() . " registered");
        }
    }

    /**
     * Called when mavoric is unloaded
     */
    public function unregisterAll(Mavoric $mavoric, Loader $plugin): void {
        unset($this->cheats);
    }
}