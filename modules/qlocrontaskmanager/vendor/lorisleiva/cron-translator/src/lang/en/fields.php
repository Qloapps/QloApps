<?php

return [
    'minutes' => [
        'every' => 'every minute',
        'increment' => 'every :increment minutes',
        'times_per_increment' => ':times every :increment minutes',
        'multiple' => ':times an hour',
    ],
    'hours' => [
        'every' => 'every hour',
        'once_an_hour' => 'once an hour',
        'increment' => 'every :increment hours',
        'multiple_per_increment' => ':count hours out of :increment',
        'times_per_increment' => ':times every :increment hours',
        'increment_chained' => 'of every :increment hours',
        'multiple_per_day' => ':count hours a day',
        'times_per_day' => ':times a day',
        'once_at_time' => 'at :time',
    ],
    'days_of_month' => [
        'every' => 'every day',
        'increment' => 'every :increment days',
        'multiple_per_increment' => ':count days out of :increment',
        'multiple_per_month' => ':count days a month',
        'once_on_day' => 'on the :day',
        'every_on_day' => 'on the :day of every month',
    ],
    'months' => [
        'every' => 'every month',
        'every_on_day' => 'the :day of every month',
        'increment' => 'every :increment months',
        'multiple_per_increment' => ':count months out of :increment',
        'multiple_per_year' => ':count months a year',
        'once_on_month' => 'on :month',
        'once_on_day' => 'on :month the :day',
    ],
    'days_of_week' => [
        'every' => 'every :weekday',
        'increment' => 'every :increment days of the week',
        'multiple_per_increment' => ':count days of the week out of :increment',
        'multiple_days_a_week' => ':count days a week',
        'once_on_day' => 'on :days',
    ],
    'years' => [
        'every' => 'every year',
    ],
];
