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
/**
 * DISCLAIMER: This code was not written by me, all credit goes to author.
 * THIS CODE IS MODFIED FROM ITS ORIGINAL CONTENT
 * Author: https://github.com/falkirks/Specter
 */

namespace Bavfalcon9\Mavoric\entity;


use Bavfalcon9\Mavoric\Main;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\network\mcpe\protocol\PlayStatusPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\RequestChunkRadiusPacket;
use pocketmine\network\mcpe\protocol\ResourcePackClientResponsePacket;
use pocketmine\network\mcpe\protocol\ResourcePacksInfoPacket;
use pocketmine\network\mcpe\protocol\RespawnPacket;
use pocketmine\network\mcpe\protocol\SetHealthPacket;
use pocketmine\network\mcpe\protocol\SetLocalPlayerAsInitializedPacket;
use pocketmine\network\mcpe\protocol\SetTitlePacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\network\SourceInterface;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\utils\UUID;

class SpecterInterface implements SourceInterface{
    /** @var  SpecterPlayer[]|\SplObjectStorage */
    private $sessions;
    /** @var  Main */
    private $mavoric;
    /** @var  array */
    private $ackStore;
    /** @var  array */
    private $replyStore;
    public function __construct(Main $mavoric){
        $this->mavoric = $mavoric;
        $this->sessions = new \SplObjectStorage();
        $this->ackStore = [];
        $this->replyStore = [];
    }
    public function start(): void {
        //NOOP
    }
    /**
     * Sends a DataPacket to the interface, returns an unique identifier for the packet if $needACK is true
     *
     * @param Player $player
     * @param DataPacket $packet
     * @param bool $needACK
     * @param bool $immediate
     *
     * @return int
     */
    public function putPacket(Player $player, DataPacket $packet, bool $needACK = false, bool $immediate = true): ?int{
        if($player instanceof SpecterPlayer){
            //$this->mavoric->getLogger()->info(get_class($packet));
            switch(get_class($packet)){
                case ResourcePacksInfoPacket::class:
                    $pk = new ResourcePackClientResponsePacket();
                    $pk->status = ResourcePackClientResponsePacket::STATUS_COMPLETED;
                    $this->sendPacket($player, $pk);
                    break;
                case TextPacket::class:
                    /** @var TextPacket $packet */
                    $type = "Unknown";
                    switch($packet->type){
                        case TextPacket::TYPE_CHAT:
                            $type = "Chat"; // warn about deprecation?
                            break;
                        case TextPacket::TYPE_RAW:
                            $type = "Message";
                            break;
                        case TextPacket::TYPE_POPUP:
                            $type = "Popup";
                            break;
                        case TextPacket::TYPE_TIP:
                            $type = "Tip";
                            break;
                        case TextPacket::TYPE_TRANSLATION:
                            $type = "Translation (with params: " . implode(", ", $packet->parameters) . ")";
                            break;
                    }
                    break;
                case SetHealthPacket::class:
                    /** @var SetHealthPacket $packet */
                    if($packet->health <= 0){
                        if($this->mavoric->getConfig()->get("autoRespawn")){
                            $pk = new RespawnPacket();
                            $this->replyStore[$player->getName()][] = $pk;
                        }
                    }else{
                        $player->spec_needRespawn = true;
                    }
                    break;
                case StartGamePacket::class:
                    $pk = new RequestChunkRadiusPacket();
                    $pk->radius = 8;
                    $this->replyStore[$player->getName()][] = $pk;
                    break;
                case PlayStatusPacket::class:
                    /** @var PlayStatusPacket $packet */
                    switch($packet->status){
                        case PlayStatusPacket::PLAYER_SPAWN:
                            /*$pk = new MovePlayerPacket();
                            $pk->x = $player->getPosition()->x;
                            $pk->y = $player->getPosition()->y;
                            $pk->z = $player->getPosition()->z;
                            $pk->yaw = $player->getYaw();
                            $pk->pitch = $player->getPitch();
                            $pk->bodyYaw = $player->getYaw();
                            $pk->onGround = true;
                            $pk->handle($player);*/
                            break;
                    }
                    break;
                case MovePlayerPacket::class:
                    /** @var MovePlayerPacket $packet */
                    $eid = $packet->entityRuntimeId;
                    if($eid === $player->getId() && $player->isAlive() && $player->spawned === true && $player->getForceMovement() !== null){
                        $packet->mode = MovePlayerPacket::MODE_NORMAL;
                        $packet->yaw += 25; //FIXME little hacky
                        $this->replyStore[$player->getName()][] = $packet;
                    }
                    break;
                case BatchPacket::class:
                    /** @var BatchPacket $packet */
                    $packet->offset = 1;
                    $packet->decode();
                    foreach($packet->getPackets() as $buf){
                        $pk = PacketPool::getPacketById(ord($buf{0}));
                        if(!$pk->canBeBatched()){
                            throw new \InvalidArgumentException("Received invalid " . get_class($pk) . " inside BatchPacket");
                        }
                        $pk->setBuffer($buf, 1);
                        $this->putPacket($player, $pk, false, $immediate);
                    }
                    break;
                case SetTitlePacket::class:
                    /** @var SetTitlePacket $packet */
                    break;
            }
            if($needACK){
                $id = count($this->ackStore[$player->getName()]);
                $this->ackStore[$player->getName()][] = $id;
                return $id;
            }
        }
        return null;
    }
    /**
     * Terminates the connection
     *
     * @param Player $player
     * @param string $reason
     *
     */
    public function close(Player $player, string $reason = "unknown reason"): void{
        $this->sessions->detach($player);
        unset($this->ackStore[$player->getName()]);
        unset($this->replyStore[$player->getName()]);
    }
    /**
     * @param string $name
     */
    public function setName(string $name): void{
        // TODO: Implement setName() method.
    }
    public function openSession($username, $address = "SPECTER", $port = 19133): bool{
        if(!isset($this->replyStore[$username])){
            $player = new SpecterPlayer($this, $address, $port);
            $this->sessions->attach($player, $username);
            $this->ackStore[$username] = [];
            $this->replyStore[$username] = [];
            $this->mavoric->getServer()->addPlayer($player);
            $pk = new LoginPk();
            $pk->username = $username;
            $pk->protocol = ProtocolInfo::CURRENT_PROTOCOL;
            $pk->clientUUID = UUID::fromData($address, $port, $username)->toString();
            $pk->clientId = 1;
            $pk->xuid = "mavoricCI-antiCheat-1ce92dnasld9al";
            $pk->identityPublicKey = "mavorasdficCI-antiCasdfheat-1ceasdf92dnasld9alde";
            $pk->clientData["SkinId"] = "Specter";
            $pk->clientData["DeviceOS"] = 1;
            $pk->clientData["ThirdPartyName"] = 'Mavoric';
            $pk->clientData["DeviceId"] = 'Mavoric#EBV';
            $pk->clientData["DeviceModel"] = 'Mavoric AntiCheat';
            $pk->clientData["UIProfile"] = 1;
            $pk->clientData["ClientRandomId"] = 'Mavoric#EBV';
            $pk->clientData["SkinData"] = base64_encode(str_repeat("\x80", 64 * 32 * 4));
            $pk->skipVerification = true;
            $this->sendPacket($player, $pk);
            $pk = new SetLocalPlayerAsInitializedPacket();
            $pk->entityRuntimeId = $player->getId();
            $this->sendPacket($player, $pk);
            return true;
        }else{
            return false;
        }
    }
    public function process() : void{
        foreach($this->ackStore as $name => $acks){
            $player = $this->mavoric->getServer()->getPlayer($name);
            if($player instanceof SpecterPlayer){
                /** @noinspection PhpUnusedLocalVariableInspection */
                foreach($acks as $id){
                    //$player->handleACK($id); // TODO method removed. Though, Specter shouldn't have ACK to fill.
                }
            }
            $this->ackStore[$name] = [];
        }
        /**
         * @var string $name
         * @var DataPacket[] $packets
         */
        foreach($this->replyStore as $name => $packets){
            $player = $this->mavoric->getServer()->getPlayer($name);
            if($player instanceof SpecterPlayer){
                foreach($packets as $pk){
                    $this->sendPacket($player, $pk);
                }
            }
            $this->replyStore[$name] = [];
        }
    }
    public function queueReply(DataPacket $pk, $player): void{
        $this->replyStore[$player][] = $pk;
    }
    public function shutdown(): void{
        // TODO: Implement shutdown() method.
    }
    public function emergencyShutdown(): void{
        // TODO: Implement emergencyShutdown() method.
    }
    private function sendPacket(SpecterPlayer $player, DataPacket $packet){
        $this->mavoric->getServer()->getPluginManager()->callEvent($ev = new DataPacketReceiveEvent($player, $packet));
        if(!$ev->isCancelled()){
            $packet->handle($player->getSessionAdapter());
        }
    }
}