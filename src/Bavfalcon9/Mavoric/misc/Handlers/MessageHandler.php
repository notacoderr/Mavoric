<?php
/***
 *      __  __                       _      
 *     |  \/  |                     (_)     
 *     | \  / | __ ___   _____  _ __ _  ___ 
 *     | |\/| |/ _` \ \ / / _ \| '__| |/ __|
 *     | |  | | (_| |\ V / (_) | |  | | (__ 
 *     |_|  |_|\__,_| \_/ \___/|_|  |_|\___|
 *                                          
 *   THIS CODE IS TO NOT BE REDISTRUBUTED
 *   @author MavoricAC
 *   @copyright Everything is copyrighted to their respective owners.
 *   @link https://github.com/Olybear9/Mavoric                                  
 */
namespace Bavfalcon9\Mavoric\misc\Handlers;

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

    public function queueMessage(String $message, String $append='') {
        $this->purgeOldCache();
        if (in_array($message, $this->ignored)) return;
        if (isset($this->sent[$message])) {
            if ($this->sent[$message]['time'] + 3 <= time()) {
                unset($this->sent[$message]);
                $this->sendMessage($message, $append);
            } else return;
        } else {
            $this->sendMessage($message, $append);
        }
    }

    public function sendMessage(String $message, $append) {
        $this->sent[$message] = [
            'time' => time()
        ];
        $this->plugin->getLogger()->info($message.$append);
        $players = $this->plugin->getServer()->getOnlinePlayers();
        foreach ($players as $p) {
            if ($p->hasPermission('mavoric.alerts')) {
                if (in_array($p->getName(), $this->staffIgnored)) return;
                $p->sendMessage($message.$append);
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
            if ($val['time'] + 10 <= time()) unset($this->sent[$key]);
        }
    }
}