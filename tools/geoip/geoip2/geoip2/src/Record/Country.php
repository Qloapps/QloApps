<?php

declare(strict_types=1);

namespace GeoIp2\Record;

/**
 * Contains data for the country record associated with an IP address.
 *
 * This record is returned by all location services and databases.
 */
class Country extends AbstractPlaceRecord
{
    /**
     * @var bool This is true if the country is a
     *           member state of the European Union. This attribute is returned by all
     *           location services and databases.
     */
    public readonly bool $isInEuropeanUnion;

    /**
     * @var string|null The two-character ISO 3166-1 alpha code
     *                  for the country. See https://en.wikipedia.org/wiki/ISO_3166-1. This
     *                  attribute is returned by all location services and databases.
     */
    public readonly ?string $isoCode;

    /**
     * @ignore
     *
     * @param array<string, mixed> $record
     * @param list<string>         $locales
     */
    public function __construct(array $record, array $locales = ['en'])
    {
        parent::__construct($record, $locales);

        $this->isInEuropeanUnion = $record['is_in_european_union'] ?? false;
        $this->isoCode = $record['iso_code'] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $js = parent::jsonSerialize();
        if ($this->isInEuropeanUnion !== false) {
            $js['is_in_european_union'] = $this->isInEuropeanUnion;
        }
        if ($this->isoCode !== null) {
            $js['iso_code'] = $this->isoCode;
        }

        return $js;
    }
}
