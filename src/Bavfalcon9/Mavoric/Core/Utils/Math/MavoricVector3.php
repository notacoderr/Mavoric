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
namespace Bavfalcon9\Mavoric\Core\Utils\Math;

use pocketmine\math\Vector3;

class MavoricVector3 extends Vector3 {

    public function __construct(int $x, int $y, int $z) {
        parent::_construct($x, $y, $z);    
    }

    /**
     * Draws and returns a line.
     * @param Vector3 $vector - The vector to draw the line to
     * @return Vector3[] - Array of all positions in the line
     */
    public function drawLineTo(Vector3 $vector): Array {
        // y = mx+b
        $vector = clone $vector;

    }
}