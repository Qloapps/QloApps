<?php

return [
    'minutes' => [
        'every' => 'každú minútu',
        'increment' => 'každých :increment minút',
        'times_per_increment' => ':times každých :increment minút',
        'multiple' => ':times za hodinu',
    ],
    'hours' => [
        'every' => 'každú hodinu',
        'once_an_hour' => 'raz za hodinu',
        'increment' => 'každých :increment hodín',
        'multiple_per_increment' => ':count hodín z :increment',
        'times_per_increment' => ':times každých :increment hodín',
        'increment_chained' => 'každých :increment hodín',
        'multiple_per_day' => ':count hodín za deň',
        'times_per_day' => ':times za deň',
        'once_at_time' => 'o :time',
    ],
    'days_of_month' => [
        'every' => 'každý deň',
        'increment' => 'každých :increment dní',
        'multiple_per_increment' => ':count dní z :increment',
        'multiple_per_month' => ':count dní v mesiaci',
        'once_on_day' => ':day dňa v mesiaci',
        'every_on_day' => 'na každý :day deň v mesiaci',
    ],
    'months' => [
        'every' => 'každý mesiac',
        'every_on_day' => 'každý :day deň v mesiaci',
        'increment' => 'každých :increment mesiacov',
        'multiple_per_increment' => ':count mesiacov z :increment',
        'multiple_per_year' => ':count mesiacov ročne',
        'once_on_month' => 'v mesiaci :month',
        'once_on_day' => 'na :day deň v mesiaci :month',
    ],
    'days_of_week' => [
        'every' => 'každá/ý :weekday',
        'increment' => 'každých :increment dní v týždni',
        'multiple_per_increment' => ':count dní v týždni z :increment',
        'multiple_days_a_week' => ':count dní v týždni',
        'once_on_day' => 'v :day',
    ],
    'years' => [
        'every' => 'každý rok',
    ],
];
