<?php

/**
 * DISCLAIMER: This code was not written by me, all credit goes to author.
 * Author: https://github.com/falkirks/Specter
 */

namespace Bavfalcon9\Mavoric\entity;
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