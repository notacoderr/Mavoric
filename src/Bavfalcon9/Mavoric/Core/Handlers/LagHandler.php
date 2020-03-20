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
namespace Bavfalcon9\Mavoric\Core\Handlers;

use Bavfalcon9\Mavoric\Main;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;

class LagHandler implements Listener {
    /** @var Array[] */
    private static $status;
    /** @var Main */
    private $plugin;

    public function __construct(Main $plugin) {
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
        $this->plugin = $plugin;
    }

    /**
     * Event handler for pocketmine.
     * @param DataPacketRecieveEvent - $event
     * @return void
     */
    public function onPacket(DataPacketReceiveEvent $event): void {
        $playerName = $event->getPlayer()->getName();

        if (!isset(self::$status[$playerName])) {
            self::$status[$playerName] = [
                "last" => microtime(true),
                "pings" => []
            ];
        }

        if ($event->getPlayer()->isOnline()) {
            $event->getPlayer()->sendTip((LagHandler::isLaggy($playerName)) ? "§cYou are lagging!" : "§aYou are not lagging");
        }

        self::$status[$playerName]["last"] = microtime(true);
        return;
    }

    /**
     * Gets a player's status
     * @param String $player - Player to retrieve status for
     * @return Array|Null
     */
    public static function getStatus(String $player): ?Array {
        return (!isset(self::$status[$player])) ? null : self::$status[$player];
    }

    /**
     * Gets whether a player is lagging based on the last packet sent.
     * @return Bool|Null
     */
    public static function isLaggy(String $player): ?Bool {
        $status = self::getStatus($player);

        if (!$status) {
            return null;
        }

        return ($status['last'] + 0.5 <= microtime(true));
    }
}