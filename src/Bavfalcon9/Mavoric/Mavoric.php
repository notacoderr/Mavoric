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
use Bavfalcon9\Mavoric\misc\Flag;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use Bavfalcon9\Mavoric\Tasks\ViolationCheck;
use Bavfalcon9\Mavoric\Tasks\DiscordPost;

use Bavfalcon9\Mavoric\Cheats\{
    Speed, AutoClicker, KillAura, MultiAura, NoClip, AntiKb,
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
use pocketmine\utils\MainLogger;
use pocketmine\utils\Config;
use Bavfalcon9\Mavoric\Bans\BanHandler;
use Bavfalcon9\Mavoric\misc\Classes\CheatPercentile;
use Bavfalcon9\Mavoric\entity\SpecterInterface;
use Bavfalcon9\Mavoric\entity\SpecterPlayer;
use Bavfalcon9\Mavoric\misc\Handlers\MessageHandler;
use Bavfalcon9\Mavoric\misc\Handlers\TpsCheck;
use Bavfalcon9\Mavoric\misc\Utils;
use pocketmine\math\Vector3;

class Mavoric {
    public const AutoClicker = 0; // AutoClicker
    public const KillAura = 1; // Kill Aura
    public const MultiAura = 2;
    public const Speed = 3; // Fast movement
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

    public const EPEARL_LOCATION_BAD = '§cNo epearl glitching.';

    private $version = '1.0.0';
    private $plugin;
    private $banHandler;
    private $messageHandler;
    private $tpsCheck;
    private $cheats = [];
    private $flags = [];
    private $NPC;

    public $ignoredPlayers = [];

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->messageHandler = new MessageHandler($plugin, $this);
        $this->tpsCheck = new TpsCheck($plugin, $this);
        $this->banManager = new BanHandler($this->plugin->getDataFolder() . 'ban_data');
        $this->NPC = new NPC($plugin, $this, new SpecterInterface($plugin));
    }

    public function loadDetections() {

    }

    public function getCheats() : Array {
        return $this->cheats;
    }

    /**
     * @var int $number - AntiCheat identification Code
     * @return String
     * @deprecated
     */

    public function getCheat(int $number) : String {
        return self::getCheatName($number);
    }

    public static function getCheatName(int $number) {

    }

    /**
     * @deprecated
     */
    public static function getCheatFromString(String $name): ?float {

    }

    public function loadChecker(): ?Bool {
        $scheduler = $this->plugin->getScheduler();
        $scheduler->scheduleRepeatingTask(new ViolationCheck($this), 20);
        return false;
    }

    public function getFlag($p) {
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
        $this->getFlag($p)->clearViolations();
        if ($this->plugin->config->getNested('Autoban.vague-reasoning') === true) $reason = 'Cheating';
        return $this->plugin->banAnimation($p, $reason);
    }

    public function kick(Player $p, String $reason="Cheating") {
        if ($p === null) return;
        $name = $p->getName();
        $p->kill();
        return $p->close('', '§c[MAVORIC]: §7Kicked for: §c'.$reason);
    }


  
    public function messageStaff(String $type, $player=null, String $message='', String $appendance='') {
        if ($this->tpsCheck->isHalted() || $this->tpsCheck->isLow()) return;
        if ($type === 'detection') {
            if (in_array($player->getName(), $this->ignoredPlayers)) return;
            $m = $message;
            $cheatPercentile = CheatPercentile::getPercentile($this->getFlag($player));
            $message = "§c[MAVORIC]: §7{$player->getName()} §cwas detected for §7{$message}§c.";
            $appendance .= " Cheating: §4{$cheatPercentile}%";
            $webhook = $this->plugin->config->getNested('Webhooks.alerts');
            if ($webhook !== null && $webhook !== false) {
                $embed = [
                    'color' => 0xFF0000,
                    'title' => 'Mavoric Detection',
                    'description' => "**Player:** {$player->getName()}\n**Violation:** {$m}\n**Violation-Info:** {$this->cleanColor($appendance)}"
                ];
                if (!$this->messageHandler->isQueued($message)) $this->postWebhook($webhook, json_encode(["embeds" => [$embed]]));
            }
        } else if ($type === 'debug') {
            if (!$this->plugin->config->get('Debugging')) return;
            echo 'called';
            $message = "§c[MAVORIC]: §4[DEBUG]§r§c $message ".$appendance;
            $this->messageHandler->sendMessage($message, '');
            return;
        } else {
            $message = "§c[MAVORIC]: $message";
            $webhook = $this->plugin->config->getNested('Webhooks.alerts');
            if ($webhook !== null && $webhook !== false) {
                $embed = [
                    'color' => 0xFF0000,
                    'title' => 'Mavoric Alert',
                    'description' => "{$this->cleanColor($message)}"
                ];
                if (!$this->messageHandler->isQueued($message)) $this->postWebhook($webhook, json_encode(["embeds" => [$embed]]));
            }
        }
        $this->messageHandler->queueMessage($message, $appendance);
    }

    public function alert($sender=null, String $type, Player $player, String $cheat='None provided.') {
        $token = $this->plugin->config->getNested('Webhooks.'.$type);
        $embed = [
            'title' => ($type === 'alert-deny') ? 'Staff Denied Violation' : 'Staff Accepted Violation (banned)',
            'fields' => [
                [
                    'inline' => true,
                    'name' => 'Staff Member',
                    'value' => (!$sender) ? 'MAVORIC - CONSOLE' : $sender->getName()
                ],
                [
                    'inline' => true,
                    'name' => 'Player',
                    'value' => $player->getName()
                ],
                [
                    'inline' => true,
                    'name' => 'Violation',
                    'value' => $cheat
                ]
                ],
            'color' => ($type === 'alert-deny') ? 0xFF0000 : 0x33ffa7,
            'footer' => [
                'text' => 'Issued',
                'icon_url' => 'https://cdn.discordapp.com/attachments/602697368718147587/625249121057636383/Mavoric.png'
            ]
        ];
        if (!$token) return;
        return $this->postWebhook($token, json_encode(["embeds" => [$embed]]), (!$sender) ? 'MAVORIC - CONSOLE' : $sender->getName());
    }

    public function postWebhook(String $url, String $content, String $replyTo='MavoricAC') {
        $post = new DiscordPost($url, $content, $replyTo);
        $task = $this->getServer()->getAsyncPool()->submitTask($post);
        return;
    }

    public function checkVersion($config) {
        if (!$config) {
            MainLogger::getLogger()->critical('Config could not be found, forcefully disabled.');
            $this->getServer()->getPluginManager()->disablePlugin($this->plugin);
            return;
        }
        if (!$config->get('Version')) {
            $this->getPlugin()->saveResource('config.yml');
            MainLogger::getLogger()->critical('Config version does not match version: ' . $this->version . ' all data erased and replaced.');
        }
        if ($config->get('Version') !== $this->version) {
            MainLogger::getLogger()->info('Mavoric config version does not match plugin version. Should match version: ' . $this->version.', fixing...');
            $this->plugin->saveResource('config.yml', true);
            $new = new Config($this->plugin->getDataFolder().'config.yml');
            $old = $config->getAll();
            foreach ($old as $key=>$val) {
                $new->set($key, $val);
            }
            $new->set('Version', $this->version);
            $new->save();
            MainLogger::getLogger()->info('Mavoric config updated to v' . $this->version.'.');
        }
        MainLogger::getLogger()->info('Mavoric version matches: '.$this->version);
    }

    public function getVersion(): ?String {
        return $this->version;
    }
    
    public function getPlugin() {
        return $this->plugin;
    }

    public function getTpsCheck() {
        return $this->tpsCheck;
    }
    
    private function getServer() {
        return $this->plugin->getServer();
    }

    public function isSuppressed(Float $cheat): ?Bool {
        if (!$this->getCheatName($cheat)) return $this->plugin->config->get('Suppression');
        $mascular = $this->plugin->config->get('Suppression');
        $singular = $this->plugin->config->getNested("Cheats.{$this->getCheatName($cheat)}.suppression"); 
        if ($singular === true) return true;
        if ($singular === null) return $mascular;
        else return $singular;
    }

    public function canAutoBan(Float $cheat): ?Bool {
        if (!$this->getCheatName($cheat)) return !$this->plugin->config->getNested('Autoban.disabled');
        $mascular = !$this->plugin->config->getNested('Autoban.disabled');
        $singular = $this->plugin->config->getNested("Cheats.{$this->getCheatName($cheat)}.autoban"); 
        if ($singular === null) return $mascular;
        return $singular;
    }

    public function isEnabled(Float $cheat): ?Bool {
        if (!$this->getCheatName($cheat)) return null;

        $cheat = $this->plugin->config->getNested("Cheats.{$this->getCheatName($cheat)}.enabled");
        return ($cheat === null) ? true : $cheat;
    }
}