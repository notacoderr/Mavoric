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

namespace Bavfalcon9\Mavoric;

use pocketmine\Player;
use Bavfalcon9\Mavoric\Tasks\PlayerCheck;
use Bavfalcon9\Mavoric\entity\SpecterInterface;

class NPC {
    private $interface;
    private $tasks = [];
    private $plugin;


    public function __construct(Main $plugin, SpecterInterface $interface) {
        $this->plugin = $plugin;
        $this->interface = $interface;
    }

    public function startTask(Player $p, int $time) {
        $allowed = $this->plugin->config->get('AllowNPC');
        if (!$allowed) return;
        if ($this->hasTaskFor($p)) return;
        $this->messageStaff('debug', null, "NPC check for player: §7{$p->getName()} §cadministered for §7{$time}§c seconds.");
        $randomName = $this->generateMavName();
        $this->getServer()->addWhitelist($randomName);
        $fakePlayer = $this->interface->openSession($randomName, 'MaVoRic');
        $scheduler = $this->plugin->getScheduler();
        $task = $scheduler->scheduleRepeatingTask(new PlayerCheck($this, $time, $p, $randomName), 1);
        $this->tasks[$randomName] = [
            'id' => $task,
            'target' => $p->getName()
        ];
        return $task;
    }

    public function hasTaskFor(Player $p) {
        foreach ($this->tasks as $ent=>$data) {
            if ($data['target'] == $p->getName()) return true;
        }
        return false;
    }

    public function getTaskFor(Player $p): ?String {
        foreach ($this->tasks as $ent=>$data) {
            if ($data['target'] == $p->getName()) return $ent;
        }
        return null;
    }

    public function killTask(String $name) {
        if (!isset($this->tasks[$name])) return false;
        $this->messageStaff('debug', null, "NPC check for player: §7{$this->tasks[$name]['target']} §ckilled.");
        $id = $this->tasks[$name]['id']->getTaskId();
        unset($this->tasks[$name]);
        $scheduler = $this->plugin->getScheduler();
        $scheduler->cancelTask($id);
        $this->getServer()->removeWhitelist($name);
        return true;
    }

    private function generateMavName() {
        return 'Mavoric' . rand(0, 9000);
    }

    public function updateMotion(Player $p, Vector3 $newPosition, $target=null) {
        $yaw = (!$target) ? $p->getYaw()+10 : $target->getYaw();
        $pitch = (!$target) ? 0 : $target->getPitch();
        $pk = new MovePlayerPacket();
        $pk->position = $newPosition;
        $pk->yaw = $yaw;
        $pk->pitch = $pitch;
        $pk->entityRuntimeId = $p->getId();
        $pla = $this->plugin->getServer()->getOnlinePlayers();
        $this->interface->queueReply($pk, $p->getName());
        $this->plugin->getServer()->broadcastPacket($pla, $pk);
    }

    private function getServer() {
        return $this->plugin->getServer();
    }

}