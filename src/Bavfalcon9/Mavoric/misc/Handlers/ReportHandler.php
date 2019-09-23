<?php

namespace Bavfalcon9\Mavoric\misc\Handlers;

use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Main;

class Reporthandler {
    private $reports;
    private $mavoric;
    private $plugin;

    public function __construct(Mavoric $mavoric, Main $plugin) {
        $this->plugin = $plugin;
        $this->mavoric = $mavoric;
    }

    public function submitReport($player, Array $cheats) : ?Bool {

    }

    public function getReport($player) : ?Report {
        
    }
}