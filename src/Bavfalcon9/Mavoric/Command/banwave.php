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
use Bavfalcon9\Mavoric\misc\Classes\CheatPercentile;

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
        
        $waveHandler = $this->pl->mavoric->getWaveHandler();
        $selectedWave = (!isset($args[1])) ? $waveHandler->getCurrentWave() :
            (sizeof($waveHandler->getWaves()) < $args[1]) ? $waveHandler->getCurrentWave() :
            $waveHandler->getWave((int) $args[1]);

        $viewing = (strtolower($args[0]) === 'view');
        if (!$sender->isOp()) {
            $viewing = true;
        }
        if ($viewing) {
            $players = [];
            $sender->sendMessage('§7Viewing players in Wave§8:§f ' . $selectedWave->getNumber());
            foreach ($selectedWave->getPlayers() as $p=>$d) {
                $sender->sendMessage('§7- §f' . $p . '§8: §7' . implode('§f, §7', array_keys($d['cheats'])) . '§r');
            }
            return true;
        } else {
            $this->pl->mavoric->issueWaveBan($selectedWave);
            return true;
        }
        
    }
}
