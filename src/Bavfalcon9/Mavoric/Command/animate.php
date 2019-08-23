<?php

namespace Bavfalcon9\Mavoric\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use pocketmine\Player;
use pocketmine\Server;

class animate extends Command {
    private $pl;

    public function __construct($pl) {
        parent::__construct("animate");
        $this->pl = $pl;
        $this->description = "Animate";
        $this->usageMessage = "/animate <player>";
        $this->setPermission("mavoric.command");
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        $this->pl->banAnimation($sender);
        return true;
        if (!$sender->hasPermission('mavoric.command') && !$sender->isOp()) {
            $sender->sendMessage(TF::RED . "You do not have permission to use this command.");
            return false;
        }
    }
}
