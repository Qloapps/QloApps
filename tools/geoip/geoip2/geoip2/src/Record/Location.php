<?php

declare(strict_types=1);

namespace GeoIp2\Record;

/**
 * Contains data for the location record associated with an IP address.
 *
 * This record is returned by all location services and databases besides
 * Country.
 */
class Location implements \JsonSerializable
{
    /**
     * @var int|null The average income in US dollars
     *               associated with the requested IP address. This attribute is only available
     *               from the Insights service.
     */
    public readonly ?int $averageIncome;

    /**
     * @var int|null The approximate accuracy radius in
     *               kilometers around the latitude and longitude for the IP address. This is
     *               the radius where we have a 67% confidence that the device using the IP
     *               address resides within the circle centered at the latitude and longitude
     *               with the provided radius.
     */
    public readonly ?int $accuracyRadius;

    /**
     * @var float|null The approximate latitude of the location
     *                 associated with the IP address. This value is not precise and should not be
     *                 used to identify a particular address or household.
     */
    public readonly ?float $latitude;

    /**
     * @var float|null The approximate longitude of the location
     *                 associated with the IP address. This value is not precise and should not be
     *                 used to identify a particular address or household.
     */
    public readonly ?float $longitude;

    /**
     * @var int|null The metro code of the location if the location
     *               is in the US. MaxMind returns the same metro codes as the
     *               Google AdWords API. See
     *               https://developers.google.com/adwords/api/docs/appendix/cities-DMAregions.
     */
    public readonly ?int $metroCode;

    /**
     * @var int|null The estimated population per square
     *               kilometer associated with the IP address. This attribute is only available
     *               from the Insights service.
     */
    public readonly ?int $populationDensity;

    /**
     * @var string|null The time zone associated with location, as
     *                  specified by the IANA Time Zone Database, e.g., "America/New_York". See
     *                  https://www.iana.org/time-zones.
     */
    public readonly ?string $timeZone;

    /**
     * @ignore
     *
     * @param array<string, mixed> $record
     */
    public function __construct(array $record)
    {
        $this->averageIncome = $record['average_income'] ?? null;
        $this->accuracyRadius = $record['accuracy_radius'] ?? null;
        $this->latitude = $record['latitude'] ?? null;
        $this->longitude = $record['longitude'] ?? null;
        $this->metroCode = $record['metro_code'] ?? null;
        $this->populationDensity = $record['population_density'] ?? null;
        $this->timeZone = $record['time_zone'] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $js = [];
        if ($this->averageIncome !== null) {
            $js['average_income'] = $this->averageIncome;
        }
        if ($this->accuracyRadius !== null) {
            $js['accuracy_radius'] = $this->accuracyRadius;
        }
        if ($this->latitude !== null) {
            $js['latitude'] = $this->latitude;
        }
        if ($this->longitude !== null) {
            $js['longitude'] = $this->longitude;
        }
        if ($this->metroCode !== null) {
            $js['metro_code'] = $this->metroCode;
        }
        if ($this->populationDensity !== null) {
            $js['population_density'] = $this->populationDensity;
        }
        if ($this->timeZone !== null) {
            $js['time_zone'] = $this->timeZone;
        }

        return $js;
    }
}
