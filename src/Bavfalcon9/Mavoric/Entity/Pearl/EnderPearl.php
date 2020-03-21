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

namespace Bavfalcon9\Mavoric\Entity\Pearl;

use Bavfalcon9\Mavoric\Entity\Pearl\Events\PearlThrownEvent;
use pocketmine\item\ProjectileItem;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityIds;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;

class EnderPearl extends ProjectileItem {
	public function __construct(int $meta = 0) {
		parent::__construct(self::ENDER_PEARL, $meta, "Ender Pearl");
	}

	public function getMaxStackSize() : int {
		return 16;
	}

	public function getProjectileEntityType() : string {
		return "ThrownEnderpearl";
	}

	public function getThrowForce() : float {
		return 1.5;
	}

	public function getCooldownTicks() : int {
		return 20;
    }
    
	final public function onClickAir(Player $player, Vector3 $directionVector) : bool {
		$nbt = Entity::createBaseNBT($player->add(0, $player->getEyeHeight(), 0), $directionVector, $player->yaw, $player->pitch);
		$this->addExtraTags($nbt);

		$projectile = Entity::createEntity($this->getProjectileEntityType(), $player->getLevel(), $nbt, $player);
		if($projectile !== null){
			$projectile->setMotion($projectile->getMotion()->multiply($this->getThrowForce()));
		}

		$this->count--;

		if ($projectile instanceof Projectile) {
			$projectileEv = new ProjectileLaunchEvent($projectile);
			$projectileEv->call();
			if ($projectileEv->isCancelled()) {
				$projectile->flagForDespawn();
			} else {
                $thrownEv = new PearlThrownEvent($player, $projectile);
                $thrownEv->call();

                if ($thrownEv->isCancelled()) {
                    $projectile->flagForDespawn();
                } else {
                    $projectile->spawnToAll();
                    $player->getLevel()->broadcastLevelSoundEvent($player, LevelSoundEventPacket::SOUND_THROW, 0, EntityIds::PLAYER);
                }
			}
		} else if ($projectile !== null) {
			$projectile->spawnToAll();
		} else {
			return false;
		}

		return true;
	}
}