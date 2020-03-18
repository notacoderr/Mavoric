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

namespace Bavfalcon9\Mavoric\Core\Miscellaneous;

use Bavfalcon9\Mavoric\misc\Flag;
use Bavfalcon9\Mavoric;

class CheatPercentile {
    public const PERCENTILE_FLAG_TOTAL = 45;
    public const PERCENTILE_FLAG_INDIV = 35;
    /**
     * Returns a percentage of cheating based on all violations
     */
    public static function getEstimatedPercentile(Flag $flag): ?float {
        $most = $flag->getViolations($flag->getMostViolations());
        $percentage = ($flag->getTotalViolations() / self::PERCENTILE_FLAG_TOTAL) * 100;

        if ($most >= 35) return 100;
        return (floor($percentage) >= 100) ? 100 : floor($percentage);
    }

    public static function getPercentile(Flag $flag): ?float {
        $percentage = 0;
        $raw = $flag->getRaw();
        foreach ($flag->getRaw() as $cheat=>$count) {
            $temp_perc = ($count / self::PERCENTILE_FLAG_INDIV) * 100;
            $percentage += $temp_perc;
        }
        return (floor($percentage) >= 100) ? 100 : floor($percentage);
    }
}