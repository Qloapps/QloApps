<?php

declare(strict_types=1);

namespace GeoIp2\Database;

use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Model\AnonymousIp;
use GeoIp2\Model\Asn;
use GeoIp2\Model\City;
use GeoIp2\Model\ConnectionType;
use GeoIp2\Model\Country;
use GeoIp2\Model\Domain;
use GeoIp2\Model\Enterprise;
use GeoIp2\Model\Isp;
use GeoIp2\ProviderInterface;
use MaxMind\Db\Reader as DbReader;
use MaxMind\Db\Reader\InvalidDatabaseException;
use MaxMind\Db\Reader\Metadata;

/**
 * Instances of this class provide a reader for the GeoIP2 database format.
 * IP addresses can be looked up using the database specific methods.
 *
 * ## Usage ##
 *
 * The basic API for this class is the same for every database. First, you
 * create a reader object, specifying a file name. You then call the method
 * corresponding to the specific database, passing it the IP address you want
 * to look up.
 *
 * If the request succeeds, the method call will return a model class for
 * the method you called. This model in turn contains multiple record classes,
 * each of which represents part of the data returned by the database. If
 * the database does not contain the requested information, the attributes
 * on the record class will have a `null` value.
 *
 * If the address is not in the database, an
 * {@link \GeoIp2\Exception\AddressNotFoundException} exception will be
 * thrown. If an invalid IP address is passed to one of the methods, a
 * SPL {@link \InvalidArgumentException} will be thrown. If the database is
 * corrupt or invalid, a {@link \MaxMind\Db\Reader\InvalidDatabaseException}
 * will be thrown.
 */
class Reader implements ProviderInterface
{
    private DbReader $dbReader;

    private string $dbType;

    /**
     * @var array<string>
     */
    private array $locales;

    /**
     * Constructor.
     *
     * @param string        $filename the path to the GeoIP2 database file
     * @param array<string> $locales  list of locale codes to use in name property
     *                                from most preferred to least preferred
     *
     * @throws InvalidDatabaseException if the database is corrupt or invalid
     */
    public function __construct(
        string $filename,
        array $locales = ['en']
    ) {
        $this->dbReader = new DbReader($filename);
        $this->dbType = $this->dbReader->metadata()->databaseType;
        $this->locales = $locales;
    }

    /**
     * This method returns a GeoIP2 City model.
     *
     * @param string $ipAddress an IPv4 or IPv6 address as a string
     *
     * @throws AddressNotFoundException if the address is not in the database
     * @throws InvalidDatabaseException if the database is corrupt or invalid
     */
    public function city(string $ipAddress): City
    {
        return $this->modelFor(City::class, 'City', $ipAddress);
    }

    /**
     * This method returns a GeoIP2 Country model.
     *
     * @param string $ipAddress an IPv4 or IPv6 address as a string
     *
     * @throws AddressNotFoundException if the address is not in the database
     * @throws InvalidDatabaseException if the database is corrupt or invalid
     */
    public function country(string $ipAddress): Country
    {
        return $this->modelFor(Country::class, 'Country', $ipAddress);
    }

    /**
     * This method returns a GeoIP2 Anonymous IP model.
     *
     * @param string $ipAddress an IPv4 or IPv6 address as a string
     *
     * @throws AddressNotFoundException if the address is not in the database
     * @throws InvalidDatabaseException if the database is corrupt or invalid
     */
    public function anonymousIp(string $ipAddress): AnonymousIp
    {
        return $this->flatModelFor(
            AnonymousIp::class,
            'GeoIP2-Anonymous-IP',
            $ipAddress
        );
    }

    /**
     * This method returns a GeoLite2 ASN model.
     *
     * @param string $ipAddress an IPv4 or IPv6 address as a string
     *
     * @throws AddressNotFoundException if the address is not in the database
     * @throws InvalidDatabaseException if the database is corrupt or invalid
     */
    public function asn(string $ipAddress): Asn
    {
        return $this->flatModelFor(
            Asn::class,
            'GeoLite2-ASN',
            $ipAddress
        );
    }

    /**
     * This method returns a GeoIP2 Connection Type model.
     *
     * @param string $ipAddress an IPv4 or IPv6 address as a string
     *
     * @throws AddressNotFoundException if the address is not in the database
     * @throws InvalidDatabaseException if the database is corrupt or invalid
     */
    public function connectionType(string $ipAddress): ConnectionType
    {
        return $this->flatModelFor(
            ConnectionType::class,
            'GeoIP2-Connection-Type',
            $ipAddress
        );
    }

    /**
     * This method returns a GeoIP2 Domain model.
     *
     * @param string $ipAddress an IPv4 or IPv6 address as a string
     *
     * @throws AddressNotFoundException if the address is not in the database
     * @throws InvalidDatabaseException if the database is corrupt or invalid
     */
    public function domain(string $ipAddress): Domain
    {
        return $this->flatModelFor(
            Domain::class,
            'GeoIP2-Domain',
            $ipAddress
        );
    }

    /**
     * This method returns a GeoIP2 Enterprise model.
     *
     * @param string $ipAddress an IPv4 or IPv6 address as a string
     *
     * @throws AddressNotFoundException if the address is not in the database
     * @throws InvalidDatabaseException if the database is corrupt or invalid
     */
    public function enterprise(string $ipAddress): Enterprise
    {
        return $this->modelFor(Enterprise::class, 'Enterprise', $ipAddress);
    }

    /**
     * This method returns a GeoIP2 ISP model.
     *
     * @param string $ipAddress an IPv4 or IPv6 address as a string
     *
     * @throws AddressNotFoundException if the address is not in the database
     * @throws InvalidDatabaseException if the database is corrupt or invalid
     */
    public function isp(string $ipAddress): Isp
    {
        return $this->flatModelFor(
            Isp::class,
            'GeoIP2-ISP',
            $ipAddress
        );
    }

    private function modelFor(string $class, string $type, string $ipAddress): object
    {
        [$record, $prefixLen] = $this->getRecord($class, $type, $ipAddress);

        $record['traits']['ip_address'] = $ipAddress;
        $record['traits']['prefix_len'] = $prefixLen;

        return new $class($record, $this->locales);
    }

    private function flatModelFor(string $class, string $type, string $ipAddress): object
    {
        [$record, $prefixLen] = $this->getRecord($class, $type, $ipAddress);

        $record['ip_address'] = $ipAddress;
        $record['prefix_len'] = $prefixLen;

        return new $class($record);
    }

    /**
     * @return array{0:array<string, mixed>, 1:int}
     */
    private function getRecord(string $class, string $type, string $ipAddress): array
    {
        if (!str_contains($this->dbType, $type)) {
            $method = lcfirst((new \ReflectionClass($class))->getShortName());

            throw new \BadMethodCallException(
                "The $method method cannot be used to open a {$this->dbType} database"
            );
        }
        [$record, $prefixLen] = $this->dbReader->getWithPrefixLen($ipAddress);
        if ($record === null) {
            throw new AddressNotFoundException(
                "The address $ipAddress is not in the database."
            );
        }
        if (!\is_array($record)) {
            // This can happen on corrupt databases. Generally,
            // MaxMind\Db\Reader will throw a
            // MaxMind\Db\Reader\InvalidDatabaseException, but occasionally
            // the lookup may result in a record that looks valid but is not
            // an array. This mostly happens when the user is ignoring all
            // exceptions and the more frequent InvalidDatabaseException
            // exceptions go unnoticed.
            throw new InvalidDatabaseException(
                "Expected an array when looking up $ipAddress but received: "
                . \gettype($record)
            );
        }

        return [$record, $prefixLen];
    }

    /**
     * @throws \InvalidArgumentException if arguments are passed to the method
     * @throws \BadMethodCallException   if the database has been closed
     *
     * @return Metadata object for the database
     */
    public function metadata(): Metadata
    {
        return $this->dbReader->metadata();
    }

    /**
     * Closes the GeoIP2 database and returns the resources to the system.
     */
    public function close(): void
    {
        $this->dbReader->close();
    }
}
