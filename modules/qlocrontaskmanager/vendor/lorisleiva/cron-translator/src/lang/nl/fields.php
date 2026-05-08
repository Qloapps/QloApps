<?php

return [
    'minutes' => [
        'every' => 'elke minuut',
        'increment' => 'elke :increment minuten',
        'times_per_increment' => ':times elke :increment minuten',
        'multiple' => ':times per uur',
    ],
    'hours' => [
        'every' => 'elk uur',
        'once_an_hour' => 'eens per uur',
        'increment' => 'elke :increment uur',
        'multiple_per_increment' => ':count uur van de :increment',
        'times_per_increment' => ':times elke :increment uur',
        'increment_chained' => 'van elke :increment uur',
        'multiple_per_day' => ':count uur per dag',
        'times_per_day' => ':times per dag',
        'once_at_time' => 'om :time',
    ],
    'days_of_month' => [
        'every' => 'elke dag',
        'increment' => 'elke :increment dagen',
        'multiple_per_increment' => ':count dagen van de :increment',
        'multiple_per_month' => ':count dagen per maand',
        'once_on_day' => 'op de :day',
        'every_on_day' => 'op de :day van elke maand',
    ],
    'months' => [
        'every' => 'elke maand',
        'every_on_day' => 'de :day van elke maand',
        'increment' => 'elke :increment maanden',
        'multiple_per_increment' => ':count maanden van de :increment',
        'multiple_per_year' => ':count maanden per jaar',
        'once_on_month' => 'in :month',
        'once_on_day' => 'in :month de :day',
    ],
    'days_of_week' => [
        'every' => 'elke :weekday',
        'increment' => 'elke :increment dag van de week',
        'multiple_per_increment' => ':count dagen van de week op :increment',
        'multiple_days_a_week' => ':count dagen per week ',
        'once_on_day' => 'op :days',
    ],
    'years' => [
        'every' => 'elk jaar',
    ],
];
