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

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\Event;
use pocketmine\event\Listener;
use Bavfalcon9\Mavoric\Mavoric;

class Cheat implements Listener {
    /** @var Mavorc */
    protected $mavoric;
    /** @var int[] */
    protected $violations;
    /** @var string */
    private $name;
    /** @var string */
    private $type;
    /** @var int */
    private $id;
    /** @var bool */
    private $enabled;
    /** @var string[] */
    private static $registered = [];

    public function __construct(Mavoric $mavoric, string $name, string $type, int $id, bool $enabled = true) {
        $this->mavoric = $mavoric;
        $this->name = $name;
        $this->type = $type;
        $this->id = $id;
        $this->enabled = $enabled;
        $this->violations = [];
        self::$registered[] = $name;
    }

    /**
     * Gets the cheat name
     * @return String
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Gets the type of cheat
     * @return String
     */
    public function getType(): string {
        return $this->type;
    }

    /**
     * Get the id of the cheat
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Whether or not the cheat is enabled
     * @return Bool
     */
    public function isEnabled(): bool {
        return $this->enabled;
    }

    /**
     * @param string $name - Name to increment
     * @param int $amount - Amount to increment
     */
    public function increment(string $name, int $amount = 1): int {
        if (!isset($this->violations[$name])) {
            $this->violations[$name] = 0;
        }

        return $this->violations[$name] += $amount;
    }

    /**
     * @param string $name - Name to deincrement
     * @param int $amount - Amount to deincrement
     */
    public function deincrement(string $name, int $amount = 1): int {
        if (!isset($this->violations[$name])) {
            $this->violations[$name] = 0;
        }

        return $this->violations[$name] -= $amount;
    }

    /**
     * @param string $name - Name to get
     */
    public function getViolation(string $name): int {
        if (!isset($this->violations[$name])) {
            $this->violations[$name] = 0;
        }

        return $this->violations[$name];
    }

    public function incrementAndNotify(Player $player, string $details, array $verboseData = []) {

    }

    /**
     * @return void
     */
    public function suppress(Event $event): void {
        /** for now this is always true */
        $event->setCancelled(true);
        return;
    }

    public function getServer(): Server {
        return Server::getInstance();
    }
}