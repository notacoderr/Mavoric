<?php

namespace Bavfalcon9\Mavoric\misc\Classes;

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
        $percentage = ($flag->getTotalViolations() / self::PERCENTILE_FLAG_TOTAL) * 100; // 20 being, probably cheating

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
        var_dump($percentage);
        return (floor($percentage) >= 100) ? 100 : floor($percentage);
    }
}