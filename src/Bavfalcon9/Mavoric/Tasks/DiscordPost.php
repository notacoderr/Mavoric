<?php

namespace Bavfalcon9\Mavoric\Tasks;

use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Internet;
use pocketmine\Server;

class DiscordPost extends AsyncTask {
    private $message;
    private $webhook;
    private $player;

    public function __construct($webhook, $embed, $player='Notch') {
        $msg = [
            'embeds' => [$embed]
        ];
        $this->message = json_encode($msg);
        $this->webhook = $webhook;
        $this->player = $player;
    }

    public function onRun() {
        $t = Internet::postURL($this->webhook, $this->message);
        $this->setResult($t);
    }

    public function onCompletion(Server $server) {
        $p = $server->getPlayer($this->player);
        if ($p === null || $p->isClosed()) return;
        else {
            if ($this->getResult() !== '') $p->sendMessage('§c[ALERT]: Failed to post ban on discord.');
            else $p->sendMessage('§aSent to discord!');
            return;
        }
    }
}