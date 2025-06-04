<?php

declare(strict_types=1);

namespace GeoIp2\Model;

use GeoIp2\Record\Continent;
use GeoIp2\Record\Country as CountryRecord;
use GeoIp2\Record\MaxMind;
use GeoIp2\Record\RepresentedCountry;
use GeoIp2\Record\Traits;

/**
 * Model class for the data returned by GeoIP2 Country web service and database.
 *
 * See https://dev.maxmind.com/geoip/docs/web-services?lang=en for more details.
 */
class Country implements \JsonSerializable
{
    /**
     * @var Continent continent data for the requested IP address
     */
    public readonly Continent $continent;

    /**
     * @var CountryRecord Country data for the requested IP address. This
     *                    object represents the country where MaxMind believes
     *                    the end user is located.
     */
    public readonly CountryRecord $country;

    /**
     * @var MaxMind data related to your MaxMind account
     */
    public readonly MaxMind $maxmind;

    /**
     * @var CountryRecord Registered country data for the requested IP address.
     *                    This record represents the country where the ISP has
     *                    registered a given IP block and may differ from the
     *                    user's country.
     */
    public readonly CountryRecord $registeredCountry;

    /**
     * @var RepresentedCountry Represented country data for the requested IP
     *                         address. The represented country is used for
     *                         things like military bases. It is only present
     *                         when the represented country differs from the
     *                         country.
     */
    public readonly RepresentedCountry $representedCountry;

    /**
     * @var Traits data for the traits of the requested IP address
     */
    public readonly Traits $traits;

    /**
     * @ignore
     *
     * @param array<string, mixed> $raw
     * @param list<string>         $locales
     */
    public function __construct(array $raw, array $locales = ['en'])
    {
        $this->continent = new Continent(
            $raw['continent'] ?? [],
            $locales
        );
        $this->country = new CountryRecord(
            $raw['country'] ?? [],
            $locales
        );
        $this->maxmind = new MaxMind($raw['maxmind'] ?? []);
        $this->registeredCountry = new CountryRecord(
            $raw['registered_country'] ?? [],
            $locales
        );
        $this->representedCountry = new RepresentedCountry(
            $raw['represented_country'] ?? [],
            $locales
        );
        $this->traits = new Traits($raw['traits'] ?? []);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function jsonSerialize(): ?array
    {
        $js = [];
        $continent = $this->continent->jsonSerialize();
        if (!empty($continent)) {
            $js['continent'] = $continent;
        }
        $country = $this->country->jsonSerialize();
        if (!empty($country)) {
            $js['country'] = $country;
        }
        $maxmind = $this->maxmind->jsonSerialize();
        if (!empty($maxmind)) {
            $js['maxmind'] = $maxmind;
        }
        $registeredCountry = $this->registeredCountry->jsonSerialize();
        if (!empty($registeredCountry)) {
            $js['registered_country'] = $registeredCountry;
        }
        $representedCountry = $this->representedCountry->jsonSerialize();
        if (!empty($representedCountry)) {
            $js['represented_country'] = $representedCountry;
        }
        $traits = $this->traits->jsonSerialize();
        if (!empty($traits)) {
            $js['traits'] = $traits;
        }

        return $js;
    }
}
