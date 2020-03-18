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

namespace Bavfalcon9\Mavoric\misc;

use pocketmine\math\Vector3;

class Utils {
    public static function drawCircle(Vector3 $centerPoint, int $radius=2, int $steps=20): ?Array {
        $points = [];
        $speed = 2 * pi() / 4;
        for ($i = 0; $i < $steps; $i++) {
            $angle = $speed * $i;
            array_push($points, self::circlePoint($centerPoint, $radius, $angle));
        }
        return $points;
    }

    public static function circlePoint(Vector3 $center, float $radius, float $angleRad): ?Vector3 {
        $x = $center->x + $radius * cos($angleRad);
        $z = $center->z + $radius * sin($angleRad);
        return new Vector3($x, $center->y, $z);
    }
}