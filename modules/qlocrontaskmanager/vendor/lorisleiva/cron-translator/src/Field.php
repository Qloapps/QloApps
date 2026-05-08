<?php

namespace Lorisleiva\CronTranslator;

abstract class Field
{
    public CronExpression $expression;
    public string $rawField;
    public CronType $type;
    public bool $dropped = false;
    public int $position;

    public function __construct(CronExpression $expression, string $rawField)
    {
        $this->expression = $expression;
        $this->rawField = $rawField;
        $this->type = CronType::parse($rawField);
    }

    public function translate()
    {
        $method = 'translate' . $this->type->type;

        if (method_exists($this, $method)) {
            return $this->{$method}();
        }
    }

    public function hasType(): bool
    {
        return $this->type->hasType(...func_get_args());
    }

    public function getValue(): ?int
    {
        return $this->type->value;
    }

    public function getCount(): ?int
    {
        return $this->type->count;
    }

    public function getIncrement(): ?int
    {
        return $this->type->increment;
    }

    public function getTimes()
    {
        return $this->langCountable('times', $this->getCount());
    }

    protected function langCountable(string $key, int $value)
    {
        return $this->expression->langCountable($key, $value);
    }

    protected function lang(string $key, array $replacements = [])
    {
        return $this->expression->lang($key, $replacements);
    }
}
