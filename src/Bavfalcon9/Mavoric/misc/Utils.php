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

use pocketmine\math\Vector3;

class Utils {
    // I don't recommend doing this, but ok.
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