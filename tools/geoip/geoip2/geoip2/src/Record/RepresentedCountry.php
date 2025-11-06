<?php

declare(strict_types=1);

namespace GeoIp2\Record;

/**
 * Contains data for the represented country associated with an IP address.
 *
 * This class contains the country-level data associated with an IP address
 * for the IP's represented country. The represented country is the country
 * represented by something like a military base.
 */
class RepresentedCountry extends Country
{
    /**
     * @var string|null A string indicating the type of entity that is
     *                  representing the country. Currently we only return <code>military</code>
     *                  but this could expand to include other types in the future.
     */
    public readonly ?string $type;

    /**
     * @ignore
     *
     * @param array<string, mixed> $record
     * @param list<string>         $locales
     */
    public function __construct(array $record, array $locales = ['en'])
    {
        parent::__construct($record, $locales);

        $this->type = $record['type'] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $js = parent::jsonSerialize();
        if ($this->type !== null) {
            $js['type'] = $this->type;
        }

        return $js;
    }
}
