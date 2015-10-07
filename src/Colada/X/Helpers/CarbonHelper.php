<?php

namespace Colada\X\Helpers;

use Carbon\Carbon;
use DateTime;

class CarbonHelper
{
    public static function toCarbon($date)
    {
        if (is_object($date) && ($date instanceof Carbon)) {
            $carbon = $date;
        } elseif (is_object($date) && ($date instanceof DateTime)) {
            $carbon = Carbon::instance($date);
        } else {
            $carbon = Carbon::parse($date);
        }

        return $carbon;
    }
}
