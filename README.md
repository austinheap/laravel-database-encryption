# Laravel 5.5+ Database Encryption Package

![laravel-database-encryption banner from the documentation](docs/img/banner-1544x500.png?raw=true)

[![License](https://img.shields.io/packagist/l/austinheap/laravel-database-encryption.svg)](https://github.com/austinheap/laravel-database-encryption/blob/master/LICENSE.md)
[![Current Release](https://img.shields.io/github/release/austinheap/laravel-database-encryption.svg)](https://github.com/austinheap/laravel-database-encryption/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/austinheap/laravel-database-encryption.svg)](https://packagist.org/packages/austinheap/laravel-database-encryption)
[![Build Status](https://travis-ci.org/austinheap/laravel-database-encryption.svg?branch=master)](https://travis-ci.org/austinheap/laravel-database-encryption)
[![Dependency Status](https://gemnasium.com/badges/github.com/austinheap/laravel-database-encryption.svg)](https://gemnasium.com/github.com/austinheap/laravel-database-encryption)
[![Scrutinizer CI](https://scrutinizer-ci.com/g/austinheap/laravel-database-encryption/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/austinheap/laravel-database-encryption/)
[![StyleCI](https://styleci.io/repos/106077909/shield?branch=master)](https://styleci.io/repos/106077909)
[![Maintainability](https://api.codeclimate.com/v1/badges/ca1e10510f778f520bb5/maintainability)](https://codeclimate.com/github/austinheap/laravel-database-encryption/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/ca1e10510f778f520bb5/test_coverage)](https://codeclimate.com/github/austinheap/laravel-database-encryption/test_coverage)
[![SensioLabs](https://insight.sensiolabs.com/projects/9fe66b91-58ad-4bc3-9ec9-37b396bb4837/mini.png)](https://insight.sensiolabs.com/projects/9fe66b91-58ad-4bc3-9ec9-37b396bb4837)

## A package for automatically encrypting and decrypting Eloquent attributes in Laravel 5.5+, based on configuration settings.

The purpose of this project is to create a set-it-and-forget-it package that can be
installed without much effort to get a Laravel project compliant with the current
[`security.txt`](https://securitytxt.org/) spec. It is therefore highly opinionated
but built for configuration.

When enabled, it allows access to all clients and serves up the `security.txt`.
Otherwise, it operates almost identically to Laravel's default configuration,
denying access to all clients.

[`security.txt`](https://github.com/securitytxt) is a [draft](https://tools.ietf.org/html/draft-foudil-securitytxt-00)
"standard" which allows websites to define security policies. This "standard"
sets clear guidelines for security researchers on how to report security issues,
and allows bug bounty programs to define a scope. Security.txt is the equivalent
of `robots.txt`, but for security issues.

There is [documentation for `laravel-database-encryption` online](https://austinheap.github.io/laravel-database-encryption/),
the source of which is in the [`docs/`](https://github.com/austinheap/laravel-database-encryption/tree/master/docs)
directory. The most logical place to start are the [docs for the `HasEncryptedAttributes` trait](https://austinheap.github.io/laravel-database-encryption/classes/AustinHeap.Database.Encryption.Traits.HasEncryptedAttributes.html).

## Installation

### Step 1: Composer

Via Composer command line:

```bash
$ composer require austinheap/laravel-database-encryption
```

Or add the package to your `composer.json`:

```json
{
    "require": {
        "austinheap/laravel-database-encryption": "0.0.1"
    }
}
```

### Step 2: Remove any existing `security.txt`

Laravel doesn't ship with a default `security.txt` file. If you have added one, it needs to be removed for the configured route to work.

```bash
$ rm public/.well-known/security.txt
```

### Step 3: Enable the package (Optional)

This package implements Laravel 5.5's auto-discovery feature. After you install it the package provider and facade are added automatically.

If you would like to declare the provider and/or alias explicitly, then add the service provider to your `config/app.php`:

Add the service provider to your `config/app.php`:

```php
'providers' => [
    //
    AustinHeap\Database\Encryption\EncryptionServiceProvider::class,
];
```

And then add the alias to your `config/app.php`:

```php
'aliases' => [
    //
    'DatabaseEncryption' => AustinHeap\Database\EncryptionFacade::class,
];
```

### Step 4: Configure the package

Publish the package config file:

```bash
$ php artisan vendor:publish --provider="AustinHeap\Database\Encryption\EncryptionServiceProvider"
```

You may now enable automagic encryption and decryption of Eloquent models by editing the `config/database-encryption.php` file:

```php
return [
    'enabled' => env('DATABASE_ENCRYPTION_ENABLED', true),
];
```

Or simply setting the the `DATABASE_ENCRYPTION_ENABLED` environment variable to true, via the Laravel `.env` file or hosting environment.

```bash
DATABASE_ENCRYPTION_ENABLED=true
```

## Full `.env` Example

After installing the package with composer, simply add the following to your .env file:

```bash
SECURITY_TXT_ENABLED=true
SECURITY_TXT_CACHE=true
SECURITY_TXT_CONTACT=security@your-site.com
SECURITY_TXT_ENCRYPTION=https://your-site.com/pgp.key
SECURITY_TXT_DISCLOSURE=full
SECURITY_TXT_ACKNOWLEDGEMENT=https://your-site.com/security-champions
```

Now point your browser to `http://your-site.com/.well-known/security.txt` and you should see:

```
# Our security address
Contact: me@austinheap.com

# Our PGP key
Encryption: http://some.url/pgp.key

# Our disclosure policy
Disclosure: Full

# Our public acknowledgement
Acknowledgement: http://some.url/acks

#
# Generated by "laravel-database-encryption" v0.4.0 (https://github.com/austinheap/laravel-database-encryption/releases/tag/v0.4.0)
# using "php-security-txt" v0.4.0 (https://github.com/austinheap/php-security-txt/releases/tag/v0.4.0)
# in 0.041008 seconds on 2017-11-22 20:31:25.
#
# Cache is enabled with key "cache:AustinHeap\Security\Txt\SecurityTxt".
#
```

## References

- [A Method for Web Security Policies (draft-foudil-securitytxt-00)](https://tools.ietf.org/html/draft-foudil-securitytxt-00)
- [php-security-txt](https://github.com/austinheap/php-security-txt)

## Credits

This is a fork of [InfusionWeb/laravel-robots-route](https://github.com/InfusionWeb/laravel-robots-route),
which was a fork of [ellisthedev/laravel-5-robots](https://github.com/ellisthedev/laravel-5-robots),
which was a fork of [jayhealey/Robots](https://github.com/jayhealey/Robots),
which was based on earlier work.

- [ellisio/laravel-5-robots Contributors](https://github.com/ellisio/laravel-5-robots/graphs/contributors)
- [InfusionWeb/laravel-robots-route Contributors](https://github.com/InfusionWeb/laravel-robots-route/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
