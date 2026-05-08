<?php

return [
    'minutes' => [
        'every' => 'cada minuto',
        'increment' => 'cada :increment minutos',
        'times_per_increment' => ':times cada :increment minutos',
        'multiple' => ':times cada hora',
    ],
    'hours' => [
        'every' => 'cada hora',
        'once_an_hour' => 'una vez cada hora',
        'increment' => 'cada :increment horas',
        'multiple_per_increment' => ':count horas de cada :increment',
        'times_per_increment' => ':times cada :increment horas',
        'increment_chained' => 'de cada :increment horas',
        'multiple_per_day' => ':count horas al día',
        'times_per_day' => ':times al día',
        'once_at_time' => 'a las :time',
    ],
    'days_of_month' => [
        'every' => 'diario',
        'increment' => 'cada :increment días',
        'multiple_per_increment' => ':count días de :increment',
        'multiple_per_month' => ':count días al mes',
        'once_on_day' => 'el :day',
        'every_on_day' => 'el :day de cada mes',
    ],
    'months' => [
        'every' => 'cada mes',
        'every_on_day' => 'el :day de cada mes',
        'increment' => 'cada :increment meses',
        'multiple_per_increment' => ':count meses de :increment',
        'multiple_per_year' => ':count meses al año',
        'once_on_month' => 'de :month',
        'once_on_day' => 'en :month el :day',
    ],
    'days_of_week' => [
        'every' => 'cada :weekday',
        'increment' => 'cada :increment días de la semana',
        'multiple_per_increment' => ':count días a la semana de :increment',
        'multiple_days_a_week' => ':count días a la semana',
        'once_on_day' => 'los :days',
    ],
    'years' => [
        'every' => 'cada año',
    ],
];
