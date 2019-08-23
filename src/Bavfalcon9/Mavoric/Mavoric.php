<?php
/**
 * @author Bavfalcon9 
 */

namespace Bavfalcon9\Mavoric;
use Bavfalcon9\Mavoric\misc\Flag;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use Bavfalcon9\Mavoric\Tasks\ViolationCheck;
use Bavfalcon9\Mavoric\Tasks\PlayerCheck;

use Bavfalcon9\Mavoric\Cheats\{
    Speed, KillAura, NoClip, AntiKb,
    Flight, NoSlowdown, Criticals,
    Bhop, Reach, Aimbot, AutoArmor,
    AutoSteal, AutoSword, AutoTool,
    AntiFire, AntiSlip, NoDamage,
    BackStep, FastPlace, FastBreak,
    Follow, FreeCam, FastEat, FastLadder,
    GhostReach, HighJump, JetPack, NoEffects,
    MenuWalk, Spider, Timer, Teleport
};

/* NPC DETECTION */
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\RespawnPacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use Bavfalcon9\Mavoric\entity\SpecterInterface;
use Bavfalcon9\Mavoric\entity\SpecterPlayer;
use pocketmine\math\Vector3;

class Mavoric {
    public const KillAura = 1; // Kill Arua
    public const Speed = 2; // Fast movement
    public const NoClip = 4; // going through blocks
    public const AntiKb = 5; // No knockback
    public const Flight = 6; // Flight
    public const NoSlowdown = 7; // Walking and eating? Etc
    public const Criticals = 8; // Is the player dealing extra damage?
    public const Bhop = 9; 
    public const Reach = 10; // Reaching further than allowed reach.
    public const Aimbot = 11; // Auto Aiming at entity
    public const AutoArmor = 12; // Auto equiping best armor
    public const AutoSteal = 13; // Check if the player is fast taking items out of chest
    public const AutoSword = 14; // Is the player Auto going to their best weapon
    public const AutoTool = 15; // Hard to detect & removed.
    public const AntiFire = 16; // Preventing fire damage.
    public const AntiSlip = 17; // No slipping on ice, or fast movement through webs
    public const NoDamage = 18; // No fall, no cactus, etc
    public const BackStep = 19; // Same speed going backwards
    public const FastPlace = 20;
    public const FastBreak = 21;
    public const Follow = 22; // Can false trigger
    public const FreeCam = 23;
    public const FastEat = 24;
    public const FastLadder = 25;
    public const GhostReach = 26; // Hit through blocks.
    public const HighJump = 27; // 
    public const JetPack = 28; // Similar to flight
    public const NoEffects = 29; // Take away effects? (NOT MANDATORY ON PMMP)
    public const MenuWalk = 30; // Walk with open inventory
    public const Spider = 31; // Climb walls
    public const Timer = 32; // Faster than normal
    public const Teleport = 33; // Triggered with tp-arua

    private $plugin;
    private $cheats = [];
    private $flags = [];
    private $interface;
    private $tasks = [];

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->interface = new SpecterInterface($plugin);
    }

    public function loadDetections() {
        $this->cheats[self::KillAura] = new KillAura($this->plugin, $this);
        $this->cheats[self::Speed] = new Speed($this->plugin, $this);
        $this->cheats[self::NoClip] = new NoClip($this->plugin, $this);
        $this->cheats[self::AntiKb] = new AntiKb($this->plugin, $this);
        $this->cheats[self::Flight] = new Flight($this->plugin, $this);
        $this->cheats[self::NoSlowdown] = new NoSlowdown($this->plugin, $this);
        $this->cheats[self::Criticals] = new Criticals($this->plugin, $this);
        $this->cheats[self::Bhop] = new Bhop($this->plugin, $this);
        $this->cheats[self::Reach] = new Reach($this->plugin, $this);
        $this->cheats[self::Aimbot] = new Aimbot($this->plugin, $this);
        $this->cheats[self::AutoArmor] = new AutoArmor($this->plugin, $this);
        $this->cheats[self::AutoSteal] = new AutoSteal($this->plugin, $this);
        $this->cheats[self::AutoSword] = new AutoSword($this->plugin, $this);
        $this->cheats[self::AutoTool] = new AutoTool($this->plugin, $this);
        $this->cheats[self::AntiFire] = new AntiFire($this->plugin, $this);
        $this->cheats[self::AntiSlip] = new AntiSlip($this->plugin, $this);
        $this->cheats[self::NoDamage] = new NoDamage($this->plugin, $this);
        $this->cheats[self::BackStep] = new BackStep($this->plugin, $this);
        $this->cheats[self::FastPlace] = new FastPlace($this->plugin, $this);
        $this->cheats[self::FastBreak] = new FastBreak($this->plugin, $this);
        $this->cheats[self::Follow] = new Follow($this->plugin, $this);
        $this->cheats[self::FreeCam] = new FreeCam($this->plugin, $this);
        $this->cheats[self::FastEat] = new FastEat($this->plugin, $this);
        $this->cheats[self::FastLadder] = new FastLadder($this->plugin, $this);
        $this->cheats[self::GhostReach] = new GhostReach($this->plugin, $this);
        $this->cheats[self::HighJump] = new HighJump($this->plugin, $this);
        $this->cheats[self::JetPack] = new JetPack($this->plugin, $this);
        $this->cheats[self::NoEffects] = new NoEffects($this->plugin, $this);
        $this->cheats[self::MenuWalk] = new MenuWalk($this->plugin, $this);
        $this->cheats[self::Spider] = new Spider($this->plugin, $this);
        $this->cheats[self::Timer] = new Timer($this->plugin, $this);
        $this->cheats[self::Teleport] = new Teleport($this->plugin, $this);

        foreach ($this->cheats as $cheat) {
            $this->getServer()->getPluginManager()->registerEvents($cheat, $this->plugin);
        }
        return $this->getCheats();
    }

    public function getCheats() : Array {
        return $this->cheats;
    }

    /**
     * @var int $number - AntiCheat identification Code
     * @return String
     */

    public function getCheat(int $number) : String {
        if ($number === self::KillAura)   return 'Kill Aura';
        if ($number === self::Reach)      return 'Reach';
        if ($number === self::Speed)      return 'Speed';
        if ($number === self::NoClip)     return 'NoClip';
        if ($number === self::AntiKb)     return 'Anti-Knockback';
        if ($number === self::Flight)     return 'Flight';
        if ($number === self::NoSlowdown) return 'No Slowdown';
        if ($number === self::Criticals)  return 'Criticals';
        if ($number === self::Bhop)       return 'Bunny Hop';
        if ($number === self::Aimbot)     return 'Aimbot';
        if ($number === self::AutoArmor)  return 'Auto Armor';
        if ($number === self::AutoSteal)  return 'Auto Steal';
        if ($number === self::AutoSword)  return 'Auto Sword';
        if ($number === self::AutoTool)   return 'Auto Tool';
        if ($number === self::AntiFire)   return 'AntiFire';
        if ($number === self::AntiSlip)   return 'AntiSlip';
        if ($number === self::NoDamage)   return 'NoDamage';
        if ($number === self::BackStep)   return 'Back Step';
        if ($number === self::FastPlace)  return 'Fast Place';
        if ($number === self::FastBreak)  return 'Fast Break';
        if ($number === self::Follow)     return 'Follow';
        if ($number === self::FreeCam)    return 'FreeCam';
        if ($number === self::FastEat)    return 'Fast Eat';
        if ($number === self::FastLadder) return 'Fast Ladder';
        if ($number === self::GhostReach) return 'GhostReach';
        if ($number === self::HighJump)   return 'HighJump';
        if ($number === self::JetPack)    return 'JetPack';
        if ($number === self::NoEffects)  return 'No Effects';
        if ($number === self::MenuWalk)   return 'Menu Walk';
        if ($number === self::Spider)     return 'Spider';
        if ($number === self::Timer)      return 'Timer';
        if ($number === self::Teleport)   return 'Teleport';
        return 'Cheating';
    }

    public function loadChecker() {
        $scheduler = $this->plugin->getScheduler();
        $scheduler->scheduleRepeatingTask(new ViolationCheck($this), 20 * 3);
    }

    public function getFlag(Player $p) {
        if ($p === null) return new Flag('Invalid');
        if (!isset($this->flags[$p->getName()])) {
            $this->flags[$p->getName()] = new Flag($p->getName());
        }
        return $this->flags[$p->getName()];
    }

    public function ban(Player $p, String $reason="Cheating") {
        if ($p === null) return;
        $name = $p->getName();
        $bans = $this->getServer()->getNameBans();
        if ($bans->isBanned($p)) return false;
        
        $this->killTask($p->getName());
        return $this->plugin->banAnimation($p, $reason);
    }

    public function kick(Player $p, String $reason="Cheating") {
        if ($p === null) return;
        $name = $p->getName();
        $p->kill();
        $this->killTask($p->getName());
        return $p->close('', '§4§lMavoric §f>§r§b (Kick) '.$reason);
    }


    /* NPC */
    public function startTask(Player $p, int $time) {
        if ($this->hasTaskFor($p)) return;
        $randomName = $this->generateMavName();
        $this->getServer()->addWhitelist($randomName);
        $fakePlayer = $this->interface->openSession($randomName, 'MaVoRic');
        $scheduler = $this->plugin->getScheduler();
        $task = $scheduler->scheduleRepeatingTask(new PlayerCheck($this, $time, $p, $randomName), 4);
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

    public function killTask(String $name) {
        if (!isset($this->tasks[$name])) return false;
        $id = $this->tasks[$name]['id']->getTaskId();
        unset($this->tasks[$name]);
        $scheduler = $this->plugin->getScheduler();
        $scheduler->cancelTask($id);
        $this->getServer()->removeWhitelist($name);
        return true;
    }

    private function generateMavName() {
        return 'Mav' . rand(0, 9000);
    }

    public function messageStaff(String $message) {
        $players = $this->getServer()->getOnlinePlayers();
        foreach ($players as $p) {
            if ($p->hasPermission('staffchat')) return $p->sendMessage($message);
        }
    }

    public function updateMotion(Player $p, Vector3 $newPosition, $target=null) {
        $yaw = (!$target) ? $p->getYaw()+10 : $target->getYaw();
        $pitch = (!$target) ? 0 : $target->getPitch();
        $pk = new MovePlayerPacket();
        $pk->position = $newPosition;
        $pk->yaw = $yaw; //This forces movement even if the movement is not large enough
        $pk->pitch = $pitch;
        $pk->entityRuntimeId = $p->getId();
        $pla = $this->plugin->getServer()->getOnlinePlayers();
        $this->interface->queueReply($pk, $p->getName());
        $this->plugin->getServer()->broadcastPacket($pla, $pk);
    }

    public function getPlugin() {
        return $this->plugin;
    }
    
    private function getServer() {
        return $this->plugin->getServer();
    }


}