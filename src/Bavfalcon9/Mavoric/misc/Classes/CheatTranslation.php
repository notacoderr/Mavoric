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
namespace Bavfalcon9\Mavoric\misc\Classes;
use Bavfalcon9\Mavoric\Mavoric;

class CheatTranslation {

    public static function TranslateCheats(Array $cheats): ?Array {
        $completed = [];

        foreach ($cheats as $cheat) {
            $cheat = str_replace(', ', '', str_replace(',', '', $cheat));
            $cheat = self::TranslateCheat($cheat);
            if (!$cheat) continue;
            else array_push($completed, $cheat);
        }
        return (!empty($completed)) ? $completed : null;
    }

    public static function TranslateCheat(String $cheat): ?float {
        $cheat = self::findExactCheat($cheat);
        return (!$cheat) ? null : Mavoric::getCheatFromString($cheat);
    }

    public static function findExactCheat(String $cheat): ?String {
        $spellings = [
            'KillAura' => ['kill-aura', 'ka', 'kr', 'kill-arua', 'aura', 'killaura', 'killarua', 'arua'],
            'Speed' => ['speed', 'fast'],
            'Flight' => ['fly', 'air', 'flight', 'fli', 'flying', 'airwalking', 'airjump'],
            'AutoClicker' => ['auto', 'fastclicker', 'autoclicker', 'clicker', 'fastclicking', 'clicks', 'auto-clicking', 'autoclicking', 'auto-clicker'],
            'MultiAura' => ['multiaura', 'multi-aura', 'ma', 'kill-aura', 'killaura'],
            'NoClip' => ['phase', 'noclip', 'walking-through-blocks', 'no-block-damage', 'walking-block'],
            'AutoArmor' => ['auto-armor', 'autoarmor', 'autoarm', 'armor'],
            'FastEat' => ['fast-eat', 'fasteat', 'quick-eating', 'faster-eating'],
            'Reach' => ['reach', 'long-arms', 'far-hits', 'hitting-far']
        ];

        $cheat = strtolower($cheat);
        foreach ($spellings as $cheatName=>$spells) {
            if (in_array($cheat, $spells)) return $cheatName;
            else continue;
        }
        return null;
    }
}