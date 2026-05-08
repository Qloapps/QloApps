<?php

namespace Lorisleiva\CronTranslator;

class CronExpression
{
    public string $raw;
    public MinutesField $minute;
    public HoursField $hour;
    public DaysOfMonthField $day;
    public MonthsField $month;
    public DaysOfWeekField $weekday;
    public string $locale;
    public bool $timeFormat24hours;
    public array $translations;

    public function __construct(string $cron, string $locale = 'en', bool $timeFormat24hours = false)
    {
        $this->raw = $cron;
        $fields = explode(' ', $cron);
        $this->minute = new MinutesField($this, $fields[0]);
        $this->hour = new HoursField($this, $fields[1]);
        $this->day = new DaysOfMonthField($this, $fields[2]);
        $this->month = new MonthsField($this, $fields[3]);
        $this->weekday = new DaysOfWeekField($this, $fields[4]);
        $this->locale = $locale;
        $this->timeFormat24hours = $timeFormat24hours;
        $this->ensureLocaleExists();
        $this->loadTranslations();
    }

    public function getFields(): array
    {
        return [
            $this->minute,
            $this->hour,
            $this->day,
            $this->month,
            $this->weekday,
        ];
    }

    public function langCountable(string $type, int $number)
    {
        $array = $this->translations[$type];

        $value = $array[$number] ?? ($array['default'] ?: '');

        return str_replace(':number', $number, $value);
    }

    public function lang(string $key, array $replacements = [])
    {
        $translation = $this->getArrayDot($this->translations['fields'], $key);

        foreach ($replacements as $key => $value) {
            $translation = str_replace(':' . $key, $value, $translation);
        }

        return $translation;
    }

    protected function ensureLocaleExists(string $fallbackLocale = 'en')
    {
        if (! is_dir($this->getTranslationDirectory())) {
            $this->locale = $fallbackLocale;
        }
    }

    /**
     * @throws TranslationFileMissingException
     */
    protected function loadTranslations()
    {
        $this->translations = [
            'days' => $this->loadTranslationFile('days'),
            'fields' => $this->loadTranslationFile('fields'),
            'months' => $this->loadTranslationFile('months'),
            'ordinals' => $this->loadTranslationFile('ordinals'),
            'times' => $this->loadTranslationFile('times'),
        ];
    }

    protected function loadTranslationFile(string $file)
    {
        $filename = sprintf('%s/%s.php', $this->getTranslationDirectory(), $file);

        if (! is_file($filename)) {
            throw new TranslationFileMissingException($this->locale, $file);
        }

        return include $filename;
    }

    protected function getTranslationDirectory(): string
    {
        return __DIR__ . '/lang/' . $this->locale;
    }

    protected function getArrayDot(array $array, string $key)
    {
        $keys = explode('.', $key);

        foreach ($keys as $key) {
            $array = $array[$key];
        }

        return $array;
    }
}
