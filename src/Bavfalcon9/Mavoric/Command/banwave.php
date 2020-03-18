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

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use pocketmine\Player;
use pocketmine\Server;
use Bavfalcon9\Mavoric\Core\Miscellaneous\CheatPercentile;

class banwave extends Command {
    private $pl;

    public function __construct($pl) {
        parent::__construct("banwave");
        $this->pl = $pl;
        $this->description = "Manage ban waves";
        $this->usageMessage = "/banwave <issue/view> <number>";
        $this->setPermission("mavoric.banwaves");
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if (!$sender->hasPermission('mavoric.banwaves') && !$sender->isOp()) {
            $sender->sendMessage(TF::RED . "Unknown command. Try /help for a list of commands");
            return true;
        }

        if (!isset($args[0])) {
            $sender->sendMessage('§c'.$this->usageMessage);
            return true;
        }
        
        $viewing = (strtolower($args[0]) === 'view');
        $waveHandler = $this->pl->mavoric->getWaveHandler();
        $selectedWave = (!isset($args[1])) ? $waveHandler->getCurrentWave() :
            ($waveHandler->getCurrentWave()->getNumber() <  $args[1]) ? $waveHandler->getCurrentWave() :
            $waveHandler->getWave((int) $args[1]);

        if (!$sender->isOp()) {
            $viewing = true;
        }
        if ($viewing) {
            $players = [];
            $sender->sendMessage('§7Viewing players in Wave§8:§f ' . $selectedWave->getNumber());
            foreach ($selectedWave->getPlayers() as $p=>$d) {
                $sender->sendMessage('§7- §f' . $p . '§8: §7' . implode('§f, §7', array_keys($d['cheats'])) . '§r');
            }
            $sender->sendMessage('§7There are§8:§f ' . $selectedWave->getPlayerCount() . ' players §7in Wave§8:§f ' . $selectedWave->getNumber());
            if ($selectedWave->isIssued()) {
                $sender->sendMessage('§aThis wave was issued: ' . $selectedWave->issuedAtDate());
            }
            return true;
        } else {
            if (!isset($args[2])) {
                $sender->sendMessage('§c§lError: §r§c' . 'Code missing.');
                return true;
            }
            if ($args[2] !== '0658') {
                $sender->sendMessage('§c§lError: §r§c' . 'Code incorrect.');
                return true;
            }
            $this->pl->mavoric->issueWaveBan($selectedWave);
            return true;
        }
        
    }
}
