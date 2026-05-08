<?php

namespace Lorisleiva\CronTranslator;

use Throwable;

class CronTranslator
{
    private static array $extendedMap = [
        '@yearly' => '0 0 1 1 *',
        '@annually' => '0 0 1 1 *',
        '@monthly' => '0 0 1 * *',
        '@weekly' => '0 0 * * 0',
        '@daily' => '0 0 * * *',
        '@hourly' => '0 * * * *'
    ];

    public static function translate(string $cron, string $locale = 'en', bool $timeFormat24hours = false): string
    {
        if (isset(self::$extendedMap[$cron])) {
            $cron = self::$extendedMap[$cron];
        }

        try {
            $expression = new CronExpression($cron, $locale, $timeFormat24hours);
            $orderedFields = static::orderFields($expression->getFields());

            $translations = array_map(function (Field $field) {
                return $field->translate();
            }, $orderedFields);

            return ucfirst(implode(' ', array_filter($translations)));
        } catch (Throwable $th) {
            throw new CronParsingException($cron);
        }
    }

    protected static function orderFields(array $fields)
    {
        // Group fields by CRON types.
        $onces = static::filterType($fields, 'Once');
        $everys = static::filterType($fields, 'Every');
        $incrementsAndMultiples = static::filterType($fields, 'Increment', 'Multiple');

        // Decide whether to keep one or zero CRON type "Every".
        $firstEvery = reset($everys)->position ?? PHP_INT_MIN;
        $firstIncrementOrMultiple = reset($incrementsAndMultiples)->position ?? PHP_INT_MAX;
        $numberOfEverysKept = $firstIncrementOrMultiple < $firstEvery ? 0 : 1;

        // Mark fields that will not be displayed as dropped.
        // This allows other fields to check whether some
        // information is missing and adapt their translation.
        /** @var Field $field */
        foreach (array_slice($everys, $numberOfEverysKept) as $field) {
            $field->dropped = true;
        }

        return array_merge(
            // Place one or zero "Every" field at the beginning.
            array_slice($everys, 0, $numberOfEverysKept),

            // Place all "Increment" and "Multiple" fields in the middle.
            $incrementsAndMultiples,

            // Finish with the "Once" fields reversed (i.e. from months to minutes).
            array_reverse($onces)
        );
    }

    protected static function filterType(array $fields, ...$types): array
    {
        return array_filter($fields, function (Field $field) use ($types) {
            return $field->hasType(...$types);
        });
    }
}
