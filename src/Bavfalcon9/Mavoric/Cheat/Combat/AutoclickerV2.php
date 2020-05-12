<?php

namespace Bavfalcon9\Mavoric\Cheat\Combat;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Cheat\Cheat;
use Bavfalcon9\Mavoric\Cheat\CheatManager;

class AutoclickerV2 extends Cheat{

    private $clicks;
    private $cps;

    public function __construct(Mavoric $mavoric) {
        parent::__construct($mavoric, "Autoclicker-BETA", "Combat", 4, true);
        $this->clicks = []; /* will *hold* how many times a player has clicked in a second */
        $this->cps = []; /* will *show* how many times the player clicked in one second */
    }

    public function receiveDataPacket(DataPacketReceiveEvent $ev): void {
        if ($ev->getPacket()::NETWORK_ID === InventoryTransactionPacket::NETWORK_ID) {
            if ($ev->getPacket()->transactionType === InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY) {
                $this->macroCheck($ev->getPlayer());
            }
        } elseif ($ev->getPacket()::NETWORK_ID === LevelSoundEventPacket::NETWORK_ID) {
            if ($ev->getPacket()->sound === LevelSoundEventPacket::SOUND_ATTACK_NODAMAGE) {
                $this->macroCheck($ev->getPlayer());
            }
        } 
    }

    private function macroCheck(Player $player){

        if (!isset($this->cps[$player->getName()])) {
            $this->cps[$player->getName()] = [];
        }

        $time = microtime(true);

        array_push($this->cps[$player->getName()], microtime(true));

        $cps = count(array_filter($this->cps[$player->getName()],  function (float $t) use ($time) : bool {
            return ($time - $t) <= 1;
        }));

        if($player->getPing() >= 250) return;
        if($player->getServer()->getTicksPerSecond() <= 17) return;
        if(!isset($this->clicks[$player->getName()])) $this->clicks[$player->getName()] = [];
        array_push($this->clicks[$player->getName()], $cps);
        if(count($this->clicks[$player->getName()]) === 40){ /* this is hard to do since some legit players can constanly click without problems */
            if(self::testMacro($this->clicks[$player->getName()], $cps) === true){
                $this->increment($player->getName(), 1);
                $msg = "§4[MAVORIC]: §c{$player->getName()} §7failed §c{$this->getName()}[{$this->getId()}]";
                $notifier = $this->mavoric->getVerboseNotifier();
                $notifier->notify($msg, "§8(§7CPS-§b{$cps}§7, Ping-§b{$player->getPing()}§8)");
                if ($this->getViolation($player->getName()) % 2 === 0) {
                    $violations = $this->mavoric->getViolationDataFor($player);
                    $violations->incrementLevel($this->getName());
                }
            }
            Server::getInstance()->getLogger()->debug($player->getName() . "'s CPS: " . implode(", ", $this->clicks[$player->getName()]));
            unset($this->clicks[$player->getName()]);
            $this->clicks[$player->getName()] = [];
        }

    }

    private static function testMacro(array $clicks, int $cps){
        if(count(array_unique($clicks)) <= 3 && end($clicks) === $cps){
            return true;
        } else {
            return false;
        }
    }

}