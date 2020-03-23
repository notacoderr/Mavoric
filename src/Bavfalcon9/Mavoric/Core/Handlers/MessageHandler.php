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
use Bavfalcon9\Mavoric\Mavoric;

class MessageHandler {
    private $plugin;
    private $mavoric;
    private $queue = [];
    private $sent = [];
    private $ignored = [];
    private $staffIgnored = [];

    public function __construct($plugin, $mavoric) {
        $this->plugin = $plugin;
        $this->mavoric = $mavoric;
    }

    public function isQueued(String $message) {
        $this->purgeOldCache();
        return isset($this->sent[$message]);
    }

    public function queueMessage(String $message, String $append='') {
        $this->purgeOldCache();
        if (in_array($message, $this->ignored)) {
            return false;
        }
        if (isset($this->sent[$message])) {
            if ($this->sent[$message]['time'] + 3 <= time()) {
                unset($this->sent[$message]);
                $this->sendMessage($message, $append);
                return true;
            } else {
                return false;
            }
        } else {
            $this->sendMessage($message, $append);
            return true;
        }
    }

    public function sendMessage(String $message, $append) {
        $this->sent[$message] = [
            'time' => time()
        ];
        $this->plugin->getLogger()->info($message.$append);
        $players = $this->plugin->getServer()->getOnlinePlayers();
        foreach ($players as $p) {
            if (Mavoric::DEV) {
                $p->sendMessage($message.$append);
            } else {
                if ($p->hasPermission('mavoric.alerts')) {
                    if (in_array($p->getName(), $this->staffIgnored)) return;
                    $p->sendMessage($message.$append);
                }
            }
        }
    }

    public function ignoreMessage(String $message) {
        if (in_array($this->ignored)) return true;
        else {
            array_push($this->ignored, $message);
            return true;
        }
    }

    public function purgeOldCache() {
        foreach ($this->sent as $key=>$val) {
            if ($val['time'] + 4 <= time()) unset($this->sent[$key]);
        }
    }
}