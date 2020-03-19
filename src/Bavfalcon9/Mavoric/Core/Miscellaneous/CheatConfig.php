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

namespace Bavfalcon9\Mavoric\Core\Miscellaneous;

use pocketmine\utils\Config;
use pocketmine\utils\MainLogger;
use Bavfalcon9\Mavoric\Mavoric;

class CheatConfig {
    private $config = [];
    private $cache;

    /**
     * Hard coded values are taken from AI generated values.
     */
    public const defaults = [
        Mavoric::KillAura => [false],
        Mavoric::AutoClicker => [true, 23],
        Mavoric::Flight => [true, 4],
        Mavoric::Reach => [true, 6.23],
        Mavoric::NoClip => [true, null],
        Mavoric::MultiAura => [true, null],
        Mavoric::Speed => [false, null]
    ];

    public function __construct(Config $config) {
        $this->cache = $config;

        if (!$this->cache) return $this->config = self::defaults;

        foreach ($this->cache->get('Cheats') as $setting=>$settings) {
            $cheatData = new CheatSetting($settings);
            $this->config[Mavoric::getCheatFromString($setting)] =  $cheatData;
            continue;
        }
    }

    public function getCpsLimit(): ?Float {
        return ($this->config[Mavoric::AutoClicker]);
    }
}