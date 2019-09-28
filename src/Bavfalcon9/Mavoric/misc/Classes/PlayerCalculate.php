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

use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\level\Position;

class PlayerCalculate {
    /* DEFAULTS */
    public const DEFAULT_WALKSPEED = 0.2;
    public const DEFAULT_FLYSPEED = 0.1;
    public const Gravity = [
        'min' => 0.0834,
        'max' => 0.0624,
        'diff' => 0.0834 - 0.0624
    ];
    public const Friction_Lava = 0.535;
    public const Friction_Air = 0.98;
    public const Friction_Water = 0.89;
    public const WALK_SPEED = 0.221;
    public const RUN_SPEED = 90;

    public static function calculateSpeed(int $type) {

    }

    public static function handleLagFor(int $ping, int $cheat) {

    }

    public static function getSurroundings(Player $player) : Array {
        $x = $player->getX();
        $y = $player->getY();
        $z = $player->getZ();
        $surroundings = [];
        $level = $player->getLevel();
        // Check 3 blocks in every direction
        for ($xidx = $x-1; $xidx <= $x+1; $xidx = $xidx + 1) {
            for ($zidx = $z-1; $zidx <= $z+1; $zidx = $zidx + 1) {
                for ($yidx = $y-1; $yidx <= $y; $yidx = $yidx + 1) {
                    $pos = new Vector3($xidx, $yidx, $zidx);
                    $block = $level->getBlock($pos);
                    array_push($surroundings, $block);
                }
            }
        }
        return $surroundings;
    }

    public static function isOnGround(Player $player) : ?Bool {
        $x = $player->getX();
        $y = $player->getY();
        $z = $player->getZ();
        $pos = new Vector3($x, $y - 1, $z);
        return ($player->getLevel()->getBlock($pos)->getId() !== Block::AIR);
    }

    public static function isAllAir(Array $blocks) : ?Bool {
        foreach ($blocks as $block) {
            if (Block::AIR !== $block->getId()) return false;
        }

        return true;
    }

    public static function isFallingNormal(Position $pos1, Position $pos2, float $actual) : ?Bool {
        if (floor($pos1->getY()) <= floor($pos2->getY()) && $actual > 2) return false;

        $expected = self::estimateTime($pos2->getY()) / 20;

        if (floor($expected) - floor($actual) > 1) return false;
        else return true;
    }

    /**
     * Estimate falling time
     */
    public static function estimateTime(float $y) {
        $falling_time = 0.2;
        return $y * $falling_time * 20;
    }

    public static function isLagging(Position $pos1, Position $pos2) {
        $xyz = [floor($pos1->getX()), floor($pos1->getY()), floor($pos1->getZ())];
        $xyz2 = [floor($pos2->getX()), floor($pos2->getY()), floor($pos2->getZ())];
        return $xyz === $xyz2;
    }

    public static function getFlight() {
        return;
    }

    public static function getSpeedForEffect(int $time) {
        $level_0_SPRINT = 5.5;
        $level_0_JUMP_SPRINT = 7;
        $level_0_WALK = 4.2;
        $level_9 = 16.519;
        $level_10 = 17.69;
        $level_11 = 18.3;
        $level_12 = 19.63;
        return;
    }
}