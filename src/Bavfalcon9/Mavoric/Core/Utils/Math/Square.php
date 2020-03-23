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
use pocketmine\level\Level;
use pocketmine\block\Block;

class Square {
    /** @var Vector3[] */
    private $vectors;

    /**
     * @param int $size - The size of the square
     * @param Vector3|Null $center - The center of the square
     * @return Square|Null
     */
    public static function make(int $size = 1, Vector3 $center = null): Square {
        $center = $center ?? new Vector3(0, 0, 0);
        $vectors = [];

        for ($xiter = $center->x - $size; $xiter <= $center->x + $size; $xiter += $size) {
            // iterate through Z pos
            for ($ziter = $center->z - $size; $ziter <= $center->z + $size; $ziter += $size) {
                // iterate through Y
                for ($yiter = $center->y - $size; $yiter <= $center->y + $size; $yiter += $size) {
                    $vectors[] = new Vector3($xiter, $yiter, $ziter);
                }
            }
        }

        return new Square($vectors);
    }

    /**
     * There is no validation so this could technically be anything
     */
    public function __construct(Array $vectors) {
        $this->vectors = $vectors;
    }

    /**
     * Return an array of blocks from square.
     * @param Level $level - Level
     * @return Block[]
     */
    public function getBlocks(Level $level): Array {
        $blocks = [];

        foreach ($this->vectors as $vector) {
            $blocks[] = $level->getBlock($vector);
        }

        return $blocks;
    }

    public function getVectors(): Array {
        return $this->vectors;
    }
}