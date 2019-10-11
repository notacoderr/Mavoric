<?php

namespace Bavfalcon9\Mavoric;

/* Commands */
use pocketmine\plugin\PluginBase;
use pocketmine\command\{
    Command,
    CommandSender
};
use pocketmine\permission\Permission;
use pocketmine\entity\Entity;

/* Misc */
use pocketmine\{
    Player,
    Server
};
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

/* Commands */
use Bavfalcon9\Mavoric\Command\{
    alert
};
use pocketmine\utils\Config;

/* Events */
use Bavfalcon9\Mavoric\EventManager;
//use Bavfalcon9\Mavoric\misc\Tests\SpeedTest;
//use Bavfalcon9\Mavoric\misc\Lightning;

class Main extends PluginBase {
    public $EventManager;
    public $config;
    public function onEnable() {
        $this->saveResource('config.yml');
        $this->mavoric = new Mavoric($this);
        $this->EventManager = new EventManager($this);
        //$this->SpeedTest = new SpeedTest($this);
        $this->getServer()->getPluginManager()->registerEvents($this->EventManager, $this);
        //$this->getServer()->getPluginManager()->registerEvents($this->SpeedTest, $this);
        $this->loadCommands();
        //Entity::registerEntity(Lightning::class, false, ['Lightning', 'minecraft:lightning']);
        $this->mavoric->loadDetections();
        $this->mavoric->loadChecker();
        $this->config = new Config($this->getDataFolder().'config.yml');
    }

    public function banAnimation(Player $p, String $reason = 'Cheating') {
        //$this->playsound('mob.enderdragon.growl', $p);
        //$nbt = Entity::createBaseNBT($p->getPosition(), null, lcg_value() * 360, 0);
        //Entity::createEntity('Lightning', $p->getLevel(), $nbt);
        $this->getServer()->broadcastMessage("§c[MAVORIC]: {$p->getName()} was detected for {$reason} and was banned.");
        $this->EventManager->onBan($p, $reason);
        $p->close('', '§c[MAVORIC] You were banned: '.$reason);
        $this->mavBan($p, $reason);
    }

    private function mavBan(Player $p, String $reason) {
        $this->getServer()->getNameBans()->addBan($p->getName(), "$reason", null, "Mavoric");
        return;
    }

    private function loadCommands() {
        $commandMap = $this->getServer()->getCommandMap();
        $commandMap->registerAll('mavoric', [
            new alert($this)
        ]);
        $this->addPerms([
            new Permission('mavoric.command', 'No', Permission::DEFAULT_OP),
            new Permission('mavoric.alerts', 'View and use mavoric alerts.', Permission::DEFAULT_OP)
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

    public function playsound(String $sound, $player=null) {
        if ($player === null) $player = $this->getServer()->getOnlinePlayers()[0];
        $x = $player->getX();
        $y = $player->getY();
        $z = $player->getZ();
        $volume = 500;
        $pitch = 1;

        $pk = new PlaySoundPacket();
        $pk->soundName = $sound;
        $pk->x = $x;
        $pk->y = $y;
        $pk->z = $z;
        $pk->volume = $volume;
        $pk->pitch = $pitch;
        $this->getServer()->broadcastPacket($this->getServer()->getOnlinePlayers(), $pk);
    }

}