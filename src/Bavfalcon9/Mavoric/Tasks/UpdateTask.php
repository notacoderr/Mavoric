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

namespace Bavfalcon9\Mavoric\Tasks;
use pocketmine\scheduler\Task;
use pocketmine\utils\Internet;
use Bavfalcon9\Mavoric\Mavoric;

// I strongly recommend not touching this lol

class UpdateTask extends Task {
    private $mavoric;
    private $checks = 0;
    private $interval = 10; // every 10 minutes
    private $apiKey = null; // obtained at the mavoric website
    private $lastFetch;

    public function __construct(Mavoric $mavoric, $apiKey=null, int $interval) {
        $this->mavoric = $mavoric;
        $this->interval = $interval;
        $this->apiKey = $apiKey;
        if ($interval < 10) {
            $mavoric->checkUpdates(Mavoric::NO_UPDATE);
        }
        if (!$apiKey) {
            $mavoric->checkUpdates(Mavoric::NO_UPDATE);
        }
    }

    public function onRun(int $tick) {
        $site = "https://mavoric-ac.net/api/updates";
        $result = Internet::postURL($site, $this->getBody(), ["Authorization" => $this->apiKey]);
        $this->lastFetch = $result;
    }

    public function onCompletion() {
        if (!$this->lastFetch) {
            $this->mavoric->messageStaff('update', null, 'Could not check for updates.');
            return;
        }

        $data = json_decode($data, true);
        if ($data['new_version']) {
            $this->mavoric->messageStaff('update', null, 'A new version is availible! ' . $data['new_version']);
            // $this->mavoric->getIntelligence()->getUpdater()->startUpdating(true);
        } else {
            return;
        }
    }
}