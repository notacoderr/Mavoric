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

namespace Bavfalcon9\Mavoric\misc\Banwaves;

class Handler {
    private $total = 0;
    private $session = []; // Cache for easier accessibility
    private $waveDir = null;

    public function __construct(String $waveDir, Bool $recursive=true) {
        $this->waveDir = $waveDir;
        $this->singular = !$recursive;
        $this->session = [];
        if (!file_exists($this->waveDir)) mkdir($this->waveDir);
        $this->loadWaves();
    }

    public function getCurrentWave(): BanWave {
        if (empty($this->session)) {
            return $this->getWave(1);
        } else {
            $index = 1;
            foreach ($this->session as $wave) {
                if (!$wave->isIssued()) {
                    return $wave;
                }
            }

            foreach ($this->session as $wave) {
                if ($index === (sizeof($this->session))) {
                    if ($wave->isIssued()) {
                        return $this->getWave($wave->getNumber() + 1);
                    } else {
                        return $wave;
                    }
                }
                $index++;
            }

            return $this->getWave(1);
        }
    }

    public function getWave(int $wave): BanWave {
        if (!isset($this->session["$wave"])) {
            $this->loadWaves();
            if (!isset($this->session["$wave"])) {
                $this->session["$wave"] = new BanWave('', $this->waveDir . '/wave-' . $wave . '.json', true);
                $this->session["$wave"]->save();
                return $this->session["$wave"];
            }
        } else {
            return $this->session["$wave"];
        }
    }

    public function getWaves(): Array {
        return $this->session;
    }

    public function saveAll(): Bool {
        foreach ($this->session as $wave) {
            $wave->save();
            continue;
        }
        return true;
    }

    private function loadWaves() {
        $waves = scandir($this->waveDir);
        foreach ($waves as $wave) {
            if ($wave === '.' || $wave === '..') continue;
            $contents = file_get_contents($this->waveDir . '/' . $wave);
            $num = explode('wave-', str_replace('.json', '', $wave))[1];
            // Prevent overwriting of cached.
            if (isset($this->session["$num"])) continue;
            $this->session["$num"] = new BanWave($contents, $this->waveDir . '/' . $wave, false);
        }
    }

    public function getTotalBans() {
        $everyone = [];
        foreach ($this->session as $wave) {
            $everyone = array_merge($wave->getPlayers());
        }
        return count($everyone);
    }

}