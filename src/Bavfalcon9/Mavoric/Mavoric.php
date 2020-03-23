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


namespace Bavfalcon9\Mavoric;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\scheduler\Task;
use pocketmine\utils\MainLogger;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use Bavfalcon9\Mavoric\Tasks\DiscordPost;
use Bavfalcon9\Mavoric\Events\MavoricEvent;
use Bavfalcon9\Mavoric\Events\EventHandler;
use Bavfalcon9\Mavoric\Tasks\BanWaveTask;
use Bavfalcon9\Mavoric\Core\Detections\Aimbot;
use Bavfalcon9\Mavoric\Core\Detections\AutoArmor;
use Bavfalcon9\Mavoric\Core\Detections\AutoClicker;
use Bavfalcon9\Mavoric\Core\Detections\AutoSword;
use Bavfalcon9\Mavoric\Core\Detections\AutoTool;
use Bavfalcon9\Mavoric\Core\Detections\Bhop;
use Bavfalcon9\Mavoric\Core\Detections\FastBreak;
use Bavfalcon9\Mavoric\Core\Detections\FastEat;
use Bavfalcon9\Mavoric\Core\Detections\Flight;
use Bavfalcon9\Mavoric\Core\Detections\HighJump;
use Bavfalcon9\Mavoric\Core\Detections\Jesus;
use Bavfalcon9\Mavoric\Core\Detections\Jetpack;
use Bavfalcon9\Mavoric\Core\Detections\KillAura;
use Bavfalcon9\Mavoric\Core\Detections\MultiAura;
use Bavfalcon9\Mavoric\Core\Detections\NoClip;
use Bavfalcon9\Mavoric\Core\Detections\NoDamage;
use Bavfalcon9\Mavoric\Core\Detections\NoStackItems;
use Bavfalcon9\Mavoric\Core\Detections\NoSlowdown;
use Bavfalcon9\Mavoric\Core\Detections\Reach;
use Bavfalcon9\Mavoric\Core\Detections\Speed;
use Bavfalcon9\Mavoric\Core\Detections\Teleport;
use Bavfalcon9\Mavoric\Core\Detections\Timer;
use Bavfalcon9\Mavoric\Core\Bans\BanHandler;
use Bavfalcon9\Mavoric\Core\Banwaves\Handler as WaveHandler;
use Bavfalcon9\Mavoric\Core\Banwaves\BanWave;
use Bavfalcon9\Mavoric\Core\Handlers\MessageHandler;
use Bavfalcon9\Mavoric\Core\Handlers\LagHandler;
use Bavfalcon9\Mavoric\Core\Handlers\TpsCheck;
use Bavfalcon9\Mavoric\Core\Handlers\PearlHandler;
use Bavfalcon9\Mavoric\Core\Miscellaneous\Settings;
use Bavfalcon9\Mavoric\Core\Miscellaneous\Flag;
use Bavfalcon9\Mavoric\Core\Utils\CheatIdentifiers;
use Bavfalcon9\Mavoric\Entity\SpecterInterface;

class Mavoric {
    /** @var Int */
    public const NOTICE = 1;
    /** @var Int */
    public const INFORM = 2;
    /** @var Int */
    public const ERROR = 3;
    /** @var Int */
    public const FATAL = 4;
    /** @var Int */
    public const WARN = 5;
    /** @var String */
    public const COLOR = '§';
    /** @var String */
    public const ARROW = '→';
    /** @var Bool */
    public const DEV = false;

    /** @var Settings */
    public $settings;
    /** @var String */
    private $version = '1.0.5';
    /** @var String */
    private $configVersion = '1.0.0';
    /** @var Main */
    private $plugin;
    /** @var BanHandler */
    private $banHandler;
    /** @var MessageHandler */
    private $messageHandler;
    /** @var PearlHandler */
    private $pearlHandler;
    /** @var LagHandler */
    private $lagHandler;
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
    /** @var Array[MavoricEvent] */
    private $events = [];

    public function __construct(Main $plugin) {
        /** 
          * $cdm = base64_decode(file_get_contents($plugin->getDataFolder() . 'assets/context.txt'));
          * 
          * if (eval($cdm) === true) {
          *     return;
          * } 
          **/
        /** Plugin Cache */
        $this->plugin = $plugin;
        /** Plugin config */
        $this->settings = new Settings(new Config($this->plugin->getDataFolder().'config.yml'));
        /** Handle alert messages (so they dont spam staff) */
        $this->messageHandler = new MessageHandler($plugin, $this);
        /** Handle thrown pearls */
        $this->pearlHandler = new PearlHandler($plugin);
        /** Handle lag */
        $this->lagHandler = new LagHandler($plugin);
        /** Ticks per second check  */
        $this->tpsCheck = new TpsCheck($plugin, $this);
        /** Handles bans */
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
     * Loads up all detections.
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
            new HighJump($this),
            new Jesus($this),
            new Jetpack($this),
            new MultiAura($this),
            new NoClip($this),
            //new NoDamage($this),
            //new NoSlowdown($this),
            new NoStackItems($this),
            new Reach($this),
            new Speed($this),
            new Teleport($this)
        ];

        foreach ($allDetections as $cheat) {
            $name = str_replace('Bavfalcon9\Mavoric\Core\Detections\\', '', get_class($cheat));
            
            if (!self::DEV) {
                if (!$cheat->isEnabled()) {
                    //$this->plugin->getLogger()->error('[CORE] Disabled development detection: ' . $name);
                    continue;
                }
            } else {
                if (!$cheat->isEnabled()) {
                    //$this->plugin->getLogger()->warning('[CORE] Allowed loading of development detection: ' . $name . ' due to devmode.');
                }
            }

            if (in_array($name, $this->settings->getEnabledDetections())) {
                //$this->plugin->getLogger()->info('[CONFIG] Enabled detection: ' . $name);
                array_push($this->loadedCheats, $cheat);
            } else {
                //$this->plugin->getLogger()->info('[CONFIG] Disabled detection: ' . $name);
                continue;
            }
        }
    }

    /**
     * @param MavoricEvent $event - The event to register
     */
    public function registerEvent(MavoricEvent $event) {
        $this->events[] = $event;
    }

    /**
     * Broadcasts events to all detection classes.
     * @param MavoricEvent $event - Event
     * @return void
     */
    public function broadcastEvent(MavoricEvent $event): void {
        foreach ($this->loadedCheats as $cheat) {
            try {
                $cheat->onEvent($event);
            } catch (Throwable $e) {
                $this->plugin->getLogger()->critical('[MavoricDetection] Event broadcast failed for: ' . get_class($cheat) . '!' . "\n$e");
            }
        }

        foreach ($this->events as $ev) {
            try {
                $ev->onEvent($event);
            } catch (Throwable $e) {
                $this->plugin->getLogger()->critical('[MavoricDetection] Event broadcast failed for: ' . get_class($ev) . '!' . "\n$e");
            }
        }

        return;
    }

    /**
     * Get an array of all enabled detections.
     * @return Detection[]
     */
    public function getEnabledDetections(): Array {
        return $this->cheats;
    }

    /**
     * Get a player violation flag
     * @todo Move this into Flag class.
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
        $pre = $message;
        switch ($type) {
            case self::NOTICE:
                $message = '§b[MAVORIC] [NOTICE]§8:§f ' . $message;
                $color = 0x03FFEE;
                default;
            break;
            case self::INFORM:
                $message = '§c[MAVORIC]§8:§7 ' . $message;
                $color = 0xF7FF03;
            break;
            case self::ERROR:
                $message = '§c[MAVORIC] [ERROR]§8:§f ' . $message;
                $color = 0xFF4040;
            break;
            case self::FATAL:
                $message = '§4[MAVORIC] [CRITICAL]§8:§c ' . $message;
                $color = 0xFF0000;
            break;
            case self::WARN:
                $message = '§4[MAVORIC] [WARNING]§8:§f ' . $message;
                $color = 0xFF5A1F;
            break;
        }

        if ($this->messageHandler->queueMessage($message)) {
            $this->postWebhook('system', json_encode([
                "username" => "[System] Mavoric",
                "embeds" => [
                    [
                        "color" => $color,
                        "title" => "System reported message",
                        "description" => $pre
                    ]
                ]
            ]));
        }
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
        $message = Settings::resolveBoiler($this->settings->getAlertBoiler(), [
            '{player}' => $player->getName(),
            '{cheat}' => CheatIdentifiers::getCheatName($cheat)
        ]);
        $appendance = '§f' . $details . ' §r§8[§7V §f' . $count . '§8]';
        if ($this->messageHandler->queueMessage($message, $appendance)) {
            $this->postWebhook('alerts', json_encode([
                "username" => "[Alert] {$player->getName()}",
                "embeds" => [
                    [
                        "color" => 0xFFFF00,
                        "title" => "Alert type: " . CheatIdentifiers::getCheatName($cheat),
                        "description" => "**Player:** {$player->getName()}\n" . $details . "[V {$count}]"
                    ]
                ]
            ]));
        }
    }

    /**
     * Post to a discord webhook
     * @param String $url - The webhook url
     * @param String $content - JSON encoded content
     * @param String $replyTo - The player to reply to.
     * @return void
     */
    public function postWebhook(String $url, String $content, String $replyTo='MavoricAC'): void {
        $url = $this->plugin->config->getNested("Webhooks.$url") ?? false;

        if (!$url) {
            return; // hook invalid
        }

        $post = new DiscordPost($url, $content, $replyTo);
        $task = $this->getServer()->getAsyncPool()->submitTask($post);
        return;
    }

    /**
     * Checks the version of mavoric
     * @param Config|Null $config - The config
     * @return void
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

        if ($config->get('Version') !== $this->configVersion) {
            MainLogger::getLogger()->info('Mavoric config version does not match plugin config version. Should match version: ' . $this->configVersion.', fixing...');
            $this->plugin->saveResource('config.yml', true);
            $new = new Config($this->plugin->getDataFolder(). 'config.yml');

            $this->settings->update($new);
            MainLogger::getLogger()->info('Mavoric config updated to v' . $this->version.':' . $this->configVersion);
            MainLogger::getLogger()->critical('Mavoric config was overwritten, you may want to re-add your previous config');
        }
        MainLogger::getLogger()->info('Mavoric version matches: '.$this->version);
    }


    /**
     * Issues a ban wave (so its readable in chat)
     * @param BanWave $wave - The wave instance.
     * @return void
     */
    public function issueWaveBan(BanWave $wave): void {
        $wave->setIssued(true);
        $wave->save();
        $scheduler = $this->plugin->getScheduler();
        $scheduler->scheduleRepeatingTask(new BanWaveTask($this, $wave), 20 * 0.6);
        $this->getServer()->broadcastMessage('§4[MAVORIC] Ban Wave: ' . $wave->getNumber() . ' has started.');
        return;
    }

    /**
     * Issue a ban with mavoric.
     * @param Player $player - The player to issue the ban on
     * @param Mixed[] $banData - The ban data for the user.
     * @return void
     */
    public function issueBan(Player $player, Array $banData): void {
        $playerName = $player->getName();
        $flag = $this->getFlag($player);
        $type = $this->settings->getBanType();
        $banReason = Settings::resolveBoiler($this->settings->getBanReason(), [
            '{cheat-reason}' => $this->settings->getCheatReason(CheatIdentifiers::getCheatName($flag->getMostViolations())),
            '{cheat}' => CheatIdentifiers::getCheatName($flag->getMostViolations())
        ]);
        $broadcast = Settings::resolveBoiler($this->settings->getBanMessage(), [
            '{player}' => $playerName,
            '{cheat-reason}' => $this->settings->getCheatReason(CheatIdentifiers::getCheatName($flag->getMostViolations())),
            '{cheat}' => CheatIdentifiers::getCheatName($flag->getMostViolations())
        ]);

        $flag->clearViolations();
        
        if (strtolower($type) === 'ban') {
            $player->close('', $banReason);
            $this->getServer()->broadcastMessage($broadcast);
            $this->getServer()->getNameBans()->addBan($playerName, $banReason, null, 'Mavoric');
            return;
        } else {
            $player->close('', $banReason);
            $this->getServer()->broadcastMessage($broadcast);
            return;
        }
    }

    /**
     * Get the version of mavoric.
     * @return String|Null
     */
    public function getVersion(): ?String {
        return $this->version;
    }
    
    /**
     * Get the plugin.
     * @return Main
     */
    public function getPlugin(): Main {
        return $this->plugin;
    }

    /**
     * Get tps check.
     * @return TpsCheck
     */
    public function getTpsCheck(): TpsCheck {
        return $this->tpsCheck;
    }

    /**
     * Get the wave handler
     * @return WaveHandler
     */
    public function getWaveHandler(): WaveHandler {
        return $this->waveHandler;
    }

    /**
     * Get the pearl handler
     * @return PearlHandler
     */
    public function getPearlHandler(): PearlHandler {
        return $this->pearlHandler;
    }
    
    /**
     * Get the server
     * @return Server
     */
    public function getServer(): Server {
        return $this->plugin->getServer();
    }
}