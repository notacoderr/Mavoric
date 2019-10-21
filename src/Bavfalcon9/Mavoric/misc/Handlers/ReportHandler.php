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