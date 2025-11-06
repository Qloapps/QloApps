<?php

declare(strict_types=1);

namespace GeoIp2\Model;

use GeoIp2\Record\City as CityRecord;
use GeoIp2\Record\Location;
use GeoIp2\Record\Postal;
use GeoIp2\Record\Subdivision;

/**
 * Model class for the data returned by City Plus web service and City
 * database.
 *
 * See https://dev.maxmind.com/geoip/docs/web-services?lang=en for more
 * details.
 */
class City extends Country
{
    /**
     * @var CityRecord city data for the requested IP address
     */
    public readonly CityRecord $city;

    /**
     * @var Location location data for the requested IP address
     */
    public readonly Location $location;

    /**
     * @var Subdivision An object representing the most specific subdivision
     *                  returned. If the response did not contain any
     *                  subdivisions, this method returns an empty
     *                  \GeoIp2\Record\Subdivision object.
     */
    public readonly Subdivision $mostSpecificSubdivision;

    /**
     * @var Postal postal data for the
     *             requested IP address
     */
    public readonly Postal $postal;

    /**
     * @var array<\GeoIp2\Record\Subdivision> An array of \GeoIp2\Record\Subdivision
     *                                        objects representing the country
     *                                        subdivisions for the requested IP
     *                                        address. The number and type of
     *                                        subdivisions varies by country,
     *                                        but a subdivision is typically a
     *                                        state, province, county, etc.
     *                                        Subdivisions are ordered from most
     *                                        general (largest) to most specific
     *                                        (smallest). If the response did
     *                                        not contain any subdivisions, this
     *                                        method returns an empty array.
     */
    public readonly array $subdivisions;

    /**
     * @ignore
     *
     * @param array<string, mixed> $raw
     * @param list<string>         $locales
     */
    public function __construct(array $raw, array $locales = ['en'])
    {
        parent::__construct($raw, $locales);

        $this->city = new CityRecord($raw['city'] ?? [], $locales);
        $this->location = new Location($raw['location'] ?? []);
        $this->postal = new Postal($raw['postal'] ?? []);

        if (!isset($raw['subdivisions'])) {
            $this->subdivisions = [];
            $this->mostSpecificSubdivision =
                    new Subdivision([], $locales);

            return;
        }

        $subdivisions = [];
        foreach ($raw['subdivisions'] as $sub) {
            $subdivisions[] =
                new Subdivision($sub, $locales)
            ;
        }

        // Not using end as we don't want to modify internal pointer.
        $this->mostSpecificSubdivision =
            $subdivisions[\count($subdivisions) - 1];
        $this->subdivisions = $subdivisions;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function jsonSerialize(): ?array
    {
        $js = parent::jsonSerialize();

        $city = $this->city->jsonSerialize();
        if (!empty($city)) {
            $js['city'] = $city;
        }

        $location = $this->location->jsonSerialize();
        if (!empty($location)) {
            $js['location'] = $location;
        }

        $postal =
         $this->postal->jsonSerialize();
        if (!empty($postal)) {
            $js['postal'] = $postal;
        }

        $subdivisions = [];
        foreach ($this->subdivisions as $sub) {
            $subdivisions[] = $sub->jsonSerialize();
        }
        if (!empty($subdivisions)) {
            $js['subdivisions'] = $subdivisions;
        }

        return $js;
    }
}
