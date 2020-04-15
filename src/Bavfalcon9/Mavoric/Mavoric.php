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
namespace Bavfalcon9\Mavoric;

use pocketmine\Player;
use pocketmine\Server;
use Bavfalcon9\Mavoric\Utils\Notifier;
use Bavfalcon9\Mavoric\Cheat\CheatManager;
use Bavfalcon9\Mavoric\Cheat\Violation\ViolationData;

class Mavoric {
    /** @var Notifier */
    private $verboseNotifier;
    /** @var Notifier */
    private $checkNotifier;
    /** @var CheatManager */
    private $cheatManager;
    /** @var EventListener */
    private $eventListener;
    /** @var ViolationData[] */
    private $violations = [];

    public function __construct(Loader $plugin) {
        $this->cheatManager = new CheatManager($this, $plugin, true);
        $this->eventListener = new EventListener($this, $plugin);
        $this->verboseNotifier = new Notifier($this, $plugin);
        $this->checkNotifier = new Notifier($this, $plugin);
        $this->violations = [];
    }

    /**
     * Unloads all modules and commands.
     * @return void
     */
    public function disable(): void {
        $this->cheatManager->disableModules();
    }

    /**
     * Gets the violation level data for a player
     */
    public function getViolationDataFor(Player $player): ?ViolationData {
        if (!$player) return null;

        if (!isset($this->violations[$player->getName()])) {
            $this->violations[$player->getName()] = new ViolationData($player);
        }

        return $this->violations[$player->getName()];
    }

    /**
     * Gets the check notifier
     * @return Notifier
     */
    public function getCheckNotifier(): Notifier {
        return $this->checkNotifier;
    }

    /**
     * Gets the verbose notifier
     * @return Notifier
     */
    public function getVerboseNotifier(): Notifier {
        return $this->verboseNotifier;
    }
}