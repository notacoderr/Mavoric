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

namespace Bavfalcon9\Mavoric\Core\Detections;

use Bavfalcon9\Mavoric\Main;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Core\Utils\CheatIdentifiers;
use Bavfalcon9\Mavoric\events\MavoricEvent;
use Bavfalcon9\Mavoric\events\player\PlayerClick;
use Bavfalcon9\Mavoric\events\player\PlayerMove;
use Bavfalcon9\Mavoric\events\player\PlayerTeleport;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;
use pocketmine\Level\Position;
use pocketmine\math\Vector3;
use pocketmine\block\BlockIds;
use pocketmine\block\Stair;
use pocketmine\block\Slab;
use pocketmine\block\SnowLayer;
use pocketmine\block\Ladder;
use pocketmine\block\Vine;

use pocketmine\{
    Player,
    Server
};

/* API CHANGE (Player) */

class NoClip implements Detection {
    /** @var Mavoric */
    private $mavoric;
    /** @var Main */
    private $plugin;

    public function __construct(Mavoric $mavoric) {
        $this->plugin = $mavoric->getPlugin();
        $this->mavoric = $mavoric;
    }

    public function onEvent(MavoricEvent $event): void {
        /**
         * @var PlayerMove event
         */
        if ($event instanceof PlayerMove) {
            //if (!$event->isMoved()) return;
            $blockA = $event->getBlocks()[1];
            $blockB = $event->getBlocks()[0];
            $player = $event->getPlayer();
            $AABB = $player->getBoundingBox();

            if ($player->isSpectator()) return;
            if ($player->isCreative()) return;

            $pearlHandler = $this->mavoric->getPearlHandler();
            $throw = $pearlHandler->getMostRecentThrowFrom($player->getName());
            $pos = null;

            if ($throw !== null) {
                if ($throw->getLandingTime() + 4 <= time()) {
                    $pos = $throw->getLandingLocation();
                }
            }

            if (($blockA->collidesWithBB($AABB) || $blockB->collidesWithBB($AABB))) {
                if (!$blockA->isSolid() || !$blockB->isSolid()) {
                    return;
                }
                
                if ($event->isTeleport()) {
                    return;
                }

                if ($pos !== null) {
                    $player->sendMessage($this->mavoric->settings->getBadPearlMessage());
                    $player->teleport($event->getFrom());
                    return;
                }

                if ($blockA instanceof Slab || $blockA instanceof Stair || $blockA instanceof SnowLayer || $blockA instanceof Ladder || $blockA instanceof Vine) {
                    return;
                }

                if ($blockB instanceof Slab || $blockB instanceof Stair || $blockB instanceof SnowLayer || $blockB instanceof Ladder || $blockB instanceof Vine) {
                    return;
                }

                /**
                 * TO DO: Check whether the player has moved from one sand to another. (this is badddd)
                 */
                if ($blockA->getId() === BlockIds::SAND || $blockB->getId() === BlockIds::SAND) {
                    return;
                }
                if ($blockA->getId() === BlockIds::GRAVEL || $blockB->getId() === BlockIds::GRAVEL) {
                    return;
                }
                if ($blockA->getId() === BlockIds::ANVIL || $blockB->getId() === BlockIds::ANVIL) {
                    return;
                }
                
                $event->issueViolation(CheatIdentifiers::CODES['NoClip']);
                $event->sendAlert('NoClip', 'Illegal movement, player moved while colliding with a block.');
            }
        }
    }

    /** 
     * @return Bool
     */
    public function isEnabled(): Bool {
        return true;
    }
}