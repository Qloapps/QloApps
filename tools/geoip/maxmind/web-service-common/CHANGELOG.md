CHANGELOG
=========

0.10.0 (2024-11-14)
-------------------

* PHP 8.1 or greater is now required.
* Type hints for PHPStan have been improved.

0.9.0 (2022-03-28)
------------------

* Improved internal type hint usage.

0.8.1 (2020-11-02)
------------------

* We now correctly handle responses without a `Content-Type` header. In 0.8.0,
  such responses could lead to a type error. In particular, this affected the
  minFraud Report Transaction endpoint, which returns a response with no
  content. Reported by Dmitry Malashko. GitHub #99 on
  `maxmind/minfraud-api-php`.

0.8.0 (2020-10-01)
------------------

* PHP 7.2 or greater is now required.
* Added additional type hints.

0.7.0 (2020-05-06)
------------------

* Responses with a 204 status code are accepted as successes.

0.6.0 (2019-12-12)
------------------

* Curl handles are now reused across requests. Pull request by Willem
  Stuursma-Ruwen. GitHub #24.
* PHP 5.6 is now required.

0.5.0 (2018-02-12)
------------------

* Refer to account IDs using the terminology "account" rather than "user".

0.4.0 (2017-07-10)
------------------

* PHP 5.4 is now required.

0.3.1 (2016-08-10)
------------------

* On Mac OS X when using a curl built against SecureTransport, the certs
  in the system's keychain will now be used instead of the CA bundle on
  the file system.

0.3.0 (2016-08-09)
------------------

* This package now uses `composer/ca-bundle` by default rather than a CA
  bundle distributed with this package. `composer/ca-bundle` will first try
  to use the system CA bundle and will fall back to the Mozilla CA bundle
  when no system bundle is available. You may still specify your own bundle
  using the `caBundle` option.

0.2.1 (2016-06-13)
------------------

* Fix typo in code to copy cert to temp directory.

0.2.0 (2016-06-10)
------------------

* Added handling of additional error codes that the web service may return.
* A `USER_ID_UNKNOWN` error will now throw a
  `MaxMind\Exception\AuthenticationException`.
* Added support for `proxy` option. Closes #6.

0.1.0 (2016-05-23)
------------------

* A `PERMISSION_REQUIRED` error will now throw a `PermissionRequiredException`
  exception.
* Added a `.gitattributes` file to exclude tests from Composer releases.
  GitHub #7.
* Updated included cert bundle.

0.0.4 (2015-07-21)
------------------

* Added extremely basic tests for the curl calls.
* Fixed broken POSTs.

0.0.3 (2015-06-30)
------------------

* Floats now work with the `timeout` and `connectTimeout` options. Fix by
  Benjamin Pick. GitHub PR #2.
* `curl_error` is now used instead of `curl_strerror`. The latter is only
  available for PHP 5.5 or later. Fix by Benjamin Pick. GitHub PR #1.


0.0.2 (2015-06-09)
------------------

* An exception is now immediately thrown curl error rather than letting later
  status code checks throw an exception. This improves the exception message
  greatly.
* If this library is inside a phar archive, the CA certs are copied out of the
  archive to a temporary file so that curl can use them.

0.0.1 (2015-06-01)
------------------

* Initial release.
