# EmailValidator Changelog

## New Features

* Access to local part and domain part from EmailParser
* Validations outside of the scope of the RFC will be considered "extra" validations, thus opening the door for adding new; will live in their own folder "extra" (as requested in #248, #195, #183). 

## Breaking changes

* PHP version upgraded to match Symfony's (as of 12/2020).
* DNSCheckValidation now fails for missing MX records. While the RFC argues that the existence of only A records to be valid, starting in v3 they will be considered invalid.
* Emails domain part are now intenteded to be RFC 1035 compliant, rendering previous valid emails (e.g example@examp&) invalid.

## PHP versions upgrade policy
PHP version upgrade requirement will happen via MINOR (3.x) version upgrades of the library, following the adoption level by major frameworks.

## Changes
* #235
* #215
* #130
* #258
* #188
* #181
* #217
* #214
* #249
* #236
* #257
* #210

## Thanks
To contributors, be it with PRs, reporting issues or supporting otherwise.
