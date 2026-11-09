<?php

namespace PeopleInside\FestiveDecorations;

use DateTimeImmutable;
use DateTimeZone;

class Decor
{
    const P = 'peopleinside-festive.';

    /** title: emoji sx/dx | menu: emoji voci | strip: festone | logo: [emoji, lato] | accent */
    public static function themes(): array
    {
        return [
            'natale'       => ['title' => ['🎄', '⭐'], 'menu' => '❄️', 'strip' => '🎄 ⭐ ❄️ 🎁 🔔', 'logo' => ['🎅', 'left'], 'accent' => '#c62828'],
            'capodanno'    => ['title' => ['🎆', '🥂'], 'menu' => '🎇', 'strip' => '🎆 🥂 ✨ 🎉', 'logo' => ['🎉', 'right'], 'accent' => '#f9a825'],
            'halloween'    => ['title' => ['🎃', '🦇'], 'menu' => '🕸️', 'strip' => '🎃 🦇 👻 🕸️ 🕷️ 🍬', 'logo' => ['🎃', 'right'], 'accent' => '#ff7518'],
            'carnevale'    => ['title' => ['🎭', '🎉'], 'menu' => '🎊', 'strip' => '🎭 🎉 🎊 🃏 🎈', 'logo' => ['🎭', 'right'], 'accent' => '#8e24aa'],
            'sanvalentino' => ['title' => ['💘', '💘'], 'menu' => '💗', 'strip' => '💖 💕 💘 🌹', 'logo' => ['💘', 'right'], 'accent' => '#e91e63'],
            'festadonna'   => ['title' => ['🌼', '🌼'], 'menu' => '🌼', 'strip' => '🌼 💐 🌼 💛', 'logo' => ['🌼', 'right'], 'accent' => '#fbc02d'],
            'pasqua'       => ['title' => ['🐣', '🐰'], 'menu' => '🥚', 'strip' => '🐣 🥚 🐰 🌷 🐇', 'logo' => ['🐣', 'right'], 'accent' => '#7cb342'],
            'festamamma'   => ['title' => ['💐', '💖'], 'menu' => '💐', 'strip' => '💐 💖 🌷 💝', 'logo' => ['💐', 'right'], 'accent' => '#d81b60'],
            'pride'        => ['title' => ['🏳️‍🌈', '🌈'], 'menu' => '🌈', 'strip' => '🏳️‍🌈 🌈 💖 🦄', 'logo' => ['🏳️‍🌈', 'right'], 'accent' => '#ab47bc'],
            'primavera'    => ['title' => ['🌷', '🦋'], 'menu' => '🌼', 'strip' => '🌷 🌼 🦋 🌱 🐝', 'logo' => ['🦋', 'right'], 'accent' => '#43a047'],
            'estate'       => ['title' => ['☀️', '🌻'], 'menu' => '🌊', 'strip' => '☀️ 🌻 🌊 🍉 🐚', 'logo' => ['☀️', 'right'], 'accent' => '#f9a825'],
            'autunno'      => ['title' => ['🍂', '🍁'], 'menu' => '🍄', 'strip' => '🍂 🍁 🌰 🍄 🍃', 'logo' => ['🍂', 'right'], 'accent' => '#b5651d'],
            'inverno'      => ['title' => ['❄️', '⛄'], 'menu' => '❄️', 'strip' => '❄️ ⛄ ✨ 🧣', 'logo' => ['⛄', 'right'], 'accent' => '#4fa3d9'],
            'cuori'        => ['title' => ['🌸', '🌸'], 'menu' => '💖', 'strip' => '🌸 💖 🌿 🕊️', 'logo' => ['💖', 'right'], 'accent' => '#e91e63'],
        ];
    }

    public static function eventKeys(): array
    {
        return ['capodanno', 'natale', 'halloween', 'carnevale', 'sanvalentino', 'festadonna', 'pasqua', 'festamamma', 'pride', 'primavera', 'estate', 'autunno', 'inverno', 'cuori'];
    }

    public static function booleanKeys(): array
    {
        $keys = ['enabled', 'heading', 'logo', 'menu', 'xmas_hats', 'fx_toggle'];
        foreach (self::eventKeys() as $k) {
            $keys[] = 'ev_' . $k;
        }
        return $keys;
    }

    public static function defaults(): array
    {
        $d = [
            'enabled' => '1', 'theme' => 'auto', 'timezone' => 'Europe/Rome',
            'heading' => '1', 'heading_sel' => '.Hero-title',
            'logo' => '1', 'logo_sel' => '.Header-title a',
            'menu' => '1', 'menu_host' => '#header', 'menu_items_sel' => '.sideNav li > a',
            'effect' => 'auto',
            'xmas_start' => '01/12', 'xmas_end' => '06/01', 'xmas_hats' => '1',
            'snow_start' => '25/10', 'snow_end' => '07/01', 'snow_count' => 140, 'snow_max' => 180,
            'ff_start' => '08/01', 'ff_end' => '24/10', 'ff_count' => 20, 'ff_speed' => 3, 'ff_mode' => 'always',
            'fx_toggle' => '1', 'custom_css' => '',
        ];
        $on = ['natale', 'halloween', 'capodanno', 'sanvalentino', 'pride', 'primavera', 'estate', 'autunno', 'inverno'];
        foreach (self::eventKeys() as $k) {
            $d['ev_' . $k] = in_array($k, $on, true) ? '1' : '0';
        }
        return $d;
    }

    public static function easter(int $year, DateTimeZone $tz): DateTimeImmutable
    {
        $a = $year % 19; $b = intdiv($year, 100); $c = $year % 100;
        $d = intdiv($b, 4); $e = $b % 4; $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4); $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $month = intdiv($h + $l - 7 * $m + 114, 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;
        return (new DateTimeImmutable('today', $tz))->setDate($year, $month, $day)->setTime(0, 0);
    }

    public static function md(DateTimeImmutable $d): int
    {
        return (int) $d->format('n') * 100 + (int) $d->format('j');
    }

    /** "GG/MM" => MMDD */
    public static function parseMd(?string $s, int $dd, int $mm): int
    {
        $str = trim((string) $s);
        if ($str !== '' && preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})$/', $str, $x)) {
            $dd = max(1, min(31, (int) $x[1]));
            $mm = max(1, min(12, (int) $x[2]));
        }
        return $mm * 100 + $dd;
    }

    public static function inRange(int $cur, int $start, int $end): bool
    {
        return $start <= $end ? ($cur >= $start && $cur <= $end) : ($cur >= $start || $cur <= $end);
    }

    public static function themeForToday(DateTimeImmutable $today, array $enabled): ?string
    {
        $year = (int) $today->format('Y');
        $md = self::md($today);
        $easter = (int) $today->diff(self::easter($year, $today->getTimezone()))->format('%r%a');

        $firstW = (int) date('w', mktime(0, 0, 0, 5, 1, $year));
        $secondSunday = 1 + ((7 - $firstW) % 7) + 7;
        $motherTarget = $today->setDate($year, 5, $secondSunday)->setTime(0, 0);
        $mother = (int) $today->diff($motherTarget)->format('%r%a');

        $rules = [
            'capodanno'    => ($md >= 1228 || $md <= 106),
            'natale'       => ($md >= 1201 && $md <= 1227),
            'halloween'    => ($md >= 1020 && $md <= 1102),
            'carnevale'    => ($easter >= 47 && $easter <= 52),
            'sanvalentino' => ($md >= 207 && $md <= 214),
            'festadonna'   => ($md >= 306 && $md <= 308),
            'pasqua'       => ($easter >= -1 && $easter <= 7),
            'festamamma'   => ($mother >= 0 && $mother <= 6),
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
        return in_array('cuori', $enabled, true) ? 'cuori' : null;
    }
}
