# Change log

# 4.8.09

- [x] `sha1` is now the default fn for encoding cache keys. Using `base64` [was causing problems](https://github.com/serbanghita/Mobile-Detect/issues/974#issuecomment-2531597903) in Laravel.

# 4.8.08

- [x] fix for missing psr/cache prod dependency 
- [bug] latest 4.8.07 cause site error Call to a member function get() on false #974
- [x] fix for Docker build not installing dev dependencies

# 4.8.07 (broken in composer, please skip)

- [x] fix cache and generate short cache key (#971)
- [x] Added configuration cacheKeyFn which allows for using a custom cache key creation fn.
- [x] Use Client Hints Sec-CH-UA-Mobile header to detect mobile (#962)
- [x] added Huawei (phone, OS - HarmonyOS, browser) detection (#952)
- [x] Bugfix: Allow Injection of Any PSR Cache Interface (#966)
- [x] PHP 8.4 - implicit nulls are deprecated (#960)


# 4.8.03
- [x] added optional `$config` to MobileDetect constructor.
- [x] added `autoInitOfHttpHeaders` configuration which is by default `true`. This enabled the old behavior from `3.x` and `2.x` that allows automatic detection of HTTP headers and User Agent from $_SERVER.
- [x] refactored internal CloudFront related methods and the way `setHttpHeaders` work. It no longer falls back on `$_SERVER`. The method still calls `setUserAgent` in case `HTTP_USER_AGENT` and friends are present.
- [x] added `maximumUserAgentLength` to the `$config`, by default the limit is `500`.

# 4.8.02
- [x] new user agents
- [x] Samsung Galaxy Tab S6 Lite #919 
- [x] Samsung Galaxy Tab S8 series #912

# 4.8.01

- [x] PHP 8.x only.
- [x] PSR-16 cache support.
- [x] Constructor accepts `CacheFactory` class where you can inject your own PSR-6 Cache interfaces.
- [x] You need to explicitly `setUserAgent("...")` or `setUserAgentHeaders([...])` otherwise an exception is being thrown.
- [x] `scripts/` folder no longer included in the git tag release archive.
- [x] added performance tests
- [x] regexes can be arrays of strings or strings


# 2023

Launched 4.8.xx which contains PHP 8.x support, refactorings and external Cache support.

## 2022

In December 2022 we released the version for PHP7.
Mobile Detect was split into two dev branches: `2.8.x` which will support PHP5, but is deprecated and
`3.74.x` which supports PHP >= 7.3

## 2013

In August 2013 the library has 1800+ stargazers and support for: composer, PHPUnit tests, PSR standards and a new webpage http://mobiledetect.net

# 2012

Throughout 2012 the script has been updated constantly, and we have received tons of feedback and requests.
In July 2012 we moved the repository from Google Code to GitHub in order to quickly accommodate the frequent updates and to involve more people.

## 2011

In December 2011 it received a major update from the first version, an important number of issues were fixed, then 2.0 was launched. 
The new version marks a new mindset and also featuring tablet detection.

## 2009

The first version of the script was developed in 2009, and it was hosted at https://code.google.com/p/php-mobile-detect/, it was a small project with around 30 stars. 
(Original blog post by Victor: http://victorstanciu.ro/detectarea-platformelor-mobile-in-php/)
