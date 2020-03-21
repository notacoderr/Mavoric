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

namespace Bavfalcon9\Mavoric\Core\Miscellaneous;

use pocketmine\utils\Config;
use Bavfalcon9\Mavoric\Mavoric;

class Settings {
    /** @var Config */
    private $config;
    /** @var String[] */
    public const OPTIONS = ['Autoban', 'Banwaves', 'Alerts', 'Suppression', 'Messages', 'Cheats'];

    public function __construct(Config $config) {
        $this->config = $config;
    }

    /**
     * Option to get from config
     * @param String $option - Option to get.
     * @return Array[]
     */
    public function get(String $option): ?Array {
        if (in_array($option, Settings::OPTIONS)) {
            return null;
        } else {
            return $this->config->get($option);
        }
    }

    /**
     * @return Config
     */
    public function getConfig(): Config {
        return $this->config;
    }

    /**
     * Returns a list of enabled cheats
     * @return String[]
     */
    public function getEnabledDetections(): Array {
        $allCheats = $this->get('Cheats');

        return array_keys(array_map(function ($settings) {
            if (!isset($settings['disabled'])) {
                return false;
            } else {
                return ($settings['disabled'] === false);
            }
        }, $allCheats));
    }

    /**
     * Returns the TPS warn value.
     * @return int
     */
    public function getTpsWarnValue(): int {
        return $this->config->getNested('TPS.warn-below') ?? 17;
    }

    /**
     * Returns the TPS stop at value
     * @return int
     */
    public function getTpsStopValue(): int {
        return $this->config->getNested('TPS.stop-below') ?? 16;
    }

    /**
     * Updates the settings config
     * @return Bool
     */
    public function update(Config $config): Bool {
        $this->config = $config;
        return true;
    }
}