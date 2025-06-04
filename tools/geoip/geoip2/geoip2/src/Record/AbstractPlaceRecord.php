<?php

declare(strict_types=1);

namespace GeoIp2\Record;

abstract class AbstractPlaceRecord extends AbstractNamedRecord
{
    /**
     * @var int|null A value from 0-100 indicating MaxMind's
     *               confidence that the location level is correct. This attribute is only available
     *               from the Insights service and the GeoIP2 Enterprise database.
     */
    public readonly ?int $confidence;

    /**
     * @var int|null The GeoName ID for the location level. This attribute
     *               is returned by all location services and databases.
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

        $this->confidence = $record['confidence'] ?? null;
        $this->geonameId = $record['geoname_id'] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $js = parent::jsonSerialize();
        if ($this->confidence !== null) {
            $js['confidence'] = $this->confidence;
        }

        if ($this->geonameId !== null) {
            $js['geoname_id'] = $this->geonameId;
        }

        return $js;
    }
}
