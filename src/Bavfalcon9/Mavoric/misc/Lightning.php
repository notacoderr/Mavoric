<?php

namespace Bavfalcon9\Mavoric\misc;
use pocketmine\block\Liquid;
use pocketmine\entity\{
	Animal, Living
};
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
class Lightning extends Animal {
	public const NETWORK_ID = self::LIGHTNING_BOLT;
	public $doneDamage = false;
	public $width = 0.3;
	public $length = 0.9;
	public $height = 1.8;
	protected $age = 0;
	public function getName(): string{
		return "Lightning";
	}
	public function onUpdate(int $currentTick): bool{
		if(!$this->doneDamage){
			$this->doneDamage = true;
			// Tnx Genisys
			if(true){
				$fire = Item::get(Item::FIRE)->getBlock();
				$oldBlock = $this->getLevel()->getBlock($this);
				if($oldBlock instanceof Liquid){
				}elseif($oldBlock->isSolid()){
					$v3 = new Vector3($this->x, $this->y + 1, $this->z);
				}else{
					$v3 = new Vector3($this->x, $this->y, $this->z);
				}
				$fire->setDamage(11); // Only one random tick away till a chance of despawn ;)
				if(isset($v3)) $this->getLevel()->setBlock($v3, $fire);
				foreach($this->level->getNearbyEntities($this->growAxis($this->boundingBox, 6, 6, 6), $this) as $entity){
					if($entity instanceof Living){
						$distance = $this->distance($entity);
						$distance = ($distance > 0 ? $distance : 1);
						$k = 5;
						$damage = $k / $distance;
						$ev = new EntityDamageByEntityEvent($this, $entity, 16, $damage); // LIGHTNING
						$entity->attack($ev);
						$entity->setOnFire(mt_rand(3, 8));
					}
				}
			}
			$spk = new PlaySoundPacket();
			$spk->soundName = "ambient.weather.lightning.impact";
			$spk->x = $this->getX();
			$spk->y = $this->getY();
			$spk->z = $this->getZ();
			$spk->volume = 500;
			$spk->pitch = 1;
			foreach($this->level->getPlayers() as $p){
				$p->dataPacket($spk);
			}
		}
		if($this->age > 6 * 20){
			$this->flagForDespawn();
		}
		$this->age++;
		return parent::onUpdate($currentTick);
	}
	private function growAxis(AxisAlignedBB $axis, $x, $y, $z){
		return new AxisAlignedBB($axis->minX - $x, $axis->minY - $y, $axis->minZ - $z, $axis->maxX + $x, $axis->maxY + $y, $axis->maxZ + $z);
	}
}
