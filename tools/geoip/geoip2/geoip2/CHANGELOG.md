CHANGELOG
=========

3.1.0 (2024-11-15)
------------------

* This library no longer uses implicitly nullable parameter types. This
  will fix deprecation warning in PHP 8.4. Reported by Steven Lewis.
  GitHub #230.
* The PHPDoc type hints have been improved for use with PHPStan.

3.0.0 (2023-12-04)
------------------

* IMPORTANT: PHP 8.1 or greater is now required.
* BREAKING: Read-only properties are now used for the model and record
  classes rather than magic methods. This significantly improves performance.
* BREAKING: The `raw` property on model classess and the `record` property on
  record classes have been removed.
* BREAKING: On `GeoIp2\Record\Traits`, the deprecated `isAnonymousProxy` and
  `isSatelliteProvider` properties have been removed.
* BREAKING: The `jsonSerialize` output has changed.
* `GeoIp2\WebService\Client` methods now throw an `InvalidArgumentException`
  if an invalid IP address is passed to them. Previously, they would make
  a request to the web service and throw a
  `GeoIp2\Exception\InvalidRequestException`.
* The `isAnycast` property was added to `GeoIp2\Record\Traits`. This returns
  `true` if the IP address belongs to an [anycast
  network](https://en.wikipedia.org/wiki/Anycast). This is available for the
  GeoIP2 Country, City Plus, and Insights web services and the GeoIP2 Country,
  City, and Enterprise databases.

2.13.0 (2022-08-05)
-------------------

* The model class names are no longer constructed by concatenating strings.
  This change was made to improve support for tools like PHP-Scoper.
  Reported by Andrew Mead. GitHub #194.
* Box 4.0.1 is now used to generate the `geoip2.phar` file.

2.12.2 (2021-11-30)
-------------------

* The `geoip2.phar` now works when included from another directory.
  Reported by Eduardo Ruiz. GitHub #179.

2.12.1 (2021-11-23)
-------------------

* The `geoip2.phar` included in 2.12.0 would only work in CLI applications.
  This was due to a change in Box 3.x. The Phar should now work in all
  applications. This release only affects users of the Phar file.

2.12.0 (2021-11-18)
-------------------

* Support for mobile country code (MCC) and mobile network codes (MNC) was
  added for the GeoIP2 ISP and Enterprise databases as well as the GeoIP2
  City and Insights web services. `$mobileCountryCode` and
  `$mobileNetworkCode` properties were added to `GeoIp2\Model\Isp`
  for the GeoIP2 ISP database and `GeoIp2\Record\Traits` for the Enterprise
  database and the GeoIP2 City and Insights web services. We expect this data
  to be available by late January, 2022.
* `geoip2.phar` is now generated with Box 3.x.

2.11.0 (2020-10-01)
-------------------

* IMPORTANT: PHP 7.2 or greater is now required.
* Added the `isResidentialProxy` property to `GeoIp2\Model\AnonymousIP` and
  `GeoIp2\Record\Traits`.
* Additional type hints have been added.

2.10.0 (2019-12-12)
-------------------

* PHP 5.6 or greater is now required.
* The `network` property was added to `GeoIp2\Record\Traits`,
  `GeoIp2\Model\AnonymousIp`, `GeoIp2\Model\Asn`,
  `GeoIp2\Model\ConnectionType`, `Geoip2\Model\Domain`,
  and `GeoIp2\Model\Isp`. This is a string in CIDR format representing the
  largest network where all of the properties besides `ipAddress` have the
  same value.
* Updated documentation of anonymizer properties - `isAnonymousVpn`
  and `isHostingProvider` - to be more descriptive.
* The `userCount` property was added to `GeoIp2\Record\Traits`. This is an
  integer which indicates the estimated number of users sharing the
  IP/network during the past 24 hours. This output is available from GeoIP2
  Precision Insights.
* The `staticIpScore` property was added to `GeoIp2\Record\Traits`. This is
  a float which indicates how static or dynamic an IP address is. This
  output is available from GeoIP2 Precision Insights.

2.9.0 (2018-04-10)
------------------

* Refer to account IDs using the terminology "account" rather than "user".

2.8.0 (2018-01-18)
------------------

* The `isInEuropeanUnion` property was added to `GeoIp2\Record\Country`
  and `GeoIp2\Record\RepresentedCountry`. This property is `true` if the
  country is a member state of the European Union.

2.7.0 (2017-10-27)
------------------

* The following new anonymizer properties were added to `GeoIp2\Record\Traits`
  for use with GeoIP2 Precision Insights: `isAnonymous`, `isAnonymousVpn`,
  `isHostingProvider`, `isPublicProxy`, and `isTorExitNode`.

2.6.0 (2017-07-10)
-----------------

* Code clean-up and tidying.
* Set minimum required PHP version to 5.4 in `composer.json`. Previously,
  5.3 would work but was not tested. Now 5.4 is hard minimum version.

2.5.0 (2017-05-08)
------------------

* Support for PHP 5.3 was dropped.
* Added support for GeoLite2 ASN database.

2.4.5 (2017-01-31)
------------------

* Additional error checking on the data returned from `MaxMind\Db\Reader`
  was added to help detect corrupt databases. GitHub #83.

2.4.4 (2016-10-11)
------------------

* `isset()` on `mostSpecificSubdivision` attribute now returns the
  correct value. Reported by Juan Francisco Giordana. GitHub #81.

2.4.3 (2016-10-11)
------------------

* `isset()` on `name` attribute now returns the correct value. Reported by
  Juan Francisco Giordana. GitHub #79.

2.4.2 (2016-08-17)
------------------

* Updated documentation to clarify what the accuracy radius refers to.
* Upgraded `maxmind/web-service-common` to 0.3.0. This version uses
  `composer/ca-bundle` rather than our own CA bundle. GitHub #75.
* Improved PHP documentation generation.

2.4.1 (2016-06-10)
------------------

* Corrected type annotations in documentation. GitHub #66.
* Updated documentation to reflect that the accuracy radius is now included
  in City.
* Upgraded web service client, which supports setting a proxy. GitHub #59.

2.4.0 (2016-04-15)
------------------

* Added support for the GeoIP2 Enterprise database.

2.3.3 (2015-09-24)
------------------

* Corrected case on `JsonSerializable` interface. Reported by Axel Etcheverry.
  GitHub #56.

2.3.2 (2015-09-23)
------------------

* `JsonSerializable` compatibility interface was moved to `GeoIp2\Compat`
  rather than the global namespace to prevent autoloading issues. Reported by
  Tomas Buteler. GitHub #54.
* Missing documentation for the `$postal` property was added to the
  `GeoIp2\Model\City` class. Fix by Roy Sindre Norangshol. GitHub #51.
* In the Phar distribution, source files for this module no longer have their
  documentation stripped, allowing IDE introspection to work properly.
  Reported by Dominic Black. GitHub #52.

2.3.1 (2015-06-30)
------------------

* Updated `maxmind/web-service-common` to version with fixes for PHP 5.3 and
  5.4.

2.3.0 (2015-06-29)
------------------

* Support for demographics fields `averageIncome` and `populationDensity` in
  the `Location` record, returned by the Insights endpoint.
* The `isAnonymousProxy` and `isSatelliteProvider` properties on
  `GeoIP2\Record\Traits` have been deprecated. Please use our [GeoIP2
  Anonymous IP database](https://www.maxmind.com/en/geoip2-anonymous-ip-database)
  to determine whether an IP address is used by an anonymizing service.

2.2.0-beta1 (2015-06-09)
------------------------

* Typo fix in documentation.

2.2.0-alpha2 (2015-06-01)
-------------------------

* `maxmind-ws/web-service-common` was renamed to `maxmind/web-service-common`.

2.2.0-alpha1 (2015-05-22)
-------------------------

* The library no longer uses Guzzle and instead uses curl directly.
* Support for `timeout` and `connectTimout` were added to the `$options` array
  passed to the `GeoIp2\WebService\Client` constructor. Pull request by Will
  Bradley. GitHub #36.

2.1.1 (2014-12-03)
------------------

* The 2.1.0 Phar builds included a shebang line, causing issues when loading
  it as a library. This has been corrected. GitHub #33.

2.1.0 (2014-10-29)
------------------

* Update ApiGen dependency to version that isn't broken on case sensitive
  file systems.
* Added support for the GeoIP2 Anonymous IP database. The
  `GeoIP2\Database\Reader` class now has an `anonymousIp` method which returns
  a `GeoIP2\Model\AnonymousIp` object.
* Boolean attributes like those in the `GeoIP2\Record\Traits` class now return
 `false` instead of `null` when they were not true.

2.0.0 (2014-09-22)
------------------

* First production release.

0.9.0 (2014-09-15)
------------------

* IMPORTANT: The deprecated `omni()` and `cityIspOrg()` methods have been
  removed from `GeoIp2\WebService\Client`.

0.8.1 (2014-09-12)
------------------

* The check added to the `GeoIP2\Database\Reader` lookup methods in 0.8.0 did
  not work with the GeoIP2 City Database Subset by Continent with World
  Countries. This has been fixed. Fixes GitHub issue #23.

0.8.0 (2014-09-10)
------------------

* The `GeoIp2\Database\Reader` lookup methods (e.g., `city()`, `isp()`) now
  throw a `BadMethodCallException` if they are used with a database that
  does not match the method. In particular, doing a `city()` lookup on a
  GeoIP2 Country database will result in an exception, and vice versa.
* A `metadata()` method has been added to the `GeoIP2\Database\Reader` class.
  This returns a `MaxMind\Db\Reader\Metadata` class with information about the
  database.
* The name attribute was missing from the RepresentedCountry class.

0.7.0 (2014-07-22)
------------------

* The web service client API has been updated for the v2.1 release of the web
  service. In particular, the `cityIspOrg` and `omni` methods on
  `GeoIp2\WebService\Client` should be considered deprecated. The `city`
  method now provides all of the data formerly provided by `cityIspOrg`, and
  the `omni` method has been replaced by the `insights` method.
* Support was added for GeoIP2 Connection Type, Domain and ISP databases.


0.6.3 (2014-05-12)
------------------

* With the previous Phar builds, some users received `phar error: invalid url
  or non-existent phar` errors. The correct alias is now used for the Phar,
  and this should no longer be an issue.

0.6.2 (2014-05-08)
------------------

* The Phar build was broken with Guzzle 3.9.0+. This has been fixed.

0.6.1 (2014-05-01)
------------------

* This API now officially supports HHVM.
* The `maxmind-db/reader` dependency was updated to a version that does not
  require BC Math.
* The Composer compatibility autoload rules are now targeted more narrowly.
* A `box.json` file is included to build a Phar package.

0.6.0 (2014-02-19)
------------------

* This API is now licensed under the Apache License, Version 2.0.
* Model and record classes now implement `JsonSerializable`.
* `isset` now works with model and record classes.

0.5.0 (2013-10-21)
------------------

* Renamed $languages constructor parameters to $locales for both the Client
  and Reader classes.
* Documentation and code clean-up (Ben Morel).
* Added the interface `GeoIp2\ProviderInterface`, which is implemented by both
  `\GeoIp2\Database\Reader` and `\GeoIp2\WebService\Client`.

0.4.0 (2013-07-16)
------------------

* This is the first release with the GeoIP2 database reader. Please see the
  `README.md` file and the `\GeoIp2\Database\Reader` class.
* The general exception classes were replaced with specific exception classes
  representing particular types of errors, such as an authentication error.

0.3.0 (2013-07-12)
------------------

* In namespaces and class names, "GeoIP2" was renamed to "GeoIp2" to improve
  consistency.

0.2.1 (2013-06-10)
------------------

* First official beta release.
* Documentation updates and corrections.

0.2.0 (2013-05-29)
------------------

* `GenericException` was renamed to `GeoIP2Exception`.
* We now support more languages. The new languages are de, es, fr, and pt-BR.
* The REST API now returns a record with data about your account. There is
  a new `GeoIP\Records\MaxMind` class for this data.
* The `continentCode` attribute on `Continent` was renamed to `code`.
* Documentation updates.

0.1.1 (2013-05-14)
------------------

* Updated Guzzle version requirement.
* Fixed Composer example in README.md.


0.1.0 (2013-05-13)
------------------

* Initial release.
