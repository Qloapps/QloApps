<?php

namespace Lorisleiva\CronTranslator;

class MinutesField extends Field
{
    public int $position = 0;

    public function translateEvery()
    {
        return $this->lang('minutes.every');
    }

    public function translateIncrement()
    {
        if ($this->getCount() > 1) {
            return $this->lang('minutes.times_per_increment', [
                'times' => $this->getTimes(),
                'increment' => $this->getIncrement(),
            ]);
        }

        return $this->lang('minutes.increment', [
            'increment' => $this->getIncrement(),
        ]);
    }

    public function translateMultiple()
    {
        return $this->lang('minutes.multiple', [
            'times' => $this->getTimes(),
        ]);
    }

    public function format(): string
    {
        return ($this->getValue() < 10 ? '0' : '') . $this->getValue();
    }
}
