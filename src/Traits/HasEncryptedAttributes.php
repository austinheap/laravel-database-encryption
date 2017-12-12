<?php
/**
 * src/Traits/HasEncryptedAttributes.php.
 *
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.0.1
 */
declare(strict_types=1);

namespace AustinHeap\Database\Encryption\Traits;

use Log;
use Crypt;
use AustinHeap\Database\Encryption\EncryptionFacade;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;

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
 * @see         Illuminate\Support\Facades\Crypt
 * @see         Illuminate\Contracts\Encryption\Encrypter
 * @see         Illuminate\Encryption\Encrypter
 * @link        http://laravel.com/docs/5.5/eloquent
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @link        https://packagist.org/packages/austinheap/laravel-database-encryption
 * @link        https://austinheap.github.io/laravel-database-encryption/classes/AustinHeap.Database.Encryption.EncryptionServiceProvider.html
 */
trait HasEncryptedAttributes
{
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
     * Get the configuration setting for the prefix used to determine if a string is encrypted.
     *
     * @return string
     */
    protected function getEncryptionPrefix(): string
    {
        return EncryptionFacade::getInstance()->getHeaderPrefix();
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
        $encrypt = isset($this->encrypted) ? $this->encrypted : [];

        return in_array($key, $encrypt);
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
        return EncryptionFacade::getInstance()->buildHeader($value).Crypt::encrypt($value);
    }

    /**
     * Return the decrypted value of an attribute's encrypted value.
     *
     * This has been exposed as a public method because it is of some
     * use when searching.
     *
     * @param string $value
     *
     * @return null|string
     */
    public function decryptedAttribute($value): ?string
    {
        $characters = EncryptionFacade::getInstance()->getControlCharacters('header');
        $value = substr($value, strpos($value, $characters['stop']['string']));

        return Crypt::decrypt($value);
    }

    /**
     * Encrypt a stored attribute.
     *
     * @param string $key
     *
     * @return void
     */
    protected function doEncryptAttribute($key): void
    {
        if ($this->shouldEncrypt($key) && ! $this->isEncrypted($this->attributes[$key])) {
            try {
                $this->attributes[$key] = $this->encryptedAttribute($this->attributes[$key]);
            } catch (EncryptException $exception) {
                Log::debug('Ignored exception "'.EncryptException::class.'": '.$exception->getMessage());
            }
        }
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
            } catch (DecryptException $e) {
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
        return $this->doDecryptAttributes(parent::getAttributes());
    }
}
