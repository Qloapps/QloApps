<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class QctmCronExpressionTranslator
{
    protected $module;

    public function __construct()
    {
        $this->module = Module::getInstanceByName('qlocrontaskmanager');
    }

    public function getCronExpressionTranslation($expression)
    {
        $parts = preg_split('/\s+/', trim($expression));

        if (count($parts) !== 5) {
            return $this->module->l('Invalid cron expression');
        }

        list($minute, $hour, $day, $month, $weekday) = $parts;

        $hasFixedTime = $this->isNumeric($hour) && $this->isNumeric($minute);
        $dayMonthWildcard = ($day == '*' && $month == '*');
        $monthWeekdayWildcard = ($month == '*' && $weekday == '*');
        $everyMinuteWildcard = ($minute == '*' && $hour == '*');

        if ($expression === '* * * * *') {
            return $this->module->l('Every minute');
        }

        if (preg_match('/^\*\/(\d+)$/', $minute, $m) && $hour == '*' && $dayMonthWildcard && $weekday == '*') {
            return ((int) $m[1] === 1)
                ? $this->module->l('Every minute')
                : vsprintf($this->module->l('Every %d minutes'), array((int) $m[1]));
        }

        if ($minute == '0' && $hour == '*' && $dayMonthWildcard && $weekday == '*') {
            return $this->module->l('Every hour');
        }

        if ($minute == '0' && preg_match('/^\*\/(\d+)$/', $hour, $m) && $dayMonthWildcard && $weekday == '*') {
            return ((int) $m[1] === 1)
                ? $this->module->l('Every hour')
                : vsprintf($this->module->l('Every %d hours'), array((int) $m[1]));
        }

        if ($hasFixedTime && $dayMonthWildcard && $weekday == '*') {
            return vsprintf($this->module->l('Every day at %s'), array($this->hourLabel($hour, $minute)));
        }

        if ($hasFixedTime && $day == '*' && $weekday == '*' && preg_match('/^\*\/(\d+)$/', $month, $mm)) {
            return ((int) $mm[1] === 1)
                ? vsprintf($this->module->l('Every day every month at %s'), array($this->hourLabel($hour, $minute)))
                : vsprintf(
                    $this->module->l('Every day every %1$d months at %2$s'),
                    array((int) $mm[1], $this->hourLabel($hour, $minute))
                );
        }

        if ($hasFixedTime && $dayMonthWildcard && $weekday == '1-5') {
            return vsprintf($this->module->l('Every weekday at %s'), array($this->hourLabel($hour, $minute)));
        }

        if ($hasFixedTime && $dayMonthWildcard && $weekday == '0,6') {
            return vsprintf($this->module->l('Every weekend at %s'), array($this->hourLabel($hour, $minute)));
        }

        if ($hasFixedTime && $dayMonthWildcard && $this->isNumeric($weekday)) {
            return vsprintf(
                $this->module->l('Every %1$s at %2$s'),
                array($this->weekdayName($weekday), $this->hourLabel($hour, $minute))
            );
        }

        if (strpos($weekday, ',') !== false && $hasFixedTime && $dayMonthWildcard) {
            return vsprintf(
                $this->module->l('Every %1$s at %2$s'),
                array($this->joinNames($weekday, 'weekdayName'), $this->hourLabel($hour, $minute))
            );
        }

        if ($this->isNumeric($day) && $hasFixedTime && $monthWeekdayWildcard) {
            return vsprintf(
                $this->module->l('On the %1$s of every month at %2$s'),
                array($this->ordinal($day), $this->hourLabel($hour, $minute))
            );
        }

        if (preg_match('/^(\d+)-(\d+)$/', $day, $dm) && $hasFixedTime && $monthWeekdayWildcard) {
            return vsprintf(
                $this->module->l('Every day from the %1$s to the %2$s of each month at %3$s'),
                array($this->ordinal($dm[1]), $this->ordinal($dm[2]), $this->hourLabel($hour, $minute))
            );
        }

        if (strpos($day, ',') !== false && $hasFixedTime && $monthWeekdayWildcard) {
            return vsprintf(
                $this->module->l('On the %1$s of each month at %2$s'),
                array($this->joinOrdinals($day), $this->hourLabel($hour, $minute))
            );
        }

        if ($this->isNumeric($day) && $this->isNumeric($month) && $hasFixedTime && $weekday == '*') {
            return vsprintf(
                $this->module->l('Every %1$s %2$s at %3$s'),
                array($this->monthName($month), $this->ordinal($day), $this->hourLabel($hour, $minute))
            );
        }

        if ($everyMinuteWildcard && $dayMonthWildcard && $this->isNumeric($weekday)) {
            return vsprintf($this->module->l('Every minute on %s'), array($this->weekdayName($weekday)));
        }

        if ($everyMinuteWildcard && $dayMonthWildcard && $weekday == '1-5') {
            return $this->module->l('Every minute on weekdays');
        }

        if ($everyMinuteWildcard && $dayMonthWildcard && $weekday == '0,6') {
            return $this->module->l('Every minute on weekends');
        }

        if ($hasFixedTime) {
            $when = vsprintf($this->module->l('At %s'), array($this->hourLabel($hour, $minute)));
        } else {
            $minuteFreq = null;
            if ($minute === '*') {
                $minuteFreq = $this->module->l('Every minute');
            } elseif (preg_match('/^\*\/(\d+)$/', $minute, $m)) {
                $minuteFreq = ((int) $m[1] === 1)
                    ? $this->module->l('Every minute')
                    : vsprintf($this->module->l('Every %d minutes'), array((int) $m[1]));
            }

            if (preg_match('/^(\d+)-(\d+)\/(\d+)$/', $hour, $hs) && $this->isNumeric($minute)) {
                $when = ((int) $hs[3] === 1)
                    ? vsprintf(
                        $this->module->l('Every hour between %1$s and %2$s'),
                        array($this->hourLabel($hs[1], $minute), $this->hourLabel($hs[2], $minute))
                    )
                    : vsprintf(
                        $this->module->l('Every %1$d hours between %2$s and %3$s'),
                        array((int) $hs[3], $this->hourLabel($hs[1], $minute), $this->hourLabel($hs[2], $minute))
                    );
            } elseif ($minuteFreq !== null) {
                if ($hour === '*') {
                    $when = $minuteFreq;
                } elseif ($this->isNumeric($hour)) {
                    $when = vsprintf($this->module->l('%1$s, during hour %2$s'), array($minuteFreq, $hour));
                } elseif (preg_match('/^(\d+)-(\d+)$/', $hour, $hm)) {
                    $when = vsprintf(
                        $this->module->l('%1$s between %2$s and %3$s'),
                        array($minuteFreq, $this->hourLabel($hm[1], 0), $this->hourLabel($hm[2], 59))
                    );
                } else {
                    $when = vsprintf($this->module->l('%1$s, during hour %2$s'), array($minuteFreq, $hour));
                }
            } elseif ($this->isNumeric($minute) && $hour === '*') {
                $when = vsprintf($this->module->l('Every hour, at minute %s'), array($minute));
            } elseif ($this->isNumeric($minute) && preg_match('/^(\d+)-(\d+)$/', $hour, $hm)) {
                $when = vsprintf(
                    $this->module->l('At minute %1$s, between %2$s and %3$s'),
                    array($minute, $this->hourLabel($hm[1], $minute), $this->hourLabel($hm[2], $minute))
                );
            } else {
                $when = vsprintf($this->module->l('At minute %1$s, hour %2$s'), array($minute, $hour));
            }
        }

        $conditions = array();

        if ($day !== '*') {
            if (preg_match('/^(\d+)-(\d+)$/', $day, $dm)) {
                $conditions[] = vsprintf(
                    $this->module->l('from the %1$s to the %2$s of the month'),
                    array($this->ordinal($dm[1]), $this->ordinal($dm[2]))
                );
            } elseif (strpos($day, ',') !== false) {
                $conditions[] = vsprintf($this->module->l('on the %s of the month'), array($this->joinOrdinals($day)));
            } elseif ($this->isNumeric($day)) {
                $conditions[] = vsprintf($this->module->l('on the %s of the month'), array($this->ordinal($day)));
            } else {
                $conditions[] = vsprintf($this->module->l('on day %s of the month'), array($day));
            }
        }

        if ($month !== '*') {
            if (preg_match('/^\*\/(\d+)$/', $month, $mf)) {
                $conditions[] = ((int) $mf[1] === 1)
                    ? $this->module->l('every month')
                    : vsprintf($this->module->l('every %d months'), array((int) $mf[1]));
            } else {
                if (preg_match('/^(\d+)-(\d+)$/', $month, $mm)) {
                    $monthDesc = vsprintf(
                        $this->module->l('%1$s through %2$s'),
                        array($this->monthName($mm[1]), $this->monthName($mm[2]))
                    );
                } elseif (strpos($month, ',') !== false) {
                    $monthDesc = $this->joinNames($month, 'monthName');
                } else {
                    $monthDesc = $this->monthName($month);
                }
                $conditions[] = vsprintf($this->module->l('in %s'), array($monthDesc));
            }
        }

        if ($weekday !== '*') {
            if ($weekday === '1-5') {
                $weekdayDesc = $this->module->l('weekdays');
            } elseif ($weekday === '0,6' || $weekday === '6,0') {
                $weekdayDesc = $this->module->l('weekends');
            } elseif (preg_match('/^(\d+)-(\d+)$/', $weekday, $wm)) {
                $weekdayDesc = vsprintf(
                    $this->module->l('%1$s through %2$s'),
                    array($this->weekdayName($wm[1]), $this->weekdayName($wm[2]))
                );
            } elseif (strpos($weekday, ',') !== false) {
                $weekdayDesc = $this->joinNames($weekday, 'weekdayName');
            } else {
                $weekdayDesc = $this->weekdayName($weekday);
            }
            $conditions[] = vsprintf($this->module->l('on %s'), array($weekdayDesc));
        }

        if (empty($conditions)) {
            return $when;
        }

        return $when . ' ' . implode(', ', $conditions);
    }

    public function isNumeric($value)
    {
        return preg_match('/^\d+$/', $value) === 1;
    }

    public function weekdayName($weekday)
    {
        $weekdays = array(
            0 => $this->module->l('Sunday'),
            1 => $this->module->l('Monday'),
            2 => $this->module->l('Tuesday'),
            3 => $this->module->l('Wednesday'),
            4 => $this->module->l('Thursday'),
            5 => $this->module->l('Friday'),
            6 => $this->module->l('Saturday'),
            7 => $this->module->l('Sunday'),
        );

        return ($this->isNumeric($weekday) && isset($weekdays[(int) $weekday])) ? $weekdays[(int) $weekday] : $weekday;
    }

    public function monthName($month)
    {
        $months = array(
            1 => $this->module->l('January'),
            2 => $this->module->l('February'),
            3 => $this->module->l('March'),
            4 => $this->module->l('April'),
            5 => $this->module->l('May'),
            6 => $this->module->l('June'),
            7 => $this->module->l('July'),
            8 => $this->module->l('August'),
            9 => $this->module->l('September'),
            10 => $this->module->l('October'),
            11 => $this->module->l('November'),
            12 => $this->module->l('December'),
        );

        return ($this->isNumeric($month) && isset($months[(int) $month])) ? $months[(int) $month] : $month;
    }

    public function hourLabel($hour, $minute = null)
    {
        $h = (int) $hour;
        $suffix = $h >= 12 ? $this->module->l('PM') : $this->module->l('AM');
        $h12 = $h % 12;

        if ($h12 === 0) {
            $h12 = 12;
        }

        return $minute === null
            ? sprintf('%d %s', $h12, $suffix)
            : sprintf('%d:%02d %s', $h12, (int) $minute, $suffix);
    }

    public function joinNames($csv, $nameMethod)
    {
        $names = array();

        foreach (explode(',', $csv) as $token) {
            $names[] = $this->$nameMethod($token);
        }

        if (count($names) === 1) {
            return $names[0];
        }

        $last = array_pop($names);

        return implode(', ', $names) . ' ' . $this->module->l('and') . ' ' . $last;
    }

    public function ordinal($number)
    {
        $number = (int) $number;

        if ($number % 100 >= 11 && $number % 100 <= 13) {
            $suffix = 'th';
        } else {
            switch ($number % 10) {
                case 1:
                    $suffix = 'st';
                    break;
                case 2:
                    $suffix = 'nd';
                    break;
                case 3:
                    $suffix = 'rd';
                    break;
                default:
                    $suffix = 'th';
            }
        }

        return $number . $suffix;
    }

    public function joinOrdinals($csv)
    {
        $ordinals = array();

        foreach (explode(',', $csv) as $token) {
            $ordinals[] = $this->isNumeric($token) ? $this->ordinal($token) : $token;
        }

        if (count($ordinals) === 1) {
            return $ordinals[0];
        }

        $last = array_pop($ordinals);

        return implode(', ', $ordinals) . ' ' . $this->module->l('and') . ' ' . $last;
    }
}
