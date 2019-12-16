<?php
/**
 * tests/Models/DummyModel.php
 *
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.3.0
 */

namespace AustinHeap\Database\Encryption\Tests\Models;

use AustinHeap\Database\Encryption\EncryptionDefaults;
use AustinHeap\Database\Encryption\EncryptionFacade;
use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;
use Illuminate\Container\Container;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Facade;

/**
 * DummyModel
 */
class DummyModel extends FakeModel
{
    use HasEncryptedAttributes;

    /** @var array list of attributes that are encrypted */
    protected $encrypted = ['encrypt_me'];

    /** @var Encrypter */
    protected $encrypter;

    public function __construct(array $attributes)
    {
        $this->encrypter = new Encrypter('088409730f085dd15e8e3a7d429dd185', 'AES-256-CBC');

        $app = new Container();
        $app->singleton('app', 'Illuminate\Container\Container');
        $app->singleton('config', 'Illuminate\Config\Repository');
        $app['config']->set('database-encryption.prefix', EncryptionDefaults::DEFAULT_PREFIX);
        Facade::setFacadeApplication($app);

        parent::__construct($attributes);
    }

    /**
     * Return the encrypted value of an attribute's value.
     *
     * This has been exposed as a public method because it is of some
     * use when searching.
     *
     * @param string $value
     *
     * @return string
     */
    public function encryptedAttribute($value)
    {
        return EncryptionFacade::getInstance()->buildHeader($value) . $this->encrypter->encrypt($value);
    }

    /**
     * Return the decrypted value of an attribute's encrypted value.
     *
     * This has been exposed as a public method because it is of some
     * use when searching.
     *
     * @param string $value
     *
     * @return string
     */
    public function decryptedAttribute($value)
    {
        return $this->encrypter->decrypt(str_replace(EncryptionFacade::getInstance()->buildHeader($value), '', $value));
    }
}
