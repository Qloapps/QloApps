<?php

declare(strict_types=1);

namespace GeoIp2\Record;

/**
 * Contains data for the continent record associated with an IP address.
 *
 * This record is returned by all location services and databases.
 */
class Continent extends AbstractNamedRecord
{
    /**
     * @var string|null A two character continent code like "NA" (North
     *                  America) or "OC" (Oceania). This attribute is returned by all location
     *                  services and databases.
     */
    public readonly ?string $code;

    /**
     * @var int|null The GeoName ID for the continent. This
     *               attribute is returned by all location services and databases.
     */
    public readonly ?int $geonameId;

    /**
     * @ignore
     *
     * @param array<string, mixed> $record
     * @param list<string>         $locales
     */
    public function __construct(array $record, array $locales = ['en'])
    {
        parent::__construct($record, $locales);

        $this->code = $record['code'] ?? null;
        $this->geonameId = $record['geoname_id'] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $js = parent::jsonSerialize();
        if ($this->code !== null) {
            $js['code'] = $this->code;
        }
        if ($this->geonameId !== null) {
            $js['geoname_id'] = $this->geonameId;
        }

        return $js;
    }
}
