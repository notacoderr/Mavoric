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
namespace Bavfalcon9\Mavoric\Cheat\violation;

use Bavfalcon9\Mavoric\Utils\PlayerResolvable;

class ViolationHandler {
    /** @var ViolationData[] */
    private $data;

    public function __construct() {
        $this->data = [];
    }

    /**
     * Gets violation data for a given player
     * @param PlayerResolvable $player - Player
     * @return ViolationData
     */
    public function getViolationDataFor(PlayerResolvable $player): ViolationData {
        $player = $player->getPlayer();

        if (!$player) {
            return null;
        } else {
            if (!isset($this->data[$player->getName()])) {
                return $this->data[$player->getName()] = new ViolationData();
            } else {
                return $this->data[$player->getName()];
            }
        }
    }

    /**
     * Clears violation data for a player
     * @param PlayerResolvable $player - Player
     * @return Bool
     */
    public function clearViolationData(PlayerResolvable $player): Bool {
        $player = $player->getPlayer();

        if (!$player) {
            return false;
        } else {
            if (!isset($this->data[$player->getName()])) {
                return false;
            } else {
                unset($this->data[$player->getName()]);
                return true;
            }
        }
    }
}