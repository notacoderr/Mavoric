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

namespace Bavfalcon9\Mavoric;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use Bavfalcon9\Mavoric\Command\{
    alert, mban, mreport, banwave
};
use pocketmine\utils\Config;
use Bavfalcon9\Mavoric\misc\Handlers\ReportHandler;

class Main extends PluginBase {
    public $EventManager;
    public $config;
    public $reportHandler;
    public $mavoric;
    
    public function onEnable() {
        $this->saveResource('config.yml');
        $this->mavoric = new Mavoric($this);
        $this->reportHandler = new ReportHandler($this->mavoric, $this);

        $this->loadCommands();
        $this->config = new Config($this->getDataFolder().'config.yml');
        $this->updateConfigs();

        $this->mavoric->checkVersion($this->config);
        $this->mavoric->loadDetections();
        $this->mavoric->loadChecker();
    }

    public function onDisable() {
        $this->mavoric->getWaveHandler()->saveAll();
        $this->getLogger()->notice('Saved Ban Waves');
    }

    private function loadCommands() {
        $commandMap = $this->getServer()->getCommandMap();
        $commandMap->registerAll('mavoric', [
            new alert($this),
            new mban($this),
            new mreport($this),
            new banwave($this)
        ]);
        $this->addPerms([
            new Permission('mavoric.command', 'No', Permission::DEFAULT_OP),
            new Permission('mavoric.alerts', 'View and use mavoric alerts.', Permission::DEFAULT_OP),
            new Permission('mavoric.report', 'Report Players', Permission::DEFAULT_OP),
            new Permission('mavoric.banwaves', 'Manage banwaves', Permission::DEFAULT_OP)
        ]);
    }

    /**
     * @param Permission[] $permissions
     */

    protected function addPerms(array $permissions) {
        foreach ($permissions as $permission) {
            $this->getServer()->getPluginManager()->addPermission($permission);
        }
    }
    private function updateConfigs() {
        if (!$this->config->get('Version')) {
            $this->config->set('Version', $this->mavoric->getVersion());
        }
    }

}