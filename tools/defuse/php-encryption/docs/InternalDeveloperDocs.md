Information for the Developers of php-encryption
=================================================

Status
-------

This library is currently frozen under a long-term support release. We do not
plan to add any new features. We will maintain the library by fixing any bugs
that are reported, or security vulnerabilities that are found.

Development Environment
------------------------

Development is done on Linux. To run the tests, you will need to have the
following tools installed:

- `php` (with OpenSSL enabled, if you're compiling from source).
- `gpg`
- `composer`

Running the Tests
------------------

First do `composer install` and then you can run the tests by running
`./test.sh`. This will download a PHPUnit PHAR, verify its cryptographic
signatures, and then use it to run the tests in `test/unit`.

Getting and Using Psalm
-----------------------

[Psalm](https://github.com/vimeo/psalm) is a static analysis suite for PHP projects.
We use Psalm to ensure type safety throughout our library.

To install Psalm, you just need to run one command:

    composer require --dev "vimeo/psalm:dev-master"

To verify that your code changes are still strictly type-safe, run the following
command:

    vendor/bin/psalm

Reporting Bugs
---------------

Please report bugs, even critical security vulnerabilities, by opening an issue
on GitHub. We recommend disclosing security vulnerabilities found in this
library *publicly* as soon as possible.

Philosophy
-----------

This library is developed around several core values:

- Rule #1: Security is prioritized over everything else.

    > Whenever there is a conflict between security and some other property,
    > security will be favored. For example, the library has runtime tests,
    > which make it slower, but will hopefully stop it from encrypting stuff
    > if the platform it's running on is broken.

- Rule #2: It should be difficult to misuse the library.

    > We assume the developers using this library have no experience with
    > cryptography. We only assume that they know that the "key" is something
    > you need to encrypt and decrypt the messages, and that it must be kept
    > secret. Whenever possible, the library should refuse to encrypt or decrypt
    > messages when it is not being used correctly.

- Rule #3: The library aims only to be compatible with itself.

    > Other PHP encryption libraries try to support every possible type of
    > encryption, even the insecure ones (e.g. ECB mode). Because there are so
    > many options, inexperienced developers must decide whether to use "CBC
    > mode" or "ECB mode" when both are meaningless terms to them. This
    > inevitably leads to vulnerabilities.

    > This library will only support one secure mode. A developer using this
    > library will call "encrypt" and "decrypt" methods without worrying about
    > how they are implemented.

- Rule #4: The library should require no special installation.

    > Some PHP encryption libraries, like libsodium-php, are not straightforward
    > to install and cannot packaged with "just download and extract"
    > applications. This library will always be just a handful of PHP files that
    > you can copy to your source tree and require().

Publishing Releases
--------------------

To make a release, you will need to install [composer](https://getcomposer.org/)
and [box](https://github.com/box-project/box2) on your system. They will need to
be available in your `$PATH` so that running the commands `composer` and `box`
in your terminal run them, respectively. You will also need the private key for
signing (ID: 7B4B2D98) available.

Once you have those tools installed and the key available follow these steps:

**Remember to set the version number in `composer.json`!**

Make a fresh clone of the repository:

```
git clone <url>
```

Check out the branch you want to release:

```
git checkout <branchname>
```

Check that the version number in composer.json is correct (or not specified so that it gets picked up from the git tag):

```
cat composer.json
```

Check that the version number and support lifetime in README.md are correct:

```
cat README.md
```

Run the tests:

```
composer install
./test.sh
```

Generate the `.phar`:

```
cd dist
make build-phar
```

Test the `.phar`:

```
cd ../
./test.sh dist/phar-testing-autoload.php
```

Sign the `.phar`:

```
cd dist
make sign-phar
```

Tag the release:

```
git -c user.signingkey=DD2E507F7BDB1669 tag -s "<TAG NAME>" -m "<TAG MESSAGE>"
```

`<TAG NAME>` should be in the format `v2.0.0` and `<TAG MESSAGE>` should look
like "Release of v2.0.0."

Push the tag to github, then use the
[releases](https://github.com/defuse/php-encryption/releases) page to draft
a new release for that tag. Upload the `.phar` and the `.phar.sig` file to be
included as part of that release.
