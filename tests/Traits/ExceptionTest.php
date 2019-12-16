<?php
/**
 * tests/Traits/ExceptionTest.php
 *
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.3.0
 */

namespace AustinHeap\Database\Encryption\Tests\Traits;

use AustinHeap\Database\Encryption\Tests\TestCase;
use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;

/**
 * ExceptionTest
 */
class ExceptionTest extends TestCase
{
    public function testDoEncryptAttribute()
    {
        $command = new class()
        {
            use HasEncryptedAttributes;

            protected $attributes = ['testing-attribute' => 123456789];

            public function encryptedAttribute($value): ?string
            {
                throw new EncryptException('testing value: ' . $value);
            }

            protected function shouldEncrypt($key): bool
            {
                return true;
            }

            protected function isEncrypted($key): bool
            {
                return false;
            }

            public function doEncryptAttributePublic($key)
            {
                return $this->doEncryptAttribute($key);
            }
        };

        $command->doEncryptAttributePublic('testing-attribute');

        $this->assertNotNull($command->getLastEncryptionException());
        $this->assertSame(EncryptException::class, get_class($command->getLastEncryptionException()));
    }

    public function testDoDecryptAttribute()
    {
        $command = new class()
        {
            use HasEncryptedAttributes;

            public function decryptedAttribute($value): ?string
            {
                throw new DecryptException('testing value: ' . $value);
            }

            protected function shouldEncrypt($key): bool
            {
                return true;
            }

            protected function isEncrypted($key): bool
            {
                return true;
            }

            public function doDecryptAttributePublic($key, $value)
            {
                return $this->doDecryptAttribute($key, $value);
            }
        };

        $command->doDecryptAttributePublic($this->newRandom('key-%s'), $this->newRandom('value-%s'));

        $this->assertNotNull($command->getLastEncryptionException());
        $this->assertSame(DecryptException::class, get_class($command->getLastEncryptionException()));
    }
}
