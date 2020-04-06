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
namespace Bavfalcon9\Mavoric\Cheat;

class Cheats {
    /** @var int */
    public const CHESTAURA = 1;
    /** @var int */
    public const KILLAURA  = 1;
    /** @var int */
    public const MULTIAURA = 1;
    /** @var int */
    public const AUTOCLICK = 1;
    /** @var int */
    public const AUTOARMOR = 1;
    /** @var int */
    public const AUTOSOUP = 1;
    /** @var int */
    public const AUTOAIM = 1;
    /** @var int */
    public const REACH = 1;
    /** @var int */
    public const HITBOX = 1;
    /** @var int */
    public const VELOCITY = 1;
    /** @var int */
    public const SPEED = 1;
    /** @var int */
    public const TIMER = 1;
    /** @var int */
    public const FLIGHT = 1;
    /** @var int */
    public const HIGHJUMP = 1;
    /** @var int */
    public const FREECAM = 1;
    /** @var int */
    public const GLIDE = 1;
    /** @var int */
    public const STEP = 1;
    /** @var int */
    public const WATERWALK = 1;
    /** @var int */
    public const SCAFFOLD = 1;
    /** @var int */
    public const DERP = 1;
    /** @var int */
    public const NOSTUCK = 1;
    /** @var int */
    public const NOVOID = 1;
    /** @var int */
    public const NOSLIP = 1;
    /** @var int */
    public const NOSLOWDOWN = 1;
    /** @var int */
    public const NOCLIP = 1;
    /** @var int */
    public const NOWEB = 1;
    /** @var int */
    public const NOFALL = 1;
    /** @var int */
    public const NOSWING = 1;
    /** @var int */
    public const NUKER = 1;
    /** @var int */
    public const FASTEAT = 1;

    /**
     * Generates a cheat map.
     * @return Array[]
     */
    public static function generateMap(): Array {
        return [
            self::CHESTAURA => 0,
            self::KILLAURA => 0,
            self::MULTIAURA => 0,
            self::AUTOCLICK => 0,
            self::AUTOARMOR => 0,
            self::AUTOSOUP => 0,
            self::AUTOAIM => 0,
            self::REACH => 0,
            self::HITBOX => 0,
            self::VELOCITY => 0,
            self::SPEED => 0,
            self::TIMER => 0,
            self::FLIGHT => 0,
            self::HIGHJUMP => 0,
            self::FREECAM => 0,
            self::GLIDE => 0,
            self::STEP => 0,
            self::WATERWALK => 0,
            self::SCAFFOLD => 0,
            self::DERP => 0,
            self::NOSTUCK => 0,
            self::NOVOID => 0,
            self::NOSLIP => 0,
            self::NOSLOWDOWN => 0,
            self::NOCLIP => 0,
            self::NOWEB => 0,
            self::NOFALL => 0,
            self::NOSWING => 0,
            self::NUKER => 0,
            self::FASTEAT => 0
        ];
    }
}