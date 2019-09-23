<?php

namespace Bavfalcon9\Mavoric\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use pocketmine\Player;
use pocketmine\Server;

class alert extends Command {
    private $pl;

    public function __construct($pl) {
        parent::__construct("alert");
        $this->pl = $pl;
        $this->description = "[]";
        $this->usageMessage = "/alert <confirm/deny/ignore/unignore> <player>";
        $this->setAliases(["mban", "mav"]);
        $this->setPermission("mavoric.alerts");
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if (!$sender->hasPermission('mavoric.alerts') && !$sender->isOp()) {
            $sender->sendMessage(TF::RED . "Unknown command. Try /help for a list of commands");
            return true;
        }

        if (!isset($args[0]) || !isset($args[1])) {
            $sender->sendMessage('§cMissing type <confirm/deny/ignore/unignore> or <player>');
            return true;
        }

        $type = strtolower($args[0]);
        $player = $this->pl->getServer()->getPlayer(implode(' ', array_slice($args, 1)));

        if ($player === null || $player->isClosed()) return false;
        if ($type === 'confirm') {
            $flag = $this->pl->mavoric->getFlag($player);
            $top = $flag->getMostViolations();
            if ($top === -1) {
                $sender->sendMessage('§cPlayer does not have any violations detected.');
                return true;
            } else {
                $cheat = $this->pl->mavoric->getCheat($flag->getMostViolations());
                $this->pl->mavoric->postWebhook($sender, 'ADD', $player, $cheat);
                $this->pl->mavoric->ban($player, $cheat);
                $sender->sendMessage('§aIssued ban.');
                return true;
            }
        }
        if ($type === 'deny') {
            $flag = $this->pl->mavoric->getFlag($player);
            $top = $flag->getMostViolations();
            if ($top === -1) {
                $sender->sendMessage('§cPlayer does not have any violations detected.');
                return true;
            } else {
                $this->pl->mavoric->postWebhook($sender, 'REMOVE', $player, $this->pl->mavoric->getCheat($flag->getMostViolations()));
                $this->pl->mavoric->getFlag($player)->clearViolations();
                $sender->sendMessage('§aCleared vioations for specified player.');
                return true;
            }
        }
        if ($type === 'ignore') {
            $ignored = $this->pl->mavoric->ignoredPlayers;
            if (in_array($player->getName(), $ignored)) {
                $sender->sendMessage('§cPlayer is already ignored.');
                return true;
            } else {
                array_push($this->pl->mavoric->ignoredPlayers, $player->getName());
                $sender->sendMessage('§aPlayer is now ignored.');
                return true;
            }
        }
        if ($type === 'unignore') {
            $ignored = $this->pl->mavoric->ignoredPlayers;
            if (!in_array($player->getName(), $ignored)) {
                $sender->sendMessage('§cPlayer is not ignored.');
                return true;
            } else {
                array_splice($this->pl->mavoric->ignoredPlayers, array_search($player->getName(), $this->pl->mavoric->ignoredPlayers));
                $sender->sendMessage('§aPlayer is no longer ignored.');
                return true;
            }
        }

        $sender->sendMessage($this->usageMessage);
        return false;
    }
}
