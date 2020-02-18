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

namespace Bavfalcon9\Mavoric\entity;
use pocketmine\Server;
use specter\Specter;

class DummyPlayer{
    private $server;
    public function __construct($name, $address = "SPECTER", $port = 19133, Server $server = null){
        $this->name = $name;
        $this->server = $server === null ? Server::getInstance() : $server;
        if(!$this->getSpecter()->getInterface()->openSession($name, $address, $port)){
            throw new \Exception("Failed to open session.");
        }
    }
	/**
	 * @return null|SpecterPlayer
	 */
    public function getPlayer(){
        $p = $this->server->getPlayer($this->name);
        if($p instanceof SpecterPlayer){
            return $p;
        }
        else{
            return null;
        }
    }
    public function close(){
        $p = $this->getPlayer();
        if($p !== null) {
            $p->close("", "client disconnect.");
        }
    }
    /**
     * @return null|Specter
     * @throws \Exception
     */
    protected function getSpecter(){
        $plugin = $this->server->getPluginManager()->getPlugin("Specter");
        if($plugin !== null && $plugin->isEnabled()){
            return $plugin;
        }
        else{
            throw new \Exception("Specter is not available.");
        }
    }
}