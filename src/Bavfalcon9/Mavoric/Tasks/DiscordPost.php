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

use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Internet;
use pocketmine\Server;

class DiscordPost extends AsyncTask {
    private $url;
    private $content;
    private $replyTo;

    public function __construct(String $url, String $content, $replyTo='*_') {
        $this->url = $url;
        $this->content = $content;
        $this->replyTo = $replyTo;
    }

    public function onRun() {
        $t = Internet::postURL($this->url, $this->content);
        $this->setResult($t);
    }

    public function onCompletion(Server $server) {
        $p = $server->getPlayer($this->replyTo);
        echo $this->getResult();
        if ($p === null || $p->isClosed()) return;
        else {
            if ($this->getResult() !== '') $p->sendMessage('§c[ALERT]: Failed to post ban on discord.');
            else $p->sendMessage('§aSent to discord!');
            return;
        }
    }
}