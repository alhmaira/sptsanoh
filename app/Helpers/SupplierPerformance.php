<?php

namespace App\Helpers;

class SupplierPerformance
{

    public static function getRankLabel($rank_score): string
    {
        switch ((int) $rank_score) {

            case 25:
                return 'A';

            case 10:
                return 'B';

            case 5:
                return 'C';

            default:
                return '-';
        }
    }



    public static function getFppkLabel($fppk): string
    {
        switch ((int) $fppk) {

            case 0:
                return 'NO PROBLEM';

            case 10:
                return 'DELAY';

            case 20:
                return 'NO REPLY';

            default:
                return '-';
        }
    }



    public static function gradeFromScore($score, string $type = 'quality'): string
    {
        $s = (float) ($score ?? 0);


        if($type === 'quality'){

            if($s == 100) return 'A';

            if($s >= 80) return 'B';

            if($s >= 50) return 'C';

            return 'D';

        }


        if($s >=95) return 'A';

        if($s >=80) return 'B';

        if($s >=60) return 'C';

        return 'D';
    }



    public static function getOTDText($otd): string
    {

        switch((int)$otd){

            case 0:
                return 'No Delay';

            case 2:
                return 'Delay 1 day';

            case 4:
                return 'Delay 2 days';

            case 6:
                return 'Delay 3 days';

            case 10:
                return 'Delay > 3 days';

            default:
                return '-';
        }

    }

public static function calculateQtyIndex($fulfillment): int
{
    $pct = (float) ($fulfillment ?? 0);

    if ($pct >= 95) return 0;
    if ($pct >= 85) return 2;
    if ($pct >= 75) return 4;
    if ($pct >= 65) return 6;

    return 8;
}



public static function getMethodText($del_method): string
{
    return ((int)$del_method) === 4 
        ? 'ABNORMAL' 
        : 'NORMAL';
}



public static function calculatePremiumIndex($premium): int
{
    $rp = (float)($premium ?? 0);

    if ($rp <= 0) return 0;
    if ($rp <= 500000) return 2;
    if ($rp <= 1000000) return 4;
    if ($rp <= 3000000) return 6;

    return 8;
}



public static function getDPSText($dps): string
{
    switch((int)$dps){

        case 0:
            return 'NO PROBLEM';

        case 10:
            return 'DELAY';

        case 20:
            return 'NO REPLY';

        default:
            return '-';
    }
}

}