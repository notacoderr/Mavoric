<?php

/***
 *      __  __                       _      
 *     |  \/  |                     (_)     
 *     | \  / | __ ___   _____  _ __ _  ___ 
 *     | |\/| |/ _` \ \ / / _ \| '__| |/ __|
 *     | |  | | (_| |\ V / (_) | |  | | (__ 
 *     |_|  |_|\__,_| \_/ \___/|_|  |_|\___|
 *                                          
 *   THIS CODE IS TO NOT BE REDISTRUBUTED
 *   @author MavoricAC
 *   @copyright Everything is copyrighted to their respective owners.
 *   @link https://github.com/Olybear9/Mavoric                                  
 */

namespace Bavfalcon9\Mavoric\misc;

use pocketmine\utils\Config;
use Bavfalcon9\Mavoric\Mavoric;

class Settings {
    private $config;
    public const OPTIONS = ['Autoban', 'Banwaves', 'Alerts', 'Suppression'];

    public function __construct(Config $config) {
        $this->config = $config;
    }

    public function getSuppressedCheats(): Array {
        $config = $this->config->get('Suppression');
        $all = $this->config->getNested('Suppression.all-cheats');
        $suppressed = $this->config->getNested('Suppression.cheats');

        if (!$config || $all === true) {
            return array_keys(Mavoric::CHEATS);
        }

        if ($suppressed && !$all) {
            return $suppressed;
        }
    }

    public function isSuppressed(String $cheat): Bool {
        if (!isset(Mavoric::CHEATS[$cheat])) {
            return false;
        }

        return in_array($cheat, $this->getSuppressedCheats());
    }

    public function isEnabled(String $type): ?Bool {
        return $this->config->getNested("$type.enabled");
    }

    public function isCheatEnabled(String $cheat): Bool {
        if (!$this->config->getNested('Cheats.disabled')) return true;
        return !in_array($cheat, $this->config->getNested('Cheats.disabled'));
    }
    
    public function getConfig(): Config {
        return $this->config;
    }

    public function update(Config $config): Bool {
        $this->config = $config;
        return true;
    }
}