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

namespace Bavfalcon9\Mavoric\Bans;

class BanHandler {
    private $total = 0;
    private $session = []; // Cache for easier accessibility
    private $singular = false;
    private $banDir = null;

    public function __construct(String $banDir, Bool $recursive=true) {
        $this->banDir = $banDir; // Set the ban dir
        $this->singular = !$recursive; // singular = 1 file - recursive = Directory/Username/Bans
        if (!file_exists($this->banDir)) mkdir($this->banDir);
    }

    public function getBansFor(String $name): ?Array {
        $d = $this->getBanDir($name);
        if (!file_exists($this->getBanDir($name))) mkdir($this->getBanDir($name));
        $bans = scandir($this->getBanDir($name));
        $allBans = [];
        foreach ($bans as $ban) {
            if ($ban === '.' || $ban === '..') continue;
            $contents = file_get_contents($this->getBanDir($name) . '/' . $ban);
            $data = json_decode($contents, true);
            array_push($allBans, $data);
        }
        return $allBans;
    }

    public function getTotalBans(): ?Float {
        return $this->total;
    }

    public function saveBans(): Bool {
        /* This is called on disable */
        
    }

    public function getBanDir(String $player): String {
        $dir = $this->banDir . '/{PLAYER}';
        return str_replace('{PLAYER}', strtolower($player), $dir);
    }

    public function saveBan(String $player, Array $violations, Float $percentile, String $moderator, String $cheat='Cheating'): ?String {
        $current = $this->getBansFor($player);
        $amount = (!$current) ? 1 : sizeOf($current) + 1;
        $dir = $this->getBanDir($player);
        $data = [
            "violations" => $violations,
            "percentile" => $percentile,
            "moderator" => ($moderator === 'MAVORIC-CONSOLE') ? false : $moderator,
            "events" => [],
            "reason" => $cheat,
            "time" => date_create()
        ];
        $data = json_encode($data);
        file_put_contents($dir . "/ban-{$amount}.json", $data);
        return "ban-{$amount}";
    }

}