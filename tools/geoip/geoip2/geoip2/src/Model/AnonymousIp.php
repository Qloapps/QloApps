<?php

declare(strict_types=1);

namespace GeoIp2\Model;

use GeoIp2\Util;

/**
 * This class provides the GeoIP2 Anonymous IP model.
 */
class AnonymousIp implements \JsonSerializable
{
    /**
     * @var bool this is true if the IP address belongs to
     *           any sort of anonymous network
     */
    public readonly bool $isAnonymous;

    /**
     * @var bool This is true if the IP address is
     *           registered to an anonymous VPN provider. If a VPN provider does not
     *           register subnets under names associated with them, we will likely only
     *           flag their IP ranges using the isHostingProvider property.
     */
    public readonly bool $isAnonymousVpn;

    /**
     * @var bool this is true if the IP address belongs
     *           to a hosting or VPN provider (see description of isAnonymousVpn property)
     */
    public readonly bool $isHostingProvider;

    /**
     * @var bool this is true if the IP address belongs to
     *           a public proxy
     */
    public readonly bool $isPublicProxy;

    /**
     * @var bool this is true if the IP address is
     *           on a suspected anonymizing network and belongs to a residential ISP
     */
    public readonly bool $isResidentialProxy;

    /**
     * @var bool this is true if the IP address is a Tor
     *           exit node
     */
    public readonly bool $isTorExitNode;

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
        $this->isAnonymous = $raw['is_anonymous'] ?? false;
        $this->isAnonymousVpn = $raw['is_anonymous_vpn'] ?? false;
        $this->isHostingProvider = $raw['is_hosting_provider'] ?? false;
        $this->isPublicProxy = $raw['is_public_proxy'] ?? false;
        $this->isResidentialProxy = $raw['is_residential_proxy'] ?? false;
        $this->isTorExitNode = $raw['is_tor_exit_node'] ?? false;
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
        $js['is_anonymous'] = $this->isAnonymous;
        $js['is_anonymous_vpn'] = $this->isAnonymousVpn;
        $js['is_hosting_provider'] = $this->isHostingProvider;
        $js['is_public_proxy'] = $this->isPublicProxy;
        $js['is_residential_proxy'] = $this->isResidentialProxy;
        $js['is_tor_exit_node'] = $this->isTorExitNode;
        $js['ip_address'] = $this->ipAddress;
        $js['network'] = $this->network;

        return $js;
    }
}
