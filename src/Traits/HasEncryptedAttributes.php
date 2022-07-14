<?php
/**
 * src/Traits/HasEncryptedAttributes.php.
 *
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.3.0
 */
declare(strict_types=1);

namespace AustinHeap\Database\Encryption\Traits;

use AustinHeap\Database\Encryption\EncryptionFacade as DatabaseEncryption;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

/**
 * HasEncryptedAttributes.
 *
 * Automatically encrypt and decrypt Laravel 5.5+ Eloquent values
 *
 * ### Example
 *
 * <code>
 *   use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;
 *
 *   class User extends Eloquent {
 *
 *       use HasEncryptedAttributes;
 *
 *       protected $encrypted = [
 *           'address_line_1', 'first_name', 'last_name', 'postcode'
 *       ];
 *   }
 * </code>
 *
 * ### Summary of Methods in Illuminate\Database\Eloquent\Model
 *
 * This surveys the major methods in the Laravel Model class as of
 * Laravel v5.5 and checks to see how those models set attributes
 * and hence how they are affected by this trait.
 *
 * - __construct -- calls fill()
 * - fill() -- calls setAttribute() which has been overridden.
 * - hydrate() -- TBD
 * - create() -- calls constructor and hence fill()
 * - firstOrCreate -- calls constructor
 * - firstOrNew -- calls constructor
 * - updateOrCreate -- calls fill()
 * - update() -- calls fill()
 * - toArray() -- calls attributesToArray()
 * - jsonSerialize() -- calls toArray()
 * - toJson() -- calls toArray()
 * - attributesToArray() -- has been over-ridden here.
 * - getAttribute -- calls getAttributeValue()
 * - getAttributeValue -- calls getAttributeFromArray()
 * - getAttributeFromArray -- calls getArrayableAttributes
 * - getArrayableAttributes -- has been over-ridden here.
 * - setAttribute -- has been over-ridden here.
 * - getAttributes -- has been over-ridden here.
 *
 * @see         \Illuminate\Support\Facades\Crypt
 * @see         \Illuminate\Contracts\Encryption\Encrypter
 * @see         \Illuminate\Encryption\Encrypter
 * @link        http://laravel.com/docs/5.5/eloquent
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @link        https://packagist.org/packages/austinheap/laravel-database-encryption
 * @link        https://austinheap.github.io/laravel-database-encryption/classes/AustinHeap.Database.Encryption.EncryptionServiceProvider.html
 */
trait HasEncryptedAttributes
{
    /**
     * Private copy of last Encryption exception to occur.
     *
     * @var null|EncryptException|DecryptException
     */
    private $lastEncryptionException = null;

    /**
     * Get the last encryption-related exception to occur, if any.
     *
     * @return null|EncryptException|DecryptException
     */
    public function getLastEncryptionException()
    {
        return $this->lastEncryptionException;
    }

    /**
     * Set the last encryption-related exception to occur, if any.
     *
     * @param null|EncryptException|DecryptException $exception
     * @param null|string                            $function
     *
     * @return self
     */
    protected function setLastEncryptionException($exception, ?string $function = null): self
    {
        Log::debug('Ignored exception "'.get_class($exception).'" in function "'.(is_null($function) ? '(unknown)' : $function).'": '.$exception->getMessage());

        $this->lastEncryptionException = $exception;

        return $this;
    }

    /**
     * Get the configuration setting for the prefix used to determine if a string is encrypted.
     *
     * @return string
     */
    protected function getEncryptionPrefix(): string
    {
        return DatabaseEncryption::getHeaderPrefix();
    }

    /**
     * Determine whether an attribute should be encrypted.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function shouldEncrypt($key): bool
    {
        $encrypt = DatabaseEncryption::isEnabled() && isset($this->encrypted) && is_array($this->encrypted) ? $this->encrypted : [];

        return in_array($key, $encrypt, true);
    }

    /**
     * Determine whether a model is ready for encryption.
     *
     * @return bool
     */
    protected function isEncryptable(): bool
    {
        $exists = property_exists($this, 'exists');

        return $exists === false || ($exists === true && $this->exists === true);
    }

    /**
     * Determine whether a string has already been encrypted.
     *
     * @param mixed $value
     *
     * @return bool
     */
    protected function isEncrypted($value): bool
    {
        return strpos((string) $value, $this->getEncryptionPrefix()) === 0;
    }

    /**
     * Return the encrypted value of an attribute's value.
     *
     * This has been exposed as a public method because it is of some
     * use when searching.
     *
     * @param string $value
     *
     * @return null|string
     */
    public function encryptedAttribute($value): ?string
    {
        return DatabaseEncryption::buildHeader($value).Crypt::encrypt($value);
    }

    /**
     * Return the decrypted value of an attribute's encrypted value.
     *
     * This has been exposed as a public method because it is of some
     * use when searching.
     *
     * @param string $value
     *
     * @return null|mixed
     * @throws \Throwable
     */
    public function decryptedAttribute($value)
    {
        $characters = DatabaseEncryption::getControlCharacters('header');

        throw_if(! array_key_exists('stop', $characters), DecryptException::class, 'Cannot decrypt model attribute not originally encrypted by this package!');

        $offset = strpos($value, $characters['stop']['string']);

        throw_if($offset === false, DecryptException::class, 'Cannot decrypt model attribute with no package header!');

        $value = substr($value, $offset);

        return Crypt::decrypt($value);
    }

    /**
     * Encrypt a stored attribute.
     *
     * @param string $key
     *
     * @return self
     */
    protected function doEncryptAttribute($key): self
    {
        if ($this->shouldEncrypt($key) && ! $this->isEncrypted($this->attributes[$key])) {
            try {
                $this->attributes[$key] = $this->encryptedAttribute($this->attributes[$key]);
            } catch (EncryptException $exception) {
                $this->setLastEncryptionException($exception, __FUNCTION__);
            }
        }

        return $this;
    }

    /**
     * Decrypt an attribute if required.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function doDecryptAttribute($key, $value)
    {
        if ($this->shouldEncrypt($key) && $this->isEncrypted($value)) {
            try {
                return $this->decryptedAttribute($value);
            } catch (DecryptException $exception) {
                $this->setLastEncryptionException($exception, __FUNCTION__);
            }
        }

        return $value;
    }

    /**
     * Decrypt each attribute in the array as required.
     *
     * @param array $attributes
     *
     * @return array
     */
    public function doDecryptAttributes($attributes)
    {
        foreach ($attributes as $key => $value) {
            $attributes[$key] = $this->doDecryptAttribute($key, $value);
        }

        return $attributes;
    }

    //
    // Methods below here override methods within the base Laravel/Illuminate/Eloquent
    // model class and may need adjusting for later releases of Laravel.
    //

    /**
     * Decrypt encrypted data before it is processed by cast attribute.
     *
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    protected function castAttribute($key, $value)
    {
        return parent::castAttribute($key, $this->doDecryptAttribute($key, $value));
    }

    /**
     * Get the attributes that have been changed since last sync.
     *
     * @return array
     */
    public function getDirty()
    {
        $dirty = [];

        foreach ($this->attributes as $key => $value) {
            if (! $this->originalIsEquivalent($key, $value)) {
                $dirty[$key] = $value;
            }
        }

        return $dirty;
    }

    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function setAttribute($key, $value)
    {
        parent::setAttribute($key, $value);

        $this->doEncryptAttribute($key);
    }

    /**
     * Get an attribute from the $attributes array.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function getAttributeFromArray($key)
    {
        return $this->doDecryptAttribute($key, parent::getAttributeFromArray($key));
    }

    /**
     * Get an attribute array of all arrayable attributes.
     *
     * @return array
     */
    protected function getArrayableAttributes()
    {
        return $this->doDecryptAttributes(parent::getArrayableAttributes());
    }

    /**
     * Get all of the current attributes on the model.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->isEncryptable() ? $this->doDecryptAttributes(parent::getAttributes()) : parent::getAttributes();
    }
}
