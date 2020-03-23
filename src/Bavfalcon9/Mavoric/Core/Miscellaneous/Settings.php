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

use pocketmine\utils\TextFormat;
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
     * Resolve the boiler for a given plate.
     * @param String $plate - The boiler plate
     * @param Array $replacements - The replacements for the plate.
     * @return String
     */
    public static function resolveBoiler(String $plate, Array $replacements): String {
        foreach ($replacements as $search=>$replace) {
            $plate = str_replace($search, $replace, $plate);
        }
        return $plate;
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
        $allCheats = $this->config->get('Cheats');
        return array_keys(array_filter($allCheats, function ($settings) {
            if (!isset($settings['disabled'])) {
                return false;
            } else {
                return ($settings['disabled'] === false);
            }
        }));
    }

    /**
     * Returns whether AutoBan is enabled
     * @return Bool
     */

    public function isAutoBanEnabled(): Bool {
        return $this->config->getNested('Autoban.enabled') ?? false;
    }

    /**
     * Returns whether BanWave is enabled
     * @return Bool
     */

    public function isBanWaveEnabled(): Bool {
        return $this->config->getNested('Autoban.enabled') ?? false;
    }

    /**
     * Returns whether a cheat is suppressed or not
     * @return Bool
     */
    public function isSuppressed(String $cheat): Bool {
        $isEnabled = $this->config->getNested('Suppression.enabled') ?? true;
        $master = $this->config->getNested('Suppression.all-cheats') ?? true;

        if (!$isEnabled) {
            return false;
        } else {
            return ($master === true) ? true : $this->config->getNested("Cheats.$cheat.enabled") ?? $master;
        }
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
     * Returns the alert boiler plate
     * @return String
     */
    public function getAlertBoiler(): String {
        $prefix = '§c[MAVORIC]: ';
        $default = $prefix . '§r§4{player} §7failed test for§c {cheat}§8: ';
        $boiler = $this->config->getNested('Messages.Alerts.format') ?? $default;

        if (strpos(strtolower(TextFormat::clean($boiler)), 'mavoric') === false) {
            $boiler = $prefix . $boiler;
        }

        return $boiler;
    }

    /**
     * Get the reason for a cheat
     * @return String
     */
    public function getCheatReason(String $cheat): String {
        if (!in_array($cheat, $this->getEnabledDetections())) {
            return md5(microtime(true));
        } else {
            return $this->config->getNested("Cheats.$cheat.reason") ?? md5(microtime(true));
        }
    }

    /**
     * Get the ban type
     * @return String
     */
    public function getBanType(): String {
        return $this->config->getNested('Autoban.type') ?? 'ban';
    }

    /**
     * Get the ban reason for a player
     * @return String
     */
    public function getBanReason(): String {
        return $this->config->getNested('Autoban.ban-reason') ?? '§cError: §l{cheat-reason}';
    }

    /**
     * Get the ban message to broadcast in chat
     * @return String
     */
    public function getBanMessage(): String {
        return $this->config->getNested('Messages.onban') ?? '§4[MAVORIC] A player has been removed from your game for abusing or hacking. Thanks for reporting them!';
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