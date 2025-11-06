<?php

declare(strict_types=1);

namespace GeoIp2\Model;

use GeoIp2\Util;

/**
 * This class provides the GeoIP2 Domain model.
 */
class Domain implements \JsonSerializable
{
    /**
     * @var string|null The second level domain associated with the
     *                  IP address. This will be something like "example.com" or
     *                  "example.co.uk", not "foo.example.com".
     */
    public readonly ?string $domain;

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
        $this->domain = $raw['domain'] ?? null;
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
        if ($this->domain !== null) {
            $js['domain'] = $this->domain;
        }
        $js['ip_address'] = $this->ipAddress;
        $js['network'] = $this->network;

        return $js;
    }
}
