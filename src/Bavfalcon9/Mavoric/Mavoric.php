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
use Bavfalcon9\Mavoric\events\MavoricEvent;
use Bavfalcon9\Mavoric\events\EventHandler;
use Bavfalcon9\Mavoric\Detections\{
    Aimbot, AutoArmor, AutoClicker, AutoSword,
    AutoTool, Bhop, FastBreak, FastEat, Flight,
    KillAura, MultiAura, NoClip, NoDamage, NoSlowdown,
    Reach, Speed, Teleport, Timer, Jesus, Jetpack
};

use pocketmine\utils\MainLogger;
use pocketmine\utils\Config;
use Bavfalcon9\Mavoric\Bans\BanHandler;
use Bavfalcon9\Mavoric\Tasks\BanWaveTask;
use Bavfalcon9\Mavoric\misc\Banwaves\Handler as WaveHandler;
use Bavfalcon9\Mavoric\misc\Banwaves\BanWave;
use Bavfalcon9\Mavoric\entity\SpecterInterface;
use Bavfalcon9\Mavoric\misc\Handlers\MessageHandler;
use Bavfalcon9\Mavoric\misc\Handlers\TpsCheck;
use Bavfalcon9\Mavoric\misc\Utils;
use pocketmine\math\Vector3;

class Mavoric {
    public const CHEATS = [
        'AutoClicker' => 0,
        'KillAura' => 1,
        'MultiAura' => 2,
        'Speed' => 3,
        'NoClip' => 4,
        'AntiKb' => 5,
        'Flight' => 6,
        'NoSlowdown' => 7,
        'Criticals' => 8,
        'Bhop' => 9,
        'Reach' => 10,
        'Aimbot' => 11,
        'AutoArmor' => 12,
        'AutoSteal' => 13,
        'AutoSword' => 14,
        'AutoTool' => 15,
        'AntiFire' => 16,
        'AntiSlip' => 17,
        'NoDamage' => 18,
        'BackStep' => 19,
        'FastPlace' => 20,
        'FastBreak' => 21,
        'Follow' => 22,
        'FreeCam' => 23,
        'FastEat' => 24,
        'FastLadder' => 25,
        'GhostReach' => 26,
        'HighJump' => 27,
        'Jesus' => 28,
        'Jetpack' => 29,
        'NoEffects' => 30,
        'MenuWalk' => 31,
        'Spider' => 32,
        'Timer' => 33,
        'Teleport' => 34
    ];
    
    /** Message Staff Vars */
    public const NOTICE = 1;
    public const INFORM = 2;
    public const ERROR = 3;
    public const FATAL = 4;
    public const WARN = 5;

    public const EPEARL_LOCATION_BAD = self::COLOR . 'c No epearl glitching.';
    public const COLOR = '§';
    public const ARROW = '→';

    /** @var String */
    private $version = '1.0.0';
    /** @var Main */
    private $plugin;
    /** @var BanHandler */
    private $banHandler;
    /** @var MessageHandler */
    private $messageHandler;
    /** @var TpsCheck */
    private $tpsCheck;
    /** @var Array[Flag] */
    private $flags = [];
    /** @var NPC */
    private $NPC;
    /** @var Array[String] */
    public $ignoredPlayers = [];
    /** @var EventHandler */
    private $eventHandler;
    /** @var WaveHandler */
    private $waveHandler;
    /** @var Array[Detection] */
    private $loadedCheats = [];

    public function __construct(Main $plugin) {
        /** Plugin Cache */
        $this->plugin = $plugin;
        /** Handle alert messages (so they dont spam staff) */
        $this->messageHandler = new MessageHandler($plugin, $this);
        /** Ticks per second check  */
        $this->tpsCheck = new TpsCheck($plugin, $this);
        /** @deprecated Handles bans */
        $this->banManager = new BanHandler($this->plugin->getDataFolder() . 'ban_data');
        /** @deprecated Handles NPC checks. */
        $this->NPC = new NPC($plugin, new SpecterInterface($plugin));
        /** Handles events that are broadcasted and translated to detections */
        $this->eventHandler = new EventHandler($this);
        /** Handles ban waves. */
        $this->waveHandler = new WaveHandler($this->plugin->getDataFolder() . 'waves');
        $this->plugin->getLogger()->notice('Mavoric is on BanWave: ' . $this->waveHandler->getCurrentWave()->getNumber());
    }

    /** 
     * @return void
     */
    public function loadDetections(): void {
        $allDetections = [
            //new Aimbot($this),
            //new AutoArmor($this),
            new AutoClicker($this),
            //new AutoSword($this),
            //new AutoTool($this),
            new FastEat($this),
            new FastBreak($this),
            new Flight($this),
            new Jesus($this),
            new JetPack($this),
            new MultiAura($this),
            new NoClip($this),
            //new NoDamage($this),
            //new NoSlowdown($this),
            new Reach($this),
            new Speed($this),
            new Teleport($this)
        ];

        foreach ($allDetections as $cheat) {
            $name = str_replace('Bavfalcon9\Mavoric\Detections\\', '', get_class($cheat));
            
            if (!$cheat->isEnabled()) {
                $this->plugin->getLogger()->notice('[CORE] Disabled detection: ' . $name);
                continue;
            }
            if ($this->isEnabled($name)) {
                $this->plugin->getLogger()->notice('Enabled detection: ' . $name);
                array_push($this->loadedCheats, $cheat);
            } else {
                $this->plugin->getLogger()->notice('Disabled detection: ' . $name);
                continue;
            }
        }
    }

    public function broadcastEvent(MavoricEvent $event) {
        foreach ($this->loadedCheats as $cheat) {
            try {
                $cheat->onEvent($event);
            } catch (Throwable $e) {
                $this->plugin->getLogger->critical('[MavoricDetection] Event broadcast failed for: ' . get_class($cheat) . '!' . "\n$e");
            }
        }
    }

    /**
     * @return Array[Detection]
     */
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

    /**
     * @var int $number - AntiCheat identification Code
     * @return String
     * @deprecated
     */

    public static function getCheatName(int $number): String {
        foreach (self::CHEATS as $cheat=>$code) {
            if ($number === $code) return $cheat;
        }
        return 'Unknown';
    }

    /**
     * @deprecated
     * @return int
     */
    public static function getCheatFromString(String $name): ?int {
        return $self::CHEATS[$name];
    }

    /**
     * @return Boolean?
     */
    public function loadChecker(): ?Bool {
        $scheduler = $this->plugin->getScheduler();
        $scheduler->scheduleRepeatingTask(new ViolationCheck($this), 20);
        return false;
    }

    /**
     * @param Player $p - Player
     * @return Flag
     */
    public function getFlag($p): Flag {
        if ($p === null) return new Flag('Invalid');
        if (!isset($this->flags[$p->getName()])) {
            $this->flags[$p->getName()] = new Flag($p->getName());
        }
        return $this->flags[$p->getName()];
    }

    /**
     * Send a message to the staff on the server.
     * @param Player $player
     * @param int $int
     * @param String $details
     */
    public function messageStaff(int $type = 1, String $message): void {
        switch ($type) {
            case self::NOTICE:
                $message = '§b[MAVORIC] [NOTICE]§8:§f ' . $message;
                default;
            break;
            case self::INFORM:
                $message = '§c[MAVORIC]§8:§7 ' . $message;
            break;
            case self::ERROR:
                $message = '§c[MAVORIC] [ERROR]§8:§f ' . $message;
            break;
            case self::FATAL:
                $message = '§4[MAVORIC] [CRITICAL]§8:§c ' . $message;
            break;
            case self::WARN:
                $message = '§4[MAVORIC] [WARNING]§8:§f ' . $message;
            break;
        }

        $this->messageHandler->queueMessage($message);
        return;
    }

    /**
     * Alert the staff on the server.
     * @param Player $player
     * @param int $int
     * @param String $details
     */
    public function alertStaff(Player $player, int $cheat, String $details='Unknown'): void {
        if ($player === null) return;
        $count = $this->getFlag($player)->getTotalViolations();
        $message = /*self::ARROW . ' ' .*/ '§c[MAVORIC]: §r§4' . $player->getName() . ' §7failed test for §c' . self::getCheatName($cheat) . '§8: ';
        $appendance = '§f' . $details . ' §r§8[§7V §f' . $count . '§8]';
        $this->messageHandler->queueMessage($message, $appendance);
    }

    /**
     * @deprecated
     */
    public function postWebhook(String $url, String $content, String $replyTo='MavoricAC') {
        $post = new DiscordPost($url, $content, $replyTo);
        $task = $this->getServer()->getAsyncPool()->submitTask($post);
        return;
    }

    /**
     * Checks the version of mavoric
     */
    public function checkVersion($config): void {
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

    public function issueWaveBan(BanWave $wave) {
        $wave->setIssued(true);
        $wave->save();
        $scheduler = $this->plugin->getScheduler();
        $scheduler->scheduleRepeatingTask(new BanWaveTask($this, $wave), 20 * 0.6);
        $this->getServer()->broadcastMessage('§4[MAVORIC] Ban Wave: ' . $wave->getNumber() . ' has started.');
    }

    public function issueBan(Player $player, BanWave $wave, Array $banData) {
        $player = $player->getName();
        $banList = $this->getServer()->getNameBans();
        $banList->addBan($player, '§4'.$banData['reason'] . ' | Wave ' . $wave->getNumber(), null, 'Mavoric');
        if ($this->getServer()->getPlayer($player)) {
            $this->getFlag($this->getServer()->getPlayer($player))->clearViolations();
            $this->getServer()->getPlayer($player)->close('', $banData['reason'] . ' | Wave ' . $wave->getNumber());
        }
        $this->getServer()->broadcastMessage('§4[MAVORIC] A player has been removed from your game for abusing or hacking. Thanks for reporting them!');
    }

    /**
     * @param Float $cheat 
     * @return Bool
     */
    public function isSuppressed(Float $cheat): ?Bool {
        if (!$this->getCheatName($cheat)) return $this->plugin->config->get('Suppression');
        $mascular = $this->plugin->config->get('Suppression');
        $singular = $this->plugin->config->getNested("Cheats.{$this->getCheatName($cheat)}.suppression"); 
        if ($singular === true) return true;
        if ($singular === null) return $mascular;
        else return $singular;
    }

    /**
     * @param Flaot $cheat
     * @return Bool
     */
    public function canAutoBan(Float $cheat): ?Bool {
        if (!$this->getCheatName($cheat)) return !$this->plugin->config->getNested('Autoban.disabled');
        $mascular = !$this->plugin->config->getNested('Autoban.disabled');
        $singular = $this->plugin->config->getNested("Cheats.{$this->getCheatName($cheat)}.autoban"); 
        if ($singular === null) return $mascular;
        return $singular;
    }

    /**
     * @param String $cheat
     * @return Bool
     */
    public function isEnabled(String $cheat): ?Bool {
        if (!isset(self::CHEATS[$cheat])) return null;
        
        $cheat = $this->plugin->config->getNested("Cheats.$cheat.enabled");
        return ($cheat === null) ? true : $cheat;
    }

    /**
     * Get the version of mavoric.
     */
    public function getVersion(): ?String {
        return $this->version;
    }
    
    /**
     * Get the plugin.
     */
    public function getPlugin() {
        return $this->plugin;
    }

    /**
     * Get tps check.
     */
    public function getTpsCheck() {
        return $this->tpsCheck;
    }

    public function getWaveHandler(): WaveHandler {
        return $this->waveHandler;
    }
    
    /**
     * Get the server
     */
    public function getServer() {
        return $this->plugin->getServer();
    }
}