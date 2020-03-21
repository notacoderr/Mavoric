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
use pocketmine\math\Vector3;
use pocketmine\level\Position;

class LevelUtils {
    /**
     * Gets the block relative to the provided face.
     * @param Block $block - Block to use
     * @param int $face - the face.
     * @return Block
     */
    public static function getRelativeBlock(Block $block, int $face): Block {
        $newVector = $block->getSide($face);
        return $block->getLevel()->getBlock($newVector);
    }

    /**
     * Alias for Level::getBlockAt()
     * @param Position $pos - Position to get the block at.
     * @return Block|null
     */
    public static function getBlockWhere(Position $pos): ?Block {
        $level = $pos->getLevel();
        if ($level === null) {
            return null;
        } else {
            return $level->getBlock($pos);
        }
    }
}