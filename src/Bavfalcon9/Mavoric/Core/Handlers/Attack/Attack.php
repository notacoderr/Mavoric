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
namespace Bavfalcon9\Mavoric\Core\Handlers\Attack;

class Attack {
    /** @var int */
    private $id;
    /** @var int */
    private $vid;
    /** @var int */
    private $time;
    
    public function __construct(int $id, int $vid) {
        $this->id = $id;
        $this->vid = $vid;
        $this->time = microtime(true);
    }

    /**
     * @return int
     */
    public function getDamagerId(): int {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getVictimId(): int {
        return $this->vid;
    }

    /**
     * @return int
     */
    public function getTime(): int {
        return $this->time;
    }
}