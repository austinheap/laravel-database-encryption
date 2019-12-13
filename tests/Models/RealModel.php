<?php
/**
 * tests/Models/RealModel.php
 *
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.3.0
 */

namespace AustinHeap\Database\Encryption\Tests\Models;

use DB, PDO, PDOException;
use \Illuminate\Database\Eloquent\Model;

/**
 * Class RealModel
 */
abstract class RealModel extends Model
{
    public function getRawValues(array $columns = null): ?array
    {
        if (!property_exists($this, 'exists') || !is_bool($this->exists) || $this->exists !== true) {
            return null;
        } else if (!property_exists($this, 'primaryKey') || !is_string($this->primaryKey)) {
            return null;
        } else if (!array_key_exists($this->primaryKey, $this->attributes) || !is_int($this->{$this->primaryKey})) {
            return null;
        } else if (is_null($columns)) {
            $columns = ['*'];
        }

        $query = sprintf(
            'SELECT %s FROM %s WHERE %s = %u LIMIT 1',
            implode(', ', $columns),
            $this->table,
            $this->primaryKey,
            $this->{$this->primaryKey}
        );

        try {
            $dsn        = sprintf('mysql:dbname=%s;host=%s;charset=utf8', DB::getDatabaseName(), env('TESTING_DB_HOST', '127.0.0.1'));
            $connection = new PDO($dsn, env('TESTING_DB_USER', 'root'), env('TESTING_DB_PASS', ''));
            $statement  = $connection->prepare($query);

            $statement->execute();

            $result     = $statement->setFetchMode(PDO::FETCH_ASSOC);
            $rows       = is_bool($result) && $result === true ? $statement->fetchAll() : null;
        } catch (PDOException $exception) {
            return null;
        } finally {
            $connnection = null;

            unset($connection);
        }

        return count($rows) === 1 && count($rows[0]) === count($columns) ? $rows[0] : null;
    }
}
