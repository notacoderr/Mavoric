<?php

namespace Bavfalcon9\Mavoric\misc;

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

    public function queueMessage(String $message) {
        $this->purgeOldCache();
        if (in_array($message, $this->ignored)) return;
        if (isset($this->sent[$message])) {
            if ($this->sent[$message]['time'] + 3 <= time()) {
                unset($this->sent[$message]);
                $this->sendMessage($message);
            } else return;
        } else {
            $this->sendMessage($message);
        }
    }

    public function sendMessage(String $message) {
        $this->sent[$message] = [
            'time' => time()
        ];

        $players = $this->plugin->getServer()->getOnlinePlayers();
        foreach ($players as $p) {
            if ($p->hasPermission('mavoric.alerts')) {
                if (in_array($p->getName(), $this->staffIgnored)) return;
                $p->sendMessage($message);
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