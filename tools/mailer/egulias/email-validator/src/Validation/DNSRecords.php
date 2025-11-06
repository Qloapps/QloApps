<?php

namespace Egulias\EmailValidator\Validation;

class DNSRecords
{
    /**
     * @param array $records
     * @param bool $error
     */
    public function __construct(private readonly array $records, private readonly bool $error = false)
    {
    }

    /**
     * @return array
     */
    public function getRecords(): array
    {
        return $this->records;
    }

    public function withError(): bool
    {
        return $this->error;
    }
}
