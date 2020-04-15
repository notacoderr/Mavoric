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

use pocketmine\event\Listener;
use Bavfalcon9\Mavoric\Events\Violation\ViolationChangeEvent;

class EventListener implements Listener {
    /** @var Mavoric */
    private $mavoric;
    /** @var Loader */
    private $plugin;

    public function __construct(Mavoric $mavoric, Loader $plugin) {
        $this->mavoric = $mavoric;
        $this->plugin = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function onViolationChange(ViolationChangeEvent $ev): void {
        $violation = $ev->getViolation();
        $cNotifier = $this->mavoric->getCheckNotifier();
        $cNotifier->notify("§4[MAVORIC]§4: §c{$ev->getPlayer()->getName()} §7detected for §c{$ev->getCheat()}", "§8[§7{$violation->getCheatProbability()}§f%§8]");
    }
}