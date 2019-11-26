# Laravel 5.5+ Database Encryption Package

![laravel-database-encryption banner from the documentation](docs/img/banner-1544x500.png?raw=true)

[![License](https://img.shields.io/packagist/l/austinheap/laravel-database-encryption.svg)](https://github.com/austinheap/laravel-database-encryption/blob/master/LICENSE.md)
[![Current Release](https://img.shields.io/github/release/austinheap/laravel-database-encryption.svg)](https://github.com/austinheap/laravel-database-encryption/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/austinheap/laravel-database-encryption.svg)](https://packagist.org/packages/austinheap/laravel-database-encryption)
[![Build Status](https://travis-ci.org/austinheap/laravel-database-encryption.svg?branch=master)](https://travis-ci.org/austinheap/laravel-database-encryption)
[![Scrutinizer CI](https://scrutinizer-ci.com/g/austinheap/laravel-database-encryption/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/austinheap/laravel-database-encryption/)
[![StyleCI](https://styleci.io/repos/113929569/shield?branch=master)](https://styleci.io/repos/113929569)
[![Maintainability](https://api.codeclimate.com/v1/badges/a63deda99383852c739b/maintainability)](https://codeclimate.com/github/austinheap/laravel-database-encryption/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/a63deda99383852c739b/test_coverage)](https://codeclimate.com/github/austinheap/laravel-database-encryption/test_coverage)

## A package for automatically encrypting and decrypting Eloquent attributes in Laravel 5.5+, based on configuration settings.

The purpose of this project is to create a set-it-and-forget-it package that can be
installed without much effort to encrypt and decrypt Eloquent model attributes stored
in your database tables, transparently. It is therefore highly opinionated but built
for [configuration](#step-3-configure-the-package).

When [enabled](#step-2-enable-the-package-optional), it automagically begins encrypting
data as it is stored in the model attributes and decrypting data as it is recalled from
the model attributes.

All data that is encrypted is prefixed with a header so that encrypted data can be
easily identified, encryption keys rotated, and (optionally) versioning of the encrypted
data format itself.

This supports columns that store either encrypted or non-encrypted data to make migration
easier.  Data can be read from columns correctly regardless of whether it is encrypted or
not but will be automatically encrypted when it is saved back into those columns. Standard
Laravel Eloquent features like attribute casting will continue to work as normal, even if
the underlying values stored in the database are encrypted by this package.

There is [documentation for `laravel-database-encryption` online](https://austinheap.github.io/laravel-database-encryption/),
the source of which is in the [`docs/`](https://github.com/austinheap/laravel-database-encryption/tree/master/docs)
directory. The most logical place to start are the [docs for the `HasEncryptedAttributes` trait](https://austinheap.github.io/laravel-database-encryption/classes/AustinHeap.Database.Encryption.Traits.HasEncryptedAttributes.html).

## Table of Contents

* [Summary](#a-package-for-automatically-encrypting-and-decrypting-eloquent-attributes-in-laravel-55-based-on-configuration-settings)
* [Requirements](#requirements)
* [Status](#status)
* [Schemas](#schemas)
* [Installation](#installation)
    + [Step 1: Composer](#step-1-composer)
    + [Step 2: Enable the package (Optional)](#step-2-enable-the-package-optional)
    + [Step 3: Configure the package](#step-3-configure-the-package)
* [Usage](#usage)
* [Keys and IVs](#keys-and-ivs)
* [Unit Tests](#unit-tests)
* [Overrides](#overrides)
* [FAQ](#faq)
    + [Can I manually encrypt or decrypt arbitrary data?](#can-i-manually-encrypt-or-decrypt-arbitrary-data)
    + [Can I search encrypted data?](#can-i-search-encrypted-data)
    + [Can I encrypt all my `User` model data?](#can-i-encrypt-all-my-user-model-data)
    + [Is this package compatible with `elocryptfive` out-of-the-box?](#is-this-package-compatible-with-elocryptfive-out-of-the-box)
    + [Is this package compatible with `insert-random-Eloquent-package-here`?](#is-this-package-compatible-with-insert-random-eloquent-package-here)
* [Implementations](#implementations)
* [Credits](#credits)
* [Contributing](#contributing)
* [License](#license)

## Requirements

* Laravel: 5.5, 5.6, 5.7, or 5.8
* PHP: 7.1, 7.2, or 7.3
* PHP [OpenSSL extension](http://php.net/manual/en/book.openssl.php)

## Status

**Framework**|**Version**|**Release**|**Status**|**PHP v7.1**|**PHP v7.2**|**PHP v7.3**
:-----:|:-----:|:-----:|:-----:|:-----:|:-----:|:-----:
Laravel|[v5.5](https://laravel.com/docs/5.5/releases)|[v0.1.0](https://github.com/austinheap/laravel-database-encryption/releases/tag/v0.1.0) ([Packagist](https://packagist.org/packages/austinheap/laravel-database-encryption#v0.1.0))|Stable|[![Build Status](https://travis-ci.org/austinheap/laravel-database-encryption.svg?branch=v0.1.0)](https://travis-ci.org/austinheap/laravel-database-encryption)|[![Build Status](https://travis-ci.org/austinheap/laravel-database-encryption.svg?branch=v0.1.0)](https://travis-ci.org/austinheap/laravel-database-encryption)|[![Build Status](https://travis-ci.org/austinheap/laravel-database-encryption.svg?branch=v0.1.0)](https://travis-ci.org/austinheap/laravel-database-encryption)
Laravel|[v5.6](https://laravel.com/docs/5.6/releases)|[v0.1.1](https://github.com/austinheap/laravel-database-encryption/releases/tag/v0.1.1) ([Packagist](https://packagist.org/packages/austinheap/laravel-database-encryption#v0.1.1))|Stable|[![Build Status](https://travis-ci.org/austinheap/laravel-database-encryption.svg?branch=v0.1.1)](https://travis-ci.org/austinheap/laravel-database-encryption)|[![Build Status](https://travis-ci.org/austinheap/laravel-database-encryption.svg?branch=v0.1.1)](https://travis-ci.org/austinheap/laravel-database-encryption)|[![Build Status](https://travis-ci.org/austinheap/laravel-database-encryption.svg?branch=v0.1.1)](https://travis-ci.org/austinheap/laravel-database-encryption)
Laravel|[v5.7](https://laravel.com/docs/5.7/releases)|[v0.2.0](https://github.com/austinheap/laravel-database-encryption/releases/tag/v0.2.0) ([Packagist](https://packagist.org/packages/austinheap/laravel-database-encryption#v0.2.0))|Stable|[![Build Status](https://travis-ci.org/austinheap/laravel-database-encryption.svg?branch=v0.2.0)](https://travis-ci.org/austinheap/laravel-database-encryption)|[![Build Status](https://travis-ci.org/austinheap/laravel-database-encryption.svg?branch=v0.2.0)](https://travis-ci.org/austinheap/laravel-database-encryption)|[![Build Status](https://travis-ci.org/austinheap/laravel-database-encryption.svg?branch=v0.2.0)](https://travis-ci.org/austinheap/laravel-database-encryption)
Laravel|[v5.8](https://laravel.com/docs/5.8/releases)|[v0.2.1](https://github.com/austinheap/laravel-database-encryption/releases/tag/v0.2.1) ([Packagist](https://packagist.org/packages/austinheap/laravel-database-encryption#v0.2.1))|Stable|[![Build Status](https://travis-ci.org/austinheap/laravel-database-encryption.svg?branch=v0.2.1)](https://travis-ci.org/austinheap/laravel-database-encryption)|[![Build Status](https://travis-ci.org/austinheap/laravel-database-encryption.svg?branch=v0.2.1)](https://travis-ci.org/austinheap/laravel-database-encryption)|[![Build Status](https://travis-ci.org/austinheap/laravel-database-encryption.svg?branch=v0.2.1)](https://travis-ci.org/austinheap/laravel-database-encryption)

## Schemas

Encrypted values are usually longer than plain text values, sometimes much longer.
You may find that the column widths in your database tables need to be altered to
store the encrypted values generated by this package.

If you are encrypting long strings such as JSON blobs then the encrypted values may
be longer than a `VARCHAR` field can support, and you will need to alter your column
types to `TEXT` or `LONGTEXT`.

The FAQ contains [migration instructions if you are moving from elocryptfive](#is-this-package-compatible-with-elocryptfive-out-of-the-box).

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
        "austinheap/laravel-database-encryption": "^0.2"
    }
}
```

### Step 2: Enable the package (Optional)

This package implements Laravel auto-discovery feature. After you install it the package
provider and facade are added automatically.

If you would like to declare the provider and/or alias explicitly, you may do so by first
adding the service provider to your `config/app.php` file:

```php
'providers' => [
    //
    AustinHeap\Database\Encryption\EncryptionServiceProvider::class,
];
```

And then add the alias to your `config/app.php` file:

```php
'aliases' => [
    //
    'DatabaseEncryption' => AustinHeap\Database\EncryptionFacade::class,
];
```

### Step 3: Configure the package

Publish the package config file:

```bash
$ php artisan vendor:publish --provider="AustinHeap\Database\Encryption\EncryptionServiceProvider"
```

You may now enable automagic encryption and decryption of Eloquent models by editing the
`config/database-encryption.php` file:

```php
return [
    'enabled' => env('DB_ENCRYPTION_ENABLED', true),
];
```

Or simply setting the the `DB_ENCRYPTION_ENABLED` environment variable to true, via
the Laravel `.env` file or hosting environment.

```bash
DB_ENCRYPTION_ENABLED=true
```

## Usage

Use the `HasEncryptedAttributes` trait in any Eloquent model that you wish to apply encryption
to and define a `protected $encrypted` array containing a list of the attributes to encrypt.

For example:

```php
    use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;

    class User extends Eloquent {
        use HasEncryptedAttributes;
       
        /**
         * The attributes that should be encrypted on save.
         *
         * @var array
         */
        protected $encrypted = [
            'address_line_1', 'first_name', 'last_name', 'postcode'
        ];
    }
```

You can combine `$casts` and `$encrypted` to store encrypted arrays. An array will first be
converted to JSON and then encrypted.

For example:

```php
    use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;

    class User extends Eloquent {
        use HasEncryptedAttributes;

        protected $casts     = ['extended_data' => 'array'];
        protected $encrypted = ['extended_data'];
    }
```

By including the `HasEncryptedAttributes` trait, the `setAttribute()` and `getAttributeFromArray()`
methods provided by Eloquent are overridden to include an additional step. This additional step
simply checks whether the attribute being accessed via setter/getter is included in the `$encrypted`
array on the model, and then encrypts or decrypts it accordingly.

## Keys and IVs

The key and encryption algorithm used is the default Laravel `Encrypter` service, and configured in
your `config/app.php`:

```php
    'key' => env('APP_KEY', 'SomeRandomString'),
    'cipher' => 'AES-256-CBC',
```

If you're using `AES-256-CBC` as the cipher for encrypting data, use the built in command to generate
your application key if you haven't already with `php artisan key:generate`.  If you are encrypting longer
data, you may want to consider the `AES-256-CBC-HMAC-SHA1` cipher.

The IV for encryption is randomly generated and cannot be set.

## Unit Tests

This package has aggressive unit tests built with the wonderful [orchestral/testbench](https://github.com/orchestral/testbench)
package which is built on top of PHPUnit. A MySQL server required for execution of unit tests.

There are [code coverage reports for `laravel-database-encryption`](https://austinheap.github.io/laravel-database-encryption/coverage/)
available online.

## Overrides

The following Laravel 5.5 methods from Eloquent are affected by this trait.

* `constructor()` -- calls `fill()`.
* `fill()` -- calls `setAttribute()` which has been extended to encrypt the data.
* `hydrate()` -- TBD.
* `create()` -- calls `constructor()` and hence `fill()`.
* `firstOrCreate()` -- calls `constructor()`.
* `firstOrNew()` -- calls `constructor()`.
* `updateOrCreate()` -- calls `fill()`.
* `update()` -- calls `fill()`.
* `toArray()` -- calls `attributesToArray()`.
* `jsonSerialize()` -- calls `toArray()`.
* `toJson()` -- calls `toArray()`.
* `attributesToArray()` -- calls `getArrayableAttributes()`.
* `getAttribute()` -- calls `getAttributeValue()`.
* `getAttributeValue()` -- calls `getAttributeFromArray()`.
* `getAttributeFromArray()` -- calls `getArrayableAttributes()`.
* `getArrayableAttributes()` -- extended to decrypt data.
* `setAttribute()` -- extended to encrypt data.
* `getAttributes()` -- extended to decrypt data.
* `castAttribute()` -- extended to cast encrypted data.
* `isDirty()` -- extended to recognize encrypted data.

## FAQ

### Can I manually encrypt or decrypt arbitrary data?

Yes! You can manually encrypt or decrypt data using the `encryptedAttribute()` and `decryptedAttribute()`
functions. For example:

```php
    $user = new User();
    $encryptedEmail = $user->encryptedAttribute(Input::get('email'));
```

### Can I search encrypted data?

No! You will not be able to search on attributes which are encrypted by this package because...it is encrypted.
Comparing encrypted values would require a fixed IV, which introduces security issues.

If you need to search on data then either:

* Leave it unencrypted, or
* Hash the data and search on the hash instead of the encrypted value using a well known hash algorithm
  such as `SHA256`.

You could store both a hashed and an encrypted value, using the hashed value for searching and retrieve
the encrypted value as needed.

### Can I encrypt all my `User` model data?

No! The same issue with searching also applies to authentication because authentication requires search.

### Is this package compatible with [`elocryptfive`](https://github.com/delatbabel/elocryptfive) out-of-the-box?

No! While it _is_ a (more modern) replacement, it is not compatible directly out of the box. To migrate to this package from elocryptfive, you must:

1. Decrypt all the data in your database encrypted by elocryptfive.
2. Remove any calls to elocryptfive from your models/code.
3. Remove elocryptfive from your `composer.json` and run `composer update`.
4. At this point you should have no encrypted data in your database and all calls/references, but make sure elocryptfive is completely purged.
5. Follow the installation instructions above.
6. ???
7. Profit!

A pull request for automated migrations is more than welcome but is currently out of the scope of this project's goals.

### Is this package compatible with `insert-random-Eloquent-package-here`?

Probably not! It's not feasible to guarantee interoperability between random packages out there, especially packages that also heavily modify Eloquent's default behavior.

Issues and pull requests regarding interoperability will not be accepted.

## Implementations

The following decently-trafficed sites use this package in production:

- [securitytext.org - `security.txt` document registry and whois server](https://securitytext.org/)

## Credits

This is a fork of [delatbabel/elocryptfive](https://github.com/delatbabel/elocryptfive),
which was a fork of [dtisgodsson/elocrypt](https://github.com/dtisgodsson/elocrypt),
which was based on earlier work.

- [delatbabel/elocryptfive Contributors](https://github.com/delatbabel/elocryptfive/graphs/contributors)
- [dtisgodsson/elocrypt Contributors](https://github.com/dtisgodsson/elocrypt/graphs/contributors)

## Contributing

[Pull requests](https://github.com/austinheap/laravel-database-encryption/pulls) welcome! Please see
[the contributing guide](CONTRIBUTING.md) for more information.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
