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

namespace Bavfalcon9\Mavoric\Events;

use pocketmine\Player;
use pocketmine\Server;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Core\Miscellaneous\Flag;

class MavoricEvent {
    /** @var Mavoric */
    private $mavoric;
    /** @var pocketmine\event\Event */
    private $eventData = null;
    /** @var Player */
    private $player;
    /** @var Bool */
    private $isCancelled = false;
    /** @var Bool */
    private $isCheating = false;

    public function __construct($e, Mavoric $mavoric, Player $target) {
        $this->mavoric = $mavoric;
        $this->player = $target;
        $this->eventData = $e;
    }

    /**
     * Cancels the event.
     * @param Bool $val - Cancel the event?
     * @return Bool - Whether or not the event was cancelled.
     */
    public function cancel(Bool $val = true): Bool {
        if ($this->mavoric->getTpsCheck()->isHalted()) {
            return false;
        }
        
        $this->eventData->setCancelled($val);
        $this->isCancelled = $val;
        return $val;
    }

    /**
     * Get the player the event belongs too
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }

    /**
     * Get the pocketmine form of the event
     * @return pocketmine\event\Event
     */
    public function getPMEvent() {
        return $this->eventData;
    }

    /**
     * Get the server
     * @return Server
     */
    public function getServer(): Server {
        return $this->mavoric->getServer();
    }

    /**
     * Get mavoric class instance
     * @return Mavoric
     */
    public function getMavoric(): Mavoric {
        return $this->mavoric;
    }

    /**
     * Get the current server tick
     * @return int - The current server tick.
     */
    public function getTick(): int {
        return $this->mavoric->getServer()->getTick();
    }

    /**
     * Set whether the event is affiliated with a cheat
     * @param Bool $val - Is cheating?
     * @return Bool - Is cheating?
     */
    public function setCheating(Bool $val): Bool {
        $this->isCheating = $val;
        return $val;
    }

    /**
     * Get whether or not the event is affiliated with cheating
     * @return Bool
     */
    public function getCheating(): Bool {
        return $this->isCheating;
    }

    /**
     * Issue a violation to the player in the event
     * @param int $cheat - Mavoric cheat ID
     * @param int $count - Violation Count
     * @return Flag - Player cheat flag
     */
    public function issueViolation(int $cheat, int $count = 1): Flag {
        if ($this->mavoric->getTpsCheck()->isHalted()) {
            return $this->mavoric->getFlag(null);
        }

        if ($this->mavoric->settings->isSuppressed(Mavoric::getCheatName($cheat))) {
            $this->cancel(true);
        }

        $flag = $this->mavoric->getFlag($this->player);
        $flag->addViolation($cheat, $count);

        return $flag;
    }

    /**
     * Sends a message to all staff in game for the cheat specified.
     * @param String $cheat - Cheat display name to staff.
     * @param String $details - Violation details
     * @return Bool - Whether or not the alert was successful
     */
    public function alertStaff(String $cheat, String $details): Bool {
        return $this->sendAlert($cheat, $details);
    }

    /**
     * Sends a message to all staff in game for the cheat specified.
     * @param String $cheat - Cheat display name to staff.
     * @param String $details - Violation details
     * @return Bool - Whether or not the alert was successful
     */
    public function sendAlert(String $cheat, String $details): Bool {
        if ($this->mavoric->getTpsCheck()->isHalted()) {
            return false;
        }
        $cheat = Mavoric::CHEATS[$cheat];
        $this->mavoric->alertStaff($this->player, $cheat, $details);
        return true;
    }
}