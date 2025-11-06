<?php

declare(strict_types=1);

namespace GeoIp2\Record;

use GeoIp2\Util;

/**
 * Contains data for the traits record associated with an IP address.
 *
 * This record is returned by all location services and databases.
 */
class Traits implements \JsonSerializable
{
    /**
     * @var int|null The autonomous system number
     *               associated with the IP address. See
     *               https://en.wikipedia.org/wiki/Autonomous_system_(Internet%29. This attribute
     *               is only available from the City Plus and Insights web services and the
     *               GeoIP2 Enterprise database.
     */
    public readonly ?int $autonomousSystemNumber;

    /**
     * @var string|null The organization
     *                  associated with the registered autonomous system number for the IP address.
     *                  See https://en.wikipedia.org/wiki/Autonomous_system_(Internet%29. This
     *                  attribute is only available from the City Plus and Insights web services and
     *                  the GeoIP2 Enterprise database.
     */
    public readonly ?string $autonomousSystemOrganization;

    /**
     * @var string|null The connection type may take the
     *                  following  values: "Dialup", "Cable/DSL", "Corporate", "Cellular", and
     *                  "Satellite". Additional values may be added in the future. This attribute is
     *                  only available from the City Plus and Insights web services and the GeoIP2
     *                  Enterprise database.
     */
    public readonly ?string $connectionType;

    /**
     * @var string|null The second level domain associated with the
     *                  IP address. This will be something like "example.com" or "example.co.uk",
     *                  not "foo.example.com". This attribute is only available from the
     *                  City Plus and Insights web services and the GeoIP2 Enterprise
     *                  database.
     */
    public readonly ?string $domain;

    /**
     * @var string|null The IP address that the data in the model
     *                  is for. If you performed a "me" lookup against the web service, this
     *                  will be the externally routable IP address for the system the code is
     *                  running on. If the system is behind a NAT, this may differ from the IP
     *                  address locally assigned to it. This attribute is returned by all end
     *                  points.
     */
    public readonly ?string $ipAddress;

    /**
     * @var bool This is true if the IP address belongs to
     *           any sort of anonymous network. This property is only available from GeoIP2
     *           Insights.
     */
    public readonly bool $isAnonymous;

    /**
     * @var bool This is true if the IP address is
     *           registered to an anonymous VPN provider. If a VPN provider does not register
     *           subnets under names associated with them, we will likely only flag their IP
     *           ranges using the isHostingProvider property. This property is only available
     *           from GeoIP2 Insights.
     */
    public readonly bool $isAnonymousVpn;

    /**
     * @var bool This is true if the IP address belongs to an [anycast
     *           network](https://en.wikipedia.org/wiki/Anycast). This property is not
     *           available from GeoLite databases or web services.
     */
    public readonly bool $isAnycast;

    /**
     * @var bool This is true if the IP address belongs
     *           to a hosting or VPN provider (see description of isAnonymousVpn property).
     *           This property is only available from GeoIP2 Insights.
     */
    public readonly bool $isHostingProvider;

    /**
     * @var bool This attribute is true if MaxMind
     *           believes this IP address to be a legitimate proxy, such as an internal
     *           VPN used by a corporation. This attribute is only available in the GeoIP2
     *           Enterprise database.
     */
    public readonly bool $isLegitimateProxy;

    /**
     * @var bool This is true if the IP address belongs to
     *           a public proxy. This property is only available from GeoIP2 Insights.
     */
    public readonly bool $isPublicProxy;

    /**
     * @var bool This is true if the IP address is
     *           on a suspected anonymizing network and belongs to a residential ISP. This
     *           property is only available from GeoIP2 Insights.
     */
    public readonly bool $isResidentialProxy;

    /**
     * @var bool This is true if the IP address is a Tor
     *           exit node. This property is only available from GeoIP2 Insights.
     */
    public readonly bool $isTorExitNode;

    /**
     * @var string|null The name of the ISP associated with the IP
     *                  address. This attribute is only available from the City Plus and Insights
     *                  web services and the GeoIP2 Enterprise database.
     */
    public readonly ?string $isp;

    /**
     * @var string|null The [mobile country code
     *                  (MCC)](https://en.wikipedia.org/wiki/Mobile_country_code) associated with
     *                  the IP address and ISP. This property is available from the City Plus and
     *                  Insights web services and the GeoIP2 Enterprise database.
     */
    public readonly ?string $mobileCountryCode;

    /**
     * @var string|null The [mobile network code
     *                  (MNC)](https://en.wikipedia.org/wiki/Mobile_country_code) associated with
     *                  the IP address and ISP. This property is available from the City Plus and
     *                  Insights web services and the GeoIP2 Enterprise database.
     */
    public readonly ?string $mobileNetworkCode;

    /**
     * @var string|null The network in CIDR notation associated with
     *                  the record. In particular, this is the largest network where all of the
     *                  fields besides $ipAddress have the same value.
     */
    public readonly ?string $network;

    /**
     * @var string|null The name of the organization
     *                  associated with the IP address. This attribute is only available from the
     *                  City Plus and Insights web services and the GeoIP2 Enterprise database.
     */
    public readonly ?string $organization;

    /**
     * @var float|null An indicator of how static or
     *                 dynamic an IP address is. This property is only available from GeoIP2
     *                 Insights.
     */
    public readonly ?float $staticIpScore;

    /**
     * @var int|null The estimated number of users sharing
     *               the IP/network during the past 24 hours. For IPv4, the count is for the
     *               individual IP. For IPv6, the count is for the /64 network. This property is
     *               only available from GeoIP2 Insights.
     */
    public readonly ?int $userCount;

    /**
     * @var string|null <p>The user type associated with the IP
     *  address. This can be one of the following values:</p>
     *  <ul>
     *    <li>business
     *    <li>cafe
     *    <li>cellular
     *    <li>college
     *    <li>consumer_privacy_network
     *    <li>content_delivery_network
     *    <li>dialup
     *    <li>government
     *    <li>hosting
     *    <li>library
     *    <li>military
     *    <li>residential
     *    <li>router
     *    <li>school
     *    <li>search_engine_spider
     *    <li>traveler
     * </ul>
     * <p>
     *   This attribute is only available from the Insights web service and the
     *   GeoIP2 Enterprise database.
     * </p>
     */
    public readonly ?string $userType;

    /**
     * @ignore
     *
     * @param array<string, mixed> $record
     */
    public function __construct(array $record)
    {
        $this->autonomousSystemNumber = $record['autonomous_system_number'] ?? null;
        $this->autonomousSystemOrganization = $record['autonomous_system_organization'] ?? null;
        $this->connectionType = $record['connection_type'] ?? null;
        $this->domain = $record['domain'] ?? null;
        $this->ipAddress = $record['ip_address'] ?? null;
        $this->isAnonymous = $record['is_anonymous'] ?? false;
        $this->isAnonymousVpn = $record['is_anonymous_vpn'] ?? false;
        $this->isAnycast = $record['is_anycast'] ?? false;
        $this->isHostingProvider = $record['is_hosting_provider'] ?? false;
        $this->isLegitimateProxy = $record['is_legitimate_proxy'] ?? false;
        $this->isp = $record['isp'] ?? null;
        $this->isPublicProxy = $record['is_public_proxy'] ?? false;
        $this->isResidentialProxy = $record['is_residential_proxy'] ?? false;
        $this->isTorExitNode = $record['is_tor_exit_node'] ?? false;
        $this->mobileCountryCode = $record['mobile_country_code'] ?? null;
        $this->mobileNetworkCode = $record['mobile_network_code'] ?? null;
        $this->organization = $record['organization'] ?? null;
        $this->staticIpScore = $record['static_ip_score'] ?? null;
        $this->userCount = $record['user_count'] ?? null;
        $this->userType = $record['user_type'] ?? null;

        if (isset($record['network'])) {
            $this->network = $record['network'];
        } else {
            $this->network = isset($record['prefix_len']) ? Util::cidr($this->ipAddress, $record['prefix_len']) : null;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $js = [];
        if ($this->autonomousSystemNumber !== null) {
            $js['autonomous_system_number'] = $this->autonomousSystemNumber;
        }
        if ($this->autonomousSystemOrganization !== null) {
            $js['autonomous_system_organization'] = $this->autonomousSystemOrganization;
        }
        if ($this->connectionType !== null) {
            $js['connection_type'] = $this->connectionType;
        }
        if ($this->domain !== null) {
            $js['domain'] = $this->domain;
        }
        if ($this->ipAddress !== null) {
            $js['ip_address'] = $this->ipAddress;
        }
        if ($this->isAnonymous !== false) {
            $js['is_anonymous'] = $this->isAnonymous;
        }
        if ($this->isAnonymousVpn !== false) {
            $js['is_anonymous_vpn'] = $this->isAnonymousVpn;
        }
        if ($this->isAnycast !== false) {
            $js['is_anycast'] = $this->isAnycast;
        }
        if ($this->isHostingProvider !== false) {
            $js['is_hosting_provider'] = $this->isHostingProvider;
        }
        if ($this->isLegitimateProxy !== false) {
            $js['is_legitimate_proxy'] = $this->isLegitimateProxy;
        }
        if ($this->isPublicProxy !== false) {
            $js['is_public_proxy'] = $this->isPublicProxy;
        }
        if ($this->isResidentialProxy !== false) {
            $js['is_residential_proxy'] = $this->isResidentialProxy;
        }
        if ($this->isTorExitNode !== false) {
            $js['is_tor_exit_node'] = $this->isTorExitNode;
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
        if ($this->network !== null) {
            $js['network'] = $this->network;
        }
        if ($this->organization !== null) {
            $js['organization'] = $this->organization;
        }
        if ($this->staticIpScore !== null) {
            $js['static_ip_score'] = $this->staticIpScore;
        }
        if ($this->userCount !== null) {
            $js['user_count'] = $this->userCount;
        }
        if ($this->userType !== null) {
            $js['user_type'] = $this->userType;
        }

        return $js;
    }
}
