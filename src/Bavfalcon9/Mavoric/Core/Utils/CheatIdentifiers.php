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
namespace Bavfalcon9\Mavoric\Core\Utils;

class CheatIdentifiers {
    /** @var String=>int[] */
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
        'Teleport' => 34,
        'NoStackItems' => 35
    ];

    /**
     * @var int $number - AntiCheat identification Code
     * @return String
     */
    public static function getCheatName(int $number): String {
        foreach (self::CHEATS as $cheat=>$code) {
            if ($number === $code) return $cheat;
        }
        return 'Unknown';
    }

    /**
     * @var int $number - AntiCheat identification Code
     * @return String
     */
    public static function getCheatIdentity(String $name): ?int {
        return self::CHEATS[$name];
    }
}