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
namespace Bavfalcon9\Mavoric\Command;
use Bavfalcon9\Mavoric\Core\Miscellaneous\CheatTranslation;
use Bavfalcon9\Mavoric\Mavoric;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use pocketmine\Player;
use pocketmine\Server;

class mreport extends Command {
    private $pl;
    private $plugin;

    public function __construct($pl) {
        parent::__construct("mreport");
        $this->pl = $pl;
        $this->plugin = $pl;
        $this->description = "Report a player for violating a rule.";
        $this->usageMessage = "/mreport <player> <violation>";
        $this->setPermission("mavoric.report");
        $this->setAliases(["mavreport", "mavr", "report-mav", "mavoricreport", "ac-report", "mav-report", "mavreport"]);
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if (!$sender->hasPermission('mavoric.report') && !$sender->isOp()) {
            $sender->sendMessage(TF::RED . "You do not have permission to use this command.");
            return false;
        }
        
        if (isset($args[0]) && $args[0] === 'help') {
            $sender->sendMessage('§c[REPORT] Help for reports:');
            $sender->sendMessage('§c- Basic Usage: §7/mreport "A hacker" cheat1 cheat2 cheat3');
            $sender->sendMessage('§c- §7Quotations are only required if the player whom you wish to report has a space in their name.');
            $sender->sendMessage('§c- §7When reporting, you must provide at least one cheat, however more can be specified as shown above.');
            $sender->sendMessage('§c- §7When reporting, it is important to sperate each cheat by a space and not a comma.');
            $sender->sendMessage('§c- §7When reporting, it is important you use a valid cheat, for a list of valid cheats, use §c/mreport cheats');
            $sender->sendMessage('§c- If you still have issues understanding this, please contact an administrator.');
            return true;
        }

        if (isset($args[0]) && $args[0] === 'cheats') {       
            $sender->sendMessage('§c[REPORT] List of valid cheats for reporting:');
            $sender->sendMessage('§ckill-aura:§7 The player is attacking you without looking at you.');
            $sender->sendMessage('§cautoclicker:§7 The player is clicking faster than usual, this can usually be indicated with sound.');
            $sender->sendMessage('§cspeed:§7 The player is faster than usual when moving around');
            $sender->sendMessage('§creach:§7 The player is attacking you from far distances');
            $sender->sendMessage('§cnoclip:§7 The player is moving through blocks. (As if it were air)');
            $sender->sendMessage('§cflight:§7 The player is flying in survival without /fly.');
            $sender->sendMessage('§cmulti-aura:§7 The player is attacking multiple entities at once.');
            return true;
        }

        $player = (!isset($args[0])) ? false : $this->plugin->getServer()->getPlayer($args[0]);
        $cheat = (!isset($args[1])) ? false : CheatTranslation::TranslateCheats(array_slice($args, 1));

        if (!$player) {
            $sender->sendMessage('§c[REPORT]:§f Player invalid! For help use §7/mreport help');
            return true;
        }
        if (!$cheat) {
            $sender->sendMessage('§c[REPORT]:§f Invalid cheat! Please specify 1 or more valid cheats. For help use §7/mreport help');
            return true;
        }
        if ($player->getName() === $sender->getName()) {
            $sender->sendMessage('§c[REPORT]:§f You can not report yourself.');
            return true;
        }
        $submission = $this->pl->reportHandler->submitReport($player->getName(), $cheat, $sender->getName());
        if (!$submission) {
            $sender->sendMessage('§c[REPORT]:§4 You have already reported this player.');
            return true;
        } else {
            $this->pl->mavoric->messageStaff(Mavoric::INFORM, "§7{$sender->getName()} §chas reported §7{$player->getName()} §cfor: §7".$this->getNames($cheat));
            $sender->sendMessage('§a[REPORT]: You have successfully reported §7'.$player->getName().'§a for §7'.$this->getNames($cheat));
            return true;
        }

        return true;
    }

    private function getNames(Array $cheats) {
        $name = false;
        foreach ($cheats as $cheat) {
            $c = Mavoric::getCheatName($cheat);
            if (!$c) continue;
            if (!$name) $name = $c;
            else $name .= ', '. $c;
        }
        return $name;
    }
}
