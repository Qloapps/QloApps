<?php

namespace Lorisleiva\CronTranslator;

class MonthsField extends Field
{
    public int $position = 3;

    public function translateEvery()
    {
        if ($this->expression->day->hasType('Once')) {
            return $this->lang('months.every_on_day', [
                'day' => $this->expression->day->format(),
            ]);
        }

        return $this->lang('months.every');
    }

    public function translateIncrement()
    {
        if ($this->getCount() > 1) {
            return $this->lang('months.multiple_per_increment', [
                'count' => $this->getCount(),
                'increment' => $this->getCount(),
            ]);
        }

        return $this->lang('months.increment', [
            'increment' => $this->getIncrement(),
        ]);
    }

    public function translateMultiple()
    {
        return $this->lang('months.multiple_per_year', [
            'count' => $this->getCount(),
        ]);
    }

    public function translateOnce()
    {
        if ($this->expression->day->hasType('Once')) {
            return $this->lang('months.once_on_day', [
                'month' => $this->format(),
                'day' => $this->expression->day->format(),
            ]);
        }

        return $this->lang('months.once_on_month', [
            'month' => $this->format()
        ]);
    }

    /**
     * @throws CronParsingException
     */
    public function format()
    {
        if ($this->getValue() < 1 || $this->getValue() > 12) {
            throw new CronParsingException($this->expression->raw);
        }

        return $this->langCountable('months', $this->getValue());
    }
}
