{
    "name": "composer/ca-bundle",
    "description": "Lets you find a path to the system CA bundle, and includes a fallback to the Mozilla CA bundle.",
    "type": "library",
    "license": "MIT",
    "keywords": [
        "cabundle",
        "cacert",
        "certificate",
        "ssl",
        "tls"
    ],
    "authors": [
        {
            "name": "Jordi Boggiano",
            "email": "j.boggiano@seld.be",
            "homepage": "http://seld.be"
        }
    ],
    "support": {
        "irc": "irc://irc.freenode.org/composer",
        "issues": "https://github.com/composer/ca-bundle/issues"
    },
    "require": {
        "ext-openssl": "*",
        "ext-pcre": "*",
        "php": "^7.2 || ^8.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^8 || ^9",
        "phpstan/phpstan": "^1.10",
        "psr/log": "^1.0 || ^2.0 || ^3.0",
        "symfony/process": "^4.0 || ^5.0 || ^6.0 || ^7.0"
    },
    "autoload": {
        "psr-4": {
            "Composer\\CaBundle\\": "src"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Composer\\CaBundle\\": "tests"
        }
    },
    "extra": {
        "branch-alias": {
            "dev-main": "1.x-dev"
        }
    },
    "scripts": {
        "test": "@php phpunit",
        "phpstan": "@php phpstan analyse"
    }
}
