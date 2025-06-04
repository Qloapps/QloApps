<?php

declare(strict_types=1);

namespace GeoIp2\Model;

use GeoIp2\Util;

/**
 * This class provides the GeoIP2 ISP model.
 */
class Isp implements \JsonSerializable
{
    /**
     * @var int|null the autonomous system number
     *               associated with the IP address
     */
    public readonly ?int $autonomousSystemNumber;

    /**
     * @var string|null the organization
     *                  associated with the registered autonomous system number for the IP
     *                  address
     */
    public readonly ?string $autonomousSystemOrganization;

    /**
     * @var string|null the name of the ISP associated with the IP
     *                  address
     */
    public readonly ?string $isp;

    /**
     * @var string|null The [mobile country code
     *                  (MCC)](https://en.wikipedia.org/wiki/Mobile_country_code) associated with
     *                  the IP address and ISP.
     */
    public readonly ?string $mobileCountryCode;

    /**
     * @var string|null The [mobile network code
     *                  (MNC)](https://en.wikipedia.org/wiki/Mobile_country_code) associated with
     *                  the IP address and ISP.
     */
    public readonly ?string $mobileNetworkCode;

    /**
     * @var string|null the name of the organization associated
     *                  with the IP address
     */
    public readonly ?string $organization;

    /**
     * @var string the IP address that the data in the model is
     *             for
     */
    public readonly string $ipAddress;

    /**
     * @var string The network in CIDR notation associated with
     *             the record. In particular, this is the largest network where all of the
     *             fields besides $ipAddress have the same value.
     */
    public readonly string $network;

    /**
     * @ignore
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(array $raw)
    {
        $this->autonomousSystemNumber = $raw['autonomous_system_number'] ?? null;
        $this->autonomousSystemOrganization =
            $raw['autonomous_system_organization'] ?? null;
        $this->isp = $raw['isp'] ?? null;
        $this->mobileCountryCode = $raw['mobile_country_code'] ?? null;
        $this->mobileNetworkCode = $raw['mobile_network_code'] ?? null;
        $this->organization = $raw['organization'] ?? null;

        $ipAddress = $raw['ip_address'];
        $this->ipAddress = $ipAddress;
        $this->network = Util::cidr($ipAddress, $raw['prefix_len']);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function jsonSerialize(): ?array
    {
        $js = [];
        if ($this->autonomousSystemNumber !== null) {
            $js['autonomous_system_number'] = $this->autonomousSystemNumber;
        }
        if ($this->autonomousSystemOrganization !== null) {
            $js['autonomous_system_organization'] = $this->autonomousSystemOrganization;
        }
        if ($this->isp !== null) {
            $js['isp'] = $this->isp;
        }
        if ($this->mobileCountryCode !== null) {
            $js['mobile_country_code'] = $this->mobileCountryCode;
        }
        if ($this->mobileNetworkCode !== null) {
            $js['mobile_network_code'] = $this->mobileNetworkCode;
        }
        if ($this->organization !== null) {
            $js['organization'] = $this->organization;
        }
        $js['ip_address'] = $this->ipAddress;
        $js['network'] = $this->network;

        return $js;
    }
}
