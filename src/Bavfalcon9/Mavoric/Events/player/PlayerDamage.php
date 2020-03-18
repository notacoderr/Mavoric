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

namespace Bavfalcon9\Mavoric\events\player;

use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\events\MavoricEvent;

/**
 * Called when a player recieves damage from an entity.
 */
class PlayerDamage extends MavoricEvent {
    /** @var Entity */
    private $attacker;
    /** @var Player */
    private $victim;
    /** @var Bool */
    private $projectile;

    public function __construct($e, Mavoric $mavoric, Entity $attacker, Player $victim, Bool $projectile) {
        parent::__construct($e, $mavoric, $victim);
        $this->attacker = $attacker;
        $this->victim = $victim;
        $this->projectile = $projectile;
    }

    public function getAttacker(): ?Entity {
        return $this->attacker;
    }

    public function getVictim(): ?Player {
        return $this->victim;
    }

    public function getDistance(): float {
        $pos1 = $this->attacker->getPosition() ?? new Vector3(0,0,0);
        $pos2 = $this->victim->getPosition() ?? new Vector3(0,0,0);
        return $pos1->distance($pos2);
    }

    public function isPlayerToPlayer(): Bool {
        return ($this->attacker instanceof Player);
    }

    public function isProjectile(): Bool {
        return $this->projectile;
    }
}