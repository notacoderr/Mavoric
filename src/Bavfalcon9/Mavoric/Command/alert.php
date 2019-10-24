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
        $this->usageMessage = "/alert <confirm/deny/ignore/unignore/info> <player>";
        $this->setPermission("mavoric.alerts");
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if (!$sender->hasPermission('mavoric.alerts') && !$sender->isOp()) {
            $sender->sendMessage(TF::RED . "Unknown command. Try /help for a list of commands");
            return true;
        }

        if (!isset($args[0])) {
            $sender->sendMessage('§cInclude <confirm/deny/info/ignore/unignore>');
            return true;
        }

        if (!isset($args[0])) {
            $sender->sendMessage('§cInclude a player');
            return true;
        }

        $type = strtolower($args[0]);
        $player = $this->pl->getServer()->getPlayer(implode(' ', array_slice($args, 1)));

        if ($player === null || $player->isClosed()) return $sender->sendMessage('§cPlayer invalid');
        if ($type === 'confirm') {
            $flag = $this->pl->mavoric->getFlag($player);
            $top = $flag->getMostViolations();
            if ($top === -1) {
                $sender->sendMessage('§cPlayer does not have any violations detected.');
                return true;
            } else {
                $cheat = $this->pl->mavoric->getCheat($flag->getMostViolations());
                $this->mav->banManager->saveBan($player->getName(), $flag->getFlagsByNameAndCount(), CheatPercentile::getPercentile($this->getFlag($player)), 'MAVORIC', $cheat);
                $this->pl->mavoric->alert($sender, 'alert-grant', $player, $cheat);
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
                $this->pl->mavoric->alert($sender, 'alert-deny', $player, $this->pl->mavoric->getCheat($flag->getMostViolations()));
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
        if ($type === 'info' || $type === 'history') {
            $flag = $this->pl->mavoric->getFlag($player);
            $data = $flag->getFlagsByNameAndCount();
            if (empty($data)) {
                $sender->sendMessage('§cNo violations detected for this user.');
                return true;
            }
            $pretty = [];
            foreach ($data as $cheat=>$amount) {
                array_push($pretty, "§f- §c{$cheat} : §7{$amount}");
            }
            $sender->sendMessage("§c=== [ALERT HISTORY FOR: §7{$player->getName()}§c] ===\n".implode("\n", $pretty));
            return true;
        }

        $sender->sendMessage($this->usageMessage);
        return false;
    }
}
