<?php

declare(strict_types=1);

namespace GeoIp2\Model;

use GeoIp2\Util;

/**
 * This class provides the GeoLite2 ASN model.
 */
class Asn implements \JsonSerializable
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
        $js['ip_address'] = $this->ipAddress;
        $js['network'] = $this->network;

        return $js;
    }
}
