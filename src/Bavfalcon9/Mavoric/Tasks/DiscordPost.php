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

namespace Bavfalcon9\Mavoric\Tasks;

use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Internet;
use pocketmine\Server;

class DiscordPost extends AsyncTask {
    private $url;
    private $content;
    private $replyTo;

    public function __construct(String $url, String $content, $replyTo='Notch') {
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