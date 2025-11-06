CHANGELOG
=========

1.12.0 (2024-11-14)
-------------------

* Improve the error handling when the user tries to open a directory
  with the pure PHP reader.
* Improve the typehints on arrays in the PHPDocs.

1.11.1 (2023-12-01)
-------------------

* Resolve warnings when compiling the C extension.
* Fix various type issues detected by PHPStan level. Pull request by
  LauraTaylorUK. GitHub #160.

1.11.0 (2021-10-18)
-------------------

* Replace runtime define of a constant to facilitate opcache preloading.
  Reported by vedadkajtaz. GitHub #134.
* Resolve minor issue found by the Clang static analyzer in the C
  extension.

1.10.1 (2021-04-14)
-------------------

* Fix a `TypeError` exception in the pure PHP reader when using large
  databases on 32-bit PHP builds with the `bcmath` extension. Reported
  by dodo1708. GitHub #124.

1.10.0 (2021-02-09)
-------------------

* When using the pure PHP reader, unsigned integers up to PHP_MAX_INT
  will now be integers in PHP rather than strings. Previously integers
  greater than 2^24 on 32-bit platforms and 2^56 on 64-bit platforms
  would be strings due to the use of `gmp` or `bcmath` to decode them.
  Reported by Alejandro Celaya. GitHub #119.

1.9.0 (2021-01-07)
------------------

* The `maxminddb` extension is now buildable on Windows. Pull request
  by Jan Ehrhardt. GitHub #115.

1.8.0 (2020-10-01)
------------------

* Fixes for PHP 8.0. Pull Request by Remi Collet. GitHub #108.

1.7.0 (2020-08-07)
------------------

* IMPORTANT: PHP 7.2 or greater is now required.
* The extension no longer depends on the pure PHP classes in
  `maxmind-db/reader`. You can use it independently.
* Type hints have been added to both the pure PHP implementation
  and the extension.
* The `metadata` method on the reader now returns a new copy of the
  metadata object rather than the actual object used by the reader.
* Work around PHP `is_readable()` bug. Reported by Ben Roberts. GitHub
  #92.
* This is the first release of the extension as a PECL package.
  GitHub #34.

1.6.0 (2019-12-19)
------------------

* 1.5.0 and 1.5.1 contained a possible memory corruptions when using
  `getWithPrefixLen`. This has been fixed. Reported by proton-ab.
  GitHub #96.
* The `composer.json` file now conflicts with all versions of the
  `maxminddb` C extension less than the Composer version. This is to
  reduce the chance of having an older, conflicting version of the
  extension installed. You will need to upgrade the extension before
  running `composer update`. Pull request by Benoît Burnichon. GitHub
  #97.

1.5.1 (2019-12-12)
------------------

* Minor performance improvements.
* Make tests pass with older versions of libmaxminddb. PR by Remi
  Collet. GitHub #90.
* Test enhancements. PR by Chun-Sheng, Li. GitHub #91.

1.5.0 (2019-09-30)
------------------

* PHP 5.6 or greater is now required.
* The C extension now supports PHP 8. Pull request by John Boehr.
  GitHub #87.
* A new method, `getWithPrefixLen`, was added to the `Reader` class.
  This method returns an array containing the record and the prefix
  length for that record. GitHub #89.

1.4.1 (2019-01-04)
------------------

* The `maxminddb` extension now returns a string when a `uint32`
  value is greater than `LONG_MAX`. Previously, the value would
  overflow. This generally only affects 32-bit machines.  Reported
  by Remi Collet. GitHub #79.
* For `uint64` values, the `maxminddb` extension now returns an
  integer rather than a string when the value is less than or equal
  to `LONG_MAX`. This more closely matches the behavior of the pure
  PHP reader.

1.4.0 (2018-11-20)
------------------

* The `maxminddb` extension now has the arginfo when using reflection.
  PR by Remi Collet. GitHub #75.
* The `maxminddb` extension now provides `MINFO()` function that
  displays the extension version and the libmaxminddb version. PR by
  Remi Collet. GitHub #74.
* The `maxminddb` `configure` script now uses `pkg-config` when
  available to get libmaxmindb build info. PR by Remi Collet.
  GitHub #73.
* The pure PHP reader now correctly decodes integers on 32-bit platforms.
  Previously, large integers would overflow. Reported by Remi Collet.
  GitHub #77.
* There are small performance improvements for the pure PHP reader.

1.3.0 (2018-02-21)
------------------

* IMPORTANT: The `maxminddb` extension now obeys `open_basedir`. If
  `open_basedir` is set, you _must_ store the database within the
  specified directory. Placing the file outside of this directory
  will result in an exception. Please test your integration before
  upgrading the extension. This does not affect the pure PHP
  implementation, which has always had this restriction. Reported
  by Benoît Burnichon. GitHub #61.
* A custom `autoload.php` file is provided for installations without
  Composer. GitHub #56.

1.2.0 (2017-10-27)
------------------

* PHP 5.4 or greater is now required.
* The `Reader` class for the `maxminddb` extension is no longer final.
  This was change to match the behavior of the pure PHP class.
  Reported and fixed by venyii. GitHub #52 & #54.

1.1.3 (2017-01-19)
------------------

* Fix incorrect version in `ext/php_maxminddb.h`. GitHub #48.

1.1.2 (2016-11-22)
------------------

* Searching for database metadata only occurs within the last 128KB
  (128 * 1024 bytes) of the file, speeding detection of corrupt
  datafiles. Reported by Eric Teubert. GitHub #42.
* Suggest relevant extensions when installing with Composer. GitHub #37.

1.1.1 (2016-09-15)
------------------

* Development files were added to the `.gitattributes` as `export-ignore` so
  that they are not part of the Composer release. Pull request by Michele
  Locati. GitHub #39.

1.1.0 (2016-01-04)
------------------

* The MaxMind DB extension now supports PHP 7. Pull request by John Boehr.
  GitHub #27.

1.0.3 (2015-03-13)
------------------

* All uses of `strlen` were removed. This should prevent issues in situations
  where the function is overloaded or otherwise broken.

1.0.2 (2015-01-19)
------------------

* Previously the MaxMind DB extension would cause a segfault if the Reader
  object's destructor was called without first having called the constructor.
  (Reported by Matthias Saou & Juan Peri. GitHub #20.)

1.0.1 (2015-01-12)
------------------

* In the last several releases, the version number in the extension was
  incorrect. This release is being done to correct it. No other code changes
  are included.

1.0.0 (2014-09-22)
------------------

* First production release.
* In the pure PHP reader, a string length test after `fread()` was replaced
  with the difference between the start pointer and the end pointer. This
  provided a 15% speed increase.

0.3.3 (2014-09-15)
------------------

* Clarified behavior of 128-bit type in documentation.
* Updated phpunit and fixed some test breakage from the newer version.

0.3.2 (2014-09-10)
------------------

* Fixed invalid reference to global class RuntimeException from namespaced
  code. Fixed by Steven Don. GitHub issue #15.
* Additional documentation of `Metadata` class as well as misc. documentation
  cleanup.

0.3.1 (2014-05-01)
------------------

* The API now works when `mbstring.func_overload` is set.
* BCMath is no longer required. If the decoder encounters a big integer,
  it will try to use GMP and then BCMath. If both of those fail, it will
  throw an exception. No databases released by MaxMind currently use big
  integers.
* The API now officially supports HHVM when using the pure PHP reader.

0.3.0 (2014-02-19)
------------------

* This API is now licensed under the Apache License, Version 2.0.
* The code for the C extension was cleaned up, fixing several potential
  issues.

0.2.0 (2013-10-21)
------------------

* Added optional C extension for using libmaxminddb in place of the pure PHP
  reader.
* Significantly improved error handling in pure PHP reader.
* Improved performance for IPv4 lookups in an IPv6 database.

0.1.0 (2013-07-16)
------------------

* Initial release
