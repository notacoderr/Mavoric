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

/**
 * DISCLAIMER: This code was not written by me, all credit goes to author.
 * Author: https://github.com/falkirks/Specter
 */

namespace Bavfalcon9\Mavoric\Entity;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\PlayerNetworkSessionAdapter;
use pocketmine\network\SourceInterface;
use pocketmine\Player;

class SpecterPlayer extends Player {
    public $spec_needRespawn = false;
    private $forceMovement;
    public function __construct(SourceInterface $interface, $ip, $port){
        parent::__construct($interface, $ip, $port);
    }
    /**
     * @return Vector3
     */
    public function getForceMovement(){
        return $this->forceMovement;
    }
	/**
	 * @return PlayerNetworkSessionAdapter
	 */
    public function getSessionAdapter() {
    	return $this->sessionAdapter;
    }
}