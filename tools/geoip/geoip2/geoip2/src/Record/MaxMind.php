<?php

declare(strict_types=1);

namespace GeoIp2\Record;

/**
 * Contains data about your account.
 *
 * This record is returned by all location services and databases.
 */
class MaxMind implements \JsonSerializable
{
    /**
     * @var int|null the number of remaining queries you
     *               have for the service you are calling
     */
    public readonly ?int $queriesRemaining;

    /**
     * @ignore
     *
     * @param array<string, mixed> $record
     */
    public function __construct(array $record)
    {
        $this->queriesRemaining = $record['queries_remaining'] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $js = [];
        if ($this->queriesRemaining !== null) {
            $js['queries_remaining'] = $this->queriesRemaining;
        }

        return $js;
    }
}
