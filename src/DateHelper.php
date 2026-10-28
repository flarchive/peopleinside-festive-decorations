<?php

namespace PeopleInside\FestiveDecorations;

class DateHelper
{
    public static function today()
    {
        return new \DateTimeImmutable('today', self::get_timezone());
    }

    private static function get_timezone()
    {
        $tz = config('app.timezone') ?: 'UTC';
        try {
            return new \DateTimeZone($tz);
        } catch (\Exception $e) {
            return new \DateTimeZone('UTC');
        }
    }

    public static function md(\DateTimeImmutable $d)
    {
        return (int) $d->format('n') * 100 + (int) $d->format('j');
    }

    public static function easter($year)
    {
        $a = $year % 19;
        $b = intdiv($year, 100);
        $c = $year % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $month = intdiv($h + $l - 7 * $m + 114, 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;
        
        return self::today()->setDate($year, $month, $day)->setTime(0, 0);
    }

    public static function theme_for_today($enabled)
    {
        $today = self::today();
        $year = (int) $today->format('Y');
        $md = self::md($today);
        
        $easterSunday = self::easter($year);
        $easter_days = (int) $today->diff($easterSunday)->format('%r%a');

        $rules = [
            'capodanno'    => ($md >= 1228 || $md <= 106),
            'natale'       => ($md >= 1201 && $md <= 1227),
            'halloween'    => ($md >= 1020 && $md <= 1102),
            'carnevale'    => ($easter_days >= 47 && $easter_days <= 52),
            'sanvalentino' => ($md >= 207 && $md <= 214),
            'festadonna'   => ($md >= 306 && $md <= 308),
            'pasqua'       => ($easter_days >= -7 && $easter_days <= 1),
            'pride'        => ($md >= 601 && $md <= 630),
            'primavera'    => ($md >= 320 && $md <= 620),
            'estate'       => ($md >= 621 && $md <= 922),
            'autunno'      => ($md >= 923 && $md <= 1130),
            'inverno'      => ($md >= 1201 || $md <= 319),
        ];

        foreach ($rules as $key => $active) {
            if ($active && in_array($key, $enabled, true)) {
                return $key;
            }
        }
        return null;
    }

    public static function is_date_in_range(\DateTimeImmutable $d, $start_md, $end_md)
    {
        $cur = self::md($d);
        if ($start_md <= $end_md) {
            return ($cur >= $start_md && $cur <= $end_md);
        }
        return ($cur >= $start_md || $cur <= $end_md);
    }

    public static function parse_day_month($str, $def_d, $def_m)
    {
        $str = trim((string) $str);
        // Validazione sicura: accetta solo numeri e separatori validi
        if (preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})$/', $str, $matches)) {
            $d = max(1, min(31, (int) $matches[1]));
            $m = max(1, min(12, (int) $matches[2]));
            return $m * 100 + $d;
        }
        // Fallback sicuro ai valori predefiniti
        return $def_m * 100 + $def_d;
    }
}
