<?php

return [
    'minutes' => [
        'every' => 'chaque minute',
        'increment' => 'toutes les :increment minutes',
        'times_per_increment' => ':times toutes les :increment minutes',
        'multiple' => ':times par heure',
    ],
    'hours' => [
        'every' => 'chaque heure',
        'once_an_hour' => 'une fois par heure',
        'increment' => 'toutes les :increment heures',
        'multiple_per_increment' => ':count heures au-delà de :increment',
        'times_per_increment' => ':times toutes les :increment heures',
        'increment_chained' => 'toutes les :increment heures',
        'multiple_per_day' => ':count heures par jour',
        'times_per_day' => ':times par jour',
        'once_at_time' => 'à :time',
    ],
    'days_of_month' => [
        'every' => 'chaque jour',
        'increment' => 'tous les :increment jours',
        'multiple_per_increment' => ':count jours sur :increment',
        'multiple_per_month' => ':count jours par mois',
        'once_on_day' => 'le :day',
        'every_on_day' => 'le :day de chaque mois',
    ],
    'months' => [
        'every' => 'chaque mois',
        'every_on_day' => 'le :day de chaque mois',
        'increment' => 'tous les :increment mois',
        'multiple_per_increment' => ':count mois sur :increment',
        'multiple_per_year' => ':count mois par an',
        'once_on_month' => 'en :month',
        'once_on_day' => 'le :day :month',
    ],
    'days_of_week' => [
        'every' => 'chaque :weekday',
        'increment' => 'tous les :increment jours de la semaine',
        'multiple_per_increment' => ':count jours de la semaine sur :increment',
        'multiple_days_a_week' => ':count jours par semaine',
        'once_on_day' => 'les :days',
    ],
    'years' => [
        'every' => 'chaque année',
    ],
];
