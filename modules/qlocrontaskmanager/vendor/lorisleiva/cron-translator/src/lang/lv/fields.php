<?php

return [
    'minutes' => [
        'every' => 'katru minūti',
        'increment' => 'ik pēc :increment minūtēm',
        'times_per_increment' => ':times ik pēc :increment minūtēm',
        'multiple' => ':times stundā',
    ],
    'hours' => [
        'every' => 'katru stundu',
        'once_an_hour' => 'vienreiz stundā',
        'increment' => 'katras :increment stundas',
        'multiple_per_increment' => ':count no :increment stundām',
        'times_per_increment' => ':times katras :increment stundas',
        'increment_chained' => 'ik pēc :increment stundām',
        'multiple_per_day' => ':count stundas dienā',
        'times_per_day' => ':times dienā',
        'once_at_time' => 'plkst. :time',
    ],
    'days_of_month' => [
        'every' => 'katru dienu',
        'increment' => 'ik pēc :increment dienām',
        'multiple_per_increment' => ':count dienas no :increment',
        'multiple_per_month' => ':count dienas mēnesī',
        'once_on_day' => ':day. datumā',
        'every_on_day' => 'katra mēneša :day. datumā',
    ],
    'months' => [
        'every' => 'katru mēnesi',
        'every_on_day' => 'katra mēneša :day. datumā',
        'increment' => 'katrus :increment mēnešus',
        'multiple_per_increment' => ':count mēnešus no :increment',
        'multiple_per_year' => ':count mēnešus gadā',
        'once_on_month' => ':month',
        'once_on_day' => ':day. :month',
    ],
    'days_of_week' => [
        'every' => 'katru :weekdayu',
        'increment' => 'ik pēc :increment nedēļas dienām',
        'multiple_per_increment' => ':count nedēļas dienas no :increment',
        'multiple_days_a_week' => ':count dienas nedēļā',
        'once_on_day' => ':dayās',
    ],
    'years' => [
        'every' => 'katru gadu',
    ],
];
