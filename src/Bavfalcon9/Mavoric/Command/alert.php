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
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\misc\Classes\CheatPercentile;

class alert extends Command {
    private $pl;
    private $mavoric;

    public function __construct($pl) {
        parent::__construct("alert");
        $this->pl = $pl;
        $this->pl->mavoric = $pl->mavoric;
        $this->description = "Manage alerts.";
        $this->usageMessage = "/alert <confirm/deny/info> <player> <cheat>";
        $this->setPermission("mavoric.alerts");
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if (!$sender->hasPermission('mavoric.alerts') && !$sender->isOp()) {
            $sender->sendMessage(TF::RED . "Unknown command. Try /help for a list of commands");
            return true;
        }

        if (!isset($args[0])) {
            $sender->sendMessage('§c§lError: §r§c' . $this->usageMessage);
            return true;
        }

        if (!isset($args[1])) {
            $sender->sendMessage('§c§lError: §r§c' . $this->usageMessage);
            return true;
        }

        $type = strtolower($args[0]);
        $player = $this->pl->getServer()->getPlayer(implode(' ', array_slice($args, 1)));

        if ($player === null || $player->isClosed()) {
            $sender->sendMessage('§c§lError: §r§c' . 'Player invalid');
            return true;
        }
        if ($type === 'confirm') {
            $flag = $this->pl->mavoric->getFlag($player);
            $top = $flag->getMostViolations();
            if ($top === -1) {
                $sender->sendMessage('§c§lError: §r§c' . '§cPlayer does not have any violations detected.');
                return true;
            } else {
                $cheat = $this->pl->mavoric->getCheat($flag->getMostViolations());
                $currentWave = $this->pl->mavoric->getWaveHandler()->getCurrentWave();
                $data = $currentWave->addPlayer($player->getName(), '§4[AC] Illegal Client Modifications or Abuse.', $flag->getRaw(), $flag->getTotalViolations());
                $this->pl->mavoric->messageStaff(Mavoric::NOTICE, $sender->getName() . ' confirmed violations for: ' . $player->getName() . ' and added them to wave ' . $currentWave->getNumber());
                $this->pl->mavoric->banManager->saveBan($player->getName(), $flag->getFlagsByNameAndCount(), CheatPercentile::getPercentile($this->pl->mavoric->getFlag($player)), $sender->getName(), $cheat);
                $this->pl->mavoric->issueBan($player, $currentWave, $data);
                $sender->sendMessage('§aIssued ban for user and added to recent wave.');
                return true;
            }
        }
        if ($type === 'deny') {
            $flag = $this->pl->mavoric->getFlag($player);
            $top = $flag->getMostViolations();
            if ($top === -1) {
                $sender->sendMessage('§c§lError: §r§c' . 'Player does not have any violations detected.');
                return true;
            } else {
                $this->pl->mavoric->messageStaff(Mavoric::NOTICE, $sender->getName() . ' cleared violations for: ' . $player->getName());
                $this->pl->mavoric->getFlag($player)->clearViolations();
                $sender->sendMessage('§aCleared violations for specified player.');
                return true;
            }
        }
        if ($type === 'ignore') {
            return true;
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
            return true;
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
                $sender->sendMessage('§c§lError: §r§c' . '§cNo violations found for this user (Already cleared?).');
                return true;
            }
            $pretty = [];
            foreach ($data as $cheat=>$amount) {
                array_push($pretty, "§c- §f{$cheat} §r§8[§7V §f{$amount}§8]");
            }
            $sender->sendMessage("§cAll Alerts for§8: §f{$player->getName()}\n".implode("\n", $pretty));
            return true;
        }

        $sender->sendMessage($this->usageMessage);
        return false;
    }
}
