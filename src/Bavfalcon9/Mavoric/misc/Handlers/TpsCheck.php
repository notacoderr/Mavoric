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
namespace Bavfalcon9\Mavoric\misc\Handlers;

use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Main;
use pocketmine\scheduler\Task;
class TpsCheckTask extends Task {
    private $tps;
    public function __construct($tps) {
        $this->tps = $tps;
    }

    public function onRun(int $tick) {
        $this->tps->postTick($tick);
    }
}
class TpsCheck {
    private $plugin;
    private $mavoric;
    private $checks;
    private $tps;
    private $measuredTPS = 20;
    private $ticks = 0;
    private $skipped = 0;
    private $expected = 0;
    private $time = -1;
    private $halted;
    private $task;

    public function __construct(Main $plugin, Mavoric $mavoric) {
        $this->mavoric = $mavoric;
        $this->plugin = $plugin;
        $this->registerCheck();
    }

    private function registerCheck() {
        $this->task = $this->plugin->getScheduler()->scheduleRepeatingTask(new TpsCheckTask($this), 1);
        $this->tps = 20;
    }

    public function postTick(int $tick) {
        $this->tps = $this->plugin->getServer()->getTicksPerSecond();
        $this->ticks++;
        if ($this->time === -1) $this->testTime = microtime(true) - 1;
        if (floor(microTime(true) - $this->testTime) > 2 || $this->tps <= 4) {
            #$this->mavoric->messageStaff('custom', null, "WARNING: Average TPS is {$this->ticks} ticks per a second! This isn't good...");
        }
        if ($this->ticks >= 20) {
            $tps = $this->ticks / (microtime(true) - $this->testTime);
            $this->measuredTPS = $tps;
            $this->testTime = microtime(true);
            if ($this->isLow()) {
                #$this->mavoric->messageStaff('custom', null, "WARNING: Current tps is [{$tps} | {$this->tps}]");
            }
            $this->ticks = 0;
        }
        $this->expected = $tick + 1;
    }

    /**
     * Halts occur when the TPS is below 15 or is spiking.
     */
    public function isHalted() : ?Bool {
        return false;
    }

    public function isLow() : ?Bool {
        if ($this->isHalted()) return true;
        if ($this->tps <= 17) return true;
        else return false;
    }
}