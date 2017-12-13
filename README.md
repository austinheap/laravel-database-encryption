# Laravel 5.5+ Database Encryption Package

![laravel-database-encryption banner from the documentation](docs/img/banner-1544x500.png?raw=true)

[![License](https://img.shields.io/packagist/l/austinheap/laravel-database-encryption.svg)](https://github.com/austinheap/laravel-database-encryption/blob/master/LICENSE.md)
[![Current Release](https://img.shields.io/github/release/austinheap/laravel-database-encryption.svg)](https://github.com/austinheap/laravel-database-encryption/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/austinheap/laravel-database-encryption.svg)](https://packagist.org/packages/austinheap/laravel-database-encryption)
[![Build Status](https://travis-ci.org/austinheap/laravel-database-encryption.svg?branch=master)](https://travis-ci.org/austinheap/laravel-database-encryption)
[![Dependency Status](https://gemnasium.com/badges/github.com/austinheap/laravel-database-encryption.svg)](https://gemnasium.com/github.com/austinheap/laravel-database-encryption)
[![Scrutinizer CI](https://scrutinizer-ci.com/g/austinheap/laravel-database-encryption/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/austinheap/laravel-database-encryption/)
[![StyleCI](https://styleci.io/repos/113929569/shield?branch=master)](https://styleci.io/repos/113929569)
[![Maintainability](https://api.codeclimate.com/v1/badges/a63deda99383852c739b/maintainability)](https://codeclimate.com/github/austinheap/laravel-database-encryption/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/a63deda99383852c739b/test_coverage)](https://codeclimate.com/github/austinheap/laravel-database-encryption/test_coverage)
[![SensioLabs](https://insight.sensiolabs.com/projects/68d37423-9a70-4f84-bfd8-b4e88ac94c1d/mini.png)](https://insight.sensiolabs.com/projects/68d37423-9a70-4f84-bfd8-b4e88ac94c1d)

## A package for automatically encrypting and decrypting Eloquent attributes in Laravel 5.5+, based on configuration settings.

The purpose of this project is to create a set-it-and-forget-it package that can be
installed without much effort to encrypt and decrypt Eloquent model attributes stored
in your database tables, transparently. It is therefore highly opinionated but built
for configuration.

When enabled, it automagically begins encrypting data as it is stored in the model
attributes and decrypting data as it is recalled from the model attributes.

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

## Requirements

* Laravel 5.5+
* PHP >= 7.1.0
* PHP [OpenSSL extension](http://php.net/manual/en/book.openssl.php)

## Schemas

Encrypted values are usually longer than plain text values, sometimes much longer.
You may find that the column widths in your database tables need to be altered to
store the encrypted values generated by this package.

If you are encrypting long strings such as JSON blobs then the encrypted values may
be longer than a `VARCHAR` field can support, and you will need to alter your column
types to `TEXT` or `LONGTEXT`.

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

### Step 2: Enable the package (Optional)

This package implements Laravel 5.5's auto-discovery feature. After you install it the
package provider and facade are added automatically.

If you would like to declare the provider and/or alias explicitly, then add the service
provider to your `config/app.php`:

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

### Step 3: Configure the package

Publish the package config file:

```bash
$ php artisan vendor:publish --provider="AustinHeap\Database\Encryption\EncryptionServiceProvider"
```

You may now enable automagic encryption and decryption of Eloquent models by editing the
`config/database-encryption.php` file:

```php
return [
    'enabled' => env('DATABASE_ENCRYPTION_ENABLED', true),
];
```

Or simply setting the the `DATABASE_ENCRYPTION_ENABLED` environment variable to true, via
the Laravel `.env` file or hosting environment.

```bash
DATABASE_ENCRYPTION_ENABLED=true
```

## Usage

Use the `HasEncryptedAttributes` trait in any Eloquent model that you wish to apply encryption
to and define a `protected $encrypted` array containing a list of the attributes to encrypt.

For example:

```php
    use AustinHeap\Database\Encryption;

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

You can combine `$casts` and `$encrypts` to store encrypted arrays. An array will first be
converted to JSON and then encrypted.

For example:

```php
    use AustinHeap\Database\Encryption;

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
