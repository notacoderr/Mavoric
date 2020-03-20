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

use pocketmine\block\Block;
use pocketmine\math\AxisAlignedBB;

class MathUtils {

    /**
     * Gets the total percentage that the AABB fills in the block, if the AABB fills the entire block, 100 is returned.
     * @param AxisAlignedBB $AABB - Axis Aligned Bounding Box of entity or other
     * @param Block $block - Block to run the check for
     * @return Percentage
     */
    public static function getPercentageFilled(AxisAlignedBB $AABB, Block $block): int {

    }

    /**
     * Gets the circumference for a given radius
     * @param float $radius - Radius to calculate
     * @return float
     */
    public static function circumference(float $radius): float {
        return (pi() * ($radius * 2));
    }

    /**
     * Gets the radius from a circumference
     * @param float $circumference - The circumference to calculate
     * @return float
     */
    public static function radiusFromCircumference(float $circumference): float {
        return ($circumference / (pi() * 2));
    }
}