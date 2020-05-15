<?php
/***
 *      __  __                       _      
 *     |  \/  |                     (_)     
 *     | \  / | __ ___   _____  _ __ _  ___ 
 *     | |\/| |/ _` \ \ / / _ \| "__| |/ __|
 *     | |  | | (_| |\ V / (_) | |  | | (__ 
 *     |_|  |_|\__,_| \_/ \___/|_|  |_|\___|
 *                                          
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  @author Bavfalcon9
 *  @link https://github.com/Bavfalcon9/Mavoric                                  
 */
namespace Bavfalcon9\Mavoric\Cheat\Movement;

use pocketmine\Player;
use pocketmine\block\Ladder;
use pocketmine\block\Slab;
use pocketmine\block\SnowLayer;
use pocketmine\block\Stair;
use pocketmine\block\Vine;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector3;
use pocketmine\math\Facing;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Cheat\Cheat;
use Bavfalcon9\Mavoric\Cheat\CheatManager;
use Bavfalcon9\Mavoric\Utils\Handlers\PearlHandler;

class NoClipA extends Cheat {
    public function __construct(Mavoric $mavoric, int $id = -1) {
        parent::__construct($mavoric, "NoClipA", "Movement", $id, false);
    }

    public function onPlayerMove(PlayerMoveEvent $ev): void {
        $player = $ev->getPlayer();
        $AABB = $player->getBoundingBox();
        $blockA = $player->getLevel()->getBlock($player);
        $blockB = $player->getLevel()->getBlock($player->floor()->up(1));
        $throw = PearlHandler::recentThrowFromWithin($player->getName(), 4);
        $pos = (!$throw) ? null : $throw->getLandingLocation();

        if ($player->getAllowFlight()) return;
        if (($blockA->collidesWithBB($AABB) || $blockB->collidesWithBB($AABB))) {
            
            if (!$blockA->isSolid() || !$blockB->isSolid()) {
                return;
            }
            
            if ($pos !== null) {
                # Bad pearl
                $player->teleport($throw->getThrowLocation());
                return;
            }

            foreach ([$blockA, $blockB] as $block) {
                if (
                    $block instanceof Ladder
                    || $block instanceof Stair
                    || $block instanceof Slab
                    || $block instanceof SnowLayer
                    || $block instanceof Vine
                ) return;
            }

            $this->increment($player->getName(), 1);
            $this->notifyAndIncrement($player, 2, 1, [
                "Block" => $blockA->getName(),
                "Ping" => $player->getPing()
            ]);
        }
    }
}