<?php

declare(strict_types=1);

namespace GeoIp2\Model;

use GeoIp2\Util;

/**
 * This class provides the GeoIP2 Connection-Type model.
 */
class ConnectionType implements \JsonSerializable
{
    /**
     * @var string|null The connection type may take the
     *                  following values: "Dialup", "Cable/DSL", "Corporate", "Cellular", and
     *                  "Satellite". Additional values may be added in the future.
     */
    public readonly ?string $connectionType;

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
        $this->connectionType = $raw['connection_type'] ?? null;
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
        if ($this->connectionType !== null) {
            $js['connection_type'] = $this->connectionType;
        }
        $js['ip_address'] = $this->ipAddress;
        $js['network'] = $this->network;

        return $js;
    }
}
