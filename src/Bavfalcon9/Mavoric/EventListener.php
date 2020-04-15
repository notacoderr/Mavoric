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
    public function __construct(Loader $plugin) {
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function onViolationChange(ViolationChangeEvent $ev): void {
        $ev->getPlayer()->sendTip('[VL ' . ($ev->getCurrent() + $ev->getAmount()) . ']');
    }
}