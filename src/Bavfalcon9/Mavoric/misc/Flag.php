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
namespace Bavfalcon9\Mavoric\misc;

use Bavfalcon9\Mavoric\Mavoric;

class Flag {
    private $player;
    private $flags = [];

    public function __construct(String $name) {
        $this->player = $name;
    }

    public function getViolations($cheat) {
        if (!isset($this->flags[$cheat])) return 0;
        else return $this->flags[$cheat];
    }

    public function getTotalViolations() {
        $tot = 0;
        foreach ($this->flags as $cheat=>$amount) {
            $tot = $tot + $amount;
        }
        return $tot;
    }

    public function getMostViolations() {
        $current = [
            'cheat' => -1,
            'amount' => 0
        ];
        foreach ($this->flags as $cheat=>$amount) {
            if ($amount > $current['amount']) {
                $current['cheat'] = $cheat;
                $current['amount'] = $amount;
            }
        }
        return $current['cheat'];
    }

    public function addViolation($cheat, $amount=1) {
        if (!isset($this->flags[$cheat])) {
            $this->flags[$cheat] = $amount;
        } else {
            $this->flags[$cheat] += $amount;
        }
        return $this->flags[$cheat];
    }

    public function removeViolation($cheat, $count=1) {
        if (!isset($this->flags[$cheat])) return false;
        $this->flags[$cheat]--;
        if ($this->flags[$cheat] <= 0) unset($this->flags[$cheat]);
        
        return true;
    }

    public function clearViolations() {
        $this->flags = [];
    }

    public function getFlagsByNameAndCount() {
        $newArr = [];
        foreach ($this->flags as $cheat=>$count) {
            $newArr[Mavoric::getCheatName($cheat)] = $count;
        }
        return $newArr;
    }

    public function getRaw() {
        return $this->flags;
    }
} 