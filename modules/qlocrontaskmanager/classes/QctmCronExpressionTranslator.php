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
    const MODULE_NAME = 'qlocrontaskmanager';

    const WEEKDAYS = array(
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
        7 => 'Sunday',
    );

    const MONTHS = array(
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December',
    );

    public static function translate($expression)
    {
        $parts = preg_split('/\s+/', trim($expression));

        if (count($parts) !== 5) {
            return self::trans('Invalid cron expression');
        }

        list($minute, $hour, $day, $month, $weekday) = $parts;

        if ($expression === '* * * * *') {
            return self::trans('Every minute');
        }

        if (preg_match('/^\*\/(\d+)$/', $minute, $m)
            && $hour == '*' && $day == '*' && $month == '*' && $weekday == '*') {
            return self::trans('Every %d minutes', (int) $m[1]);
        }

        if ($minute == '0' && $hour == '*' && $day == '*' && $month == '*' && $weekday == '*') {
            return self::trans('Every hour');
        }

        if ($minute == '0'
            && preg_match('/^\*\/(\d+)$/', $hour, $m)
            && $day == '*' && $month == '*' && $weekday == '*') {
            return self::trans('Every %d hours', (int) $m[1]);
        }

        if (self::isNumeric($hour) && self::isNumeric($minute) && $day == '*' && $month == '*' && $weekday == '*') {
            return self::trans('Every day at %s', self::hourLabel($hour, $minute));
        }

        if (self::isNumeric($hour) && self::isNumeric($minute) && $day == '*' && $weekday == '*'
            && preg_match('/^\*\/(\d+)$/', $month, $mm)) {
            return self::trans(
                'Every day every %1$d months at %2$s',
                array((int) $mm[1], self::hourLabel($hour, $minute))
            );
        }

        if (self::isNumeric($hour) && self::isNumeric($minute) && $day == '*' && $month == '*' && $weekday == '1-5') {
            return self::trans('Every weekday at %s', self::hourLabel($hour, $minute));
        }

        if (self::isNumeric($hour) && self::isNumeric($minute) && $day == '*' && $month == '*' && $weekday == '0,6') {
            return self::trans('Every weekend at %s', self::hourLabel($hour, $minute));
        }

        if (self::isNumeric($hour) && self::isNumeric($minute) && $day == '*' && $month == '*' && self::isNumeric($weekday)) {
            return self::trans(
                'Every %1$s at %2$s',
                array(self::weekdayName($weekday), self::hourLabel($hour, $minute))
            );
        }

        if (strpos($weekday, ',') !== false
            && self::isNumeric($hour) && self::isNumeric($minute) && $day == '*' && $month == '*') {
            return self::trans(
                'Every %1$s at %2$s',
                array(self::joinNames($weekday, 'weekdayName'), self::hourLabel($hour, $minute))
            );
        }

        if (self::isNumeric($day) && self::isNumeric($hour) && self::isNumeric($minute) && $month == '*' && $weekday == '*') {
            return self::trans(
                'On the %1$s of every month at %2$s',
                array(self::ordinal($day), self::hourLabel($hour, $minute))
            );
        }

        if (preg_match('/^(\d+)-(\d+)$/', $day, $dm)
            && self::isNumeric($hour) && self::isNumeric($minute) && $month == '*' && $weekday == '*') {
            return self::trans(
                'Every day from the %1$s to the %2$s of each month at %3$s',
                array(self::ordinal($dm[1]), self::ordinal($dm[2]), self::hourLabel($hour, $minute))
            );
        }

        if (strpos($day, ',') !== false
            && self::isNumeric($hour) && self::isNumeric($minute) && $month == '*' && $weekday == '*') {
            return self::trans(
                'On the %1$s of each month at %2$s',
                array(self::joinOrdinals($day), self::hourLabel($hour, $minute))
            );
        }

        if (self::isNumeric($day) && self::isNumeric($month) && self::isNumeric($hour) && self::isNumeric($minute) && $weekday == '*') {
            return self::trans(
                'Every %1$s %2$s at %3$s',
                array(self::monthName($month), self::ordinal($day), self::hourLabel($hour, $minute))
            );
        }

        if ($minute == '*' && $hour == '*' && $day == '*' && $month == '*' && self::isNumeric($weekday)) {
            return self::trans('Every minute on %s', self::weekdayName($weekday));
        }

        if ($minute == '*' && $hour == '*' && $day == '*' && $month == '*' && $weekday == '1-5') {
            return self::trans('Every minute on weekdays');
        }

        if ($minute == '*' && $hour == '*' && $day == '*' && $month == '*' && $weekday == '0,6') {
            return self::trans('Every minute on weekends');
        }

        if (self::isNumeric($minute) && self::isNumeric($hour)) {
            $when = self::trans('At %s', self::hourLabel($hour, $minute));
        } else {
            $minuteFreq = null;
            if ($minute === '*') {
                $minuteFreq = self::trans('Every minute');
            } elseif (preg_match('/^\*\/(\d+)$/', $minute, $m)) {
                $minuteFreq = self::trans('Every %d minutes', (int) $m[1]);
            }

            if (preg_match('/^(\d+)-(\d+)\/(\d+)$/', $hour, $hs) && self::isNumeric($minute)) {
                $when = self::trans(
                    'Every %1$d hours between %2$s and %3$s',
                    array((int) $hs[3], self::hourLabel($hs[1], $minute), self::hourLabel($hs[2], $minute))
                );
            } elseif ($minuteFreq !== null) {
                if ($hour === '*') {
                    $when = $minuteFreq;
                } elseif (self::isNumeric($hour)) {
                    $when = self::trans('%1$s, during hour %2$s', array($minuteFreq, $hour));
                } elseif (preg_match('/^(\d+)-(\d+)$/', $hour, $hm)) {
                    $when = self::trans(
                        '%1$s between %2$s and %3$s',
                        array($minuteFreq, self::hourLabel($hm[1], 0), self::hourLabel($hm[2], 59))
                    );
                } else {
                    $when = self::trans('%1$s, during hour %2$s', array($minuteFreq, $hour));
                }
            } elseif (self::isNumeric($minute) && $hour === '*') {
                $when = self::trans('Every hour, at minute %s', $minute);
            } elseif (self::isNumeric($minute) && preg_match('/^(\d+)-(\d+)$/', $hour, $hm)) {
                $when = self::trans(
                    'At minute %1$s, between %2$s and %3$s',
                    array($minute, self::hourLabel($hm[1], $minute), self::hourLabel($hm[2], $minute))
                );
            } else {
                $when = self::trans('At minute %1$s, hour %2$s', array($minute, $hour));
            }
        }

        $conditions = array();

        if ($day !== '*') {
            if (preg_match('/^(\d+)-(\d+)$/', $day, $dm)) {
                $conditions[] = self::trans(
                    'from the %1$s to the %2$s of the month',
                    array(self::ordinal($dm[1]), self::ordinal($dm[2]))
                );
            } elseif (strpos($day, ',') !== false) {
                $conditions[] = self::trans('on the %s of the month', self::joinOrdinals($day));
            } elseif (self::isNumeric($day)) {
                $conditions[] = self::trans('on the %s of the month', self::ordinal($day));
            } else {
                $conditions[] = self::trans('on day %s of the month', $day);
            }
        }

        if ($month !== '*') {
            if (preg_match('/^\*\/(\d+)$/', $month, $mf)) {
                $conditions[] = self::trans('every %d months', (int) $mf[1]);
            } else {
                if (preg_match('/^(\d+)-(\d+)$/', $month, $mm)) {
                    $monthDesc = self::trans('%1$s through %2$s', array(self::monthName($mm[1]), self::monthName($mm[2])));
                } elseif (strpos($month, ',') !== false) {
                    $monthDesc = self::joinNames($month, 'monthName');
                } else {
                    $monthDesc = self::monthName($month);
                }
                $conditions[] = self::trans('in %s', $monthDesc);
            }
        }

        if ($weekday !== '*') {
            if ($weekday === '1-5') {
                $weekdayDesc = self::trans('weekdays');
            } elseif ($weekday === '0,6' || $weekday === '6,0') {
                $weekdayDesc = self::trans('weekends');
            } elseif (preg_match('/^(\d+)-(\d+)$/', $weekday, $wm)) {
                $weekdayDesc = self::trans('%1$s through %2$s', array(self::weekdayName($wm[1]), self::weekdayName($wm[2])));
            } elseif (strpos($weekday, ',') !== false) {
                $weekdayDesc = self::joinNames($weekday, 'weekdayName');
            } else {
                $weekdayDesc = self::weekdayName($weekday);
            }
            $conditions[] = self::trans('on %s', $weekdayDesc);
        }

        if (empty($conditions)) {
            return $when;
        }

        return $when . ' ' . implode(', ', $conditions);
    }

    public static function trans($string, $sprintf = null)
    {
        return Translate::getModuleTranslation(self::MODULE_NAME, $string, 'QctmCronExpressionTranslator', $sprintf);
    }

    public static function isNumeric($value)
    {
        return preg_match('/^\d+$/', $value) === 1;
    }

    public static function weekdayName($weekday)
    {
        return (self::isNumeric($weekday) && isset(self::WEEKDAYS[(int) $weekday]))
            ? self::trans(self::WEEKDAYS[(int) $weekday])
            : $weekday;
    }

    public static function monthName($month)
    {
        return (self::isNumeric($month) && isset(self::MONTHS[(int) $month]))
            ? self::trans(self::MONTHS[(int) $month])
            : $month;
    }

    public static function hourLabel($hour, $minute = null)
    {
        $h = (int) $hour;
        $suffix = $h >= 12 ? self::trans('PM') : self::trans('AM');
        $h12 = $h % 12;

        if ($h12 === 0) {
            $h12 = 12;
        }

        return $minute === null
            ? sprintf('%d %s', $h12, $suffix)
            : sprintf('%d:%02d %s', $h12, (int) $minute, $suffix);
    }

    public static function joinNames($csv, $nameMethod)
    {
        $names = array();

        foreach (explode(',', $csv) as $token) {
            $names[] = self::$nameMethod($token);
        }

        if (count($names) === 1) {
            return $names[0];
        }

        $last = array_pop($names);

        return implode(', ', $names) . ' ' . self::trans('and') . ' ' . $last;
    }

    public static function ordinal($number)
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

    public static function joinOrdinals($csv)
    {
        $ordinals = array();

        foreach (explode(',', $csv) as $token) {
            $ordinals[] = self::isNumeric($token) ? self::ordinal($token) : $token;
        }

        if (count($ordinals) === 1) {
            return $ordinals[0];
        }

        $last = array_pop($ordinals);

        return implode(', ', $ordinals) . ' ' . self::trans('and') . ' ' . $last;
    }
}
