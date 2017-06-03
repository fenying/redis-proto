<?php
/**
 * File: lib/TBaseCommands.php
 * Created Date: 2017-06-03 13:00:53
 */
declare (strict_types = 1);

namespace RedisProto;

trait TBaseCommands
{
    /**
     * Get the value of specific key.
     * @param string $key
     * @return string|null
     */
    public function get(string $key)
    {
        return $this->sendCommand('GET', $key);
    }

    /**
     * Returns the string representation of the type of the value stored
     * at key. 
     * @param string $key
     * @return string
     */
    public function type(string $key): string
    {
        return $this->sendCommand('TYPE', $key);
    }

    /**
     * Check if a key exists.
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        return $this->sendCommand('EXISTS', $key) === 1;
    }

    /**
     * Add or overwrite a key with new value.
     * @param string $key
     * @param mixed $val
     * @return bool
     */
    public function set(string $key, $val): bool
    {
        return $this->sendCommand('SET', $key, $val) === 'OK';
    }

    /**
     * Renames key to newkey if newkey does not yet exist.
     * @param string $key
     * @param string $val
     * @return bool
     */
    public function renameNx(string $key, string $newKey): bool
    {
        try {

            return $this->sendCommand('RENAMENX', $key, $newKey) === 1;
        }
        catch (Exception $e) {

            return false;
        }
    }

    /**
     * Renames key to newkey.
     * @param string $key
     * @param string $val
     * @return bool
     */
    public function rename(string $key, string $newKey): bool
    {
        try {

            $this->sendCommand('RENAME', $key, $newKey);
        }
        catch (Exception $e) {

            return false;
        }

        return true;
    }

    /**
     * Set the value of a non-existent key.
     * @param string $key
     * @param unknown $val
     * @return bool
     */
    public function setNx(string $key, $val): bool
    {
        return $this->sendCommand('SETNX', $key, $val) === 1;
    }

    /**
     * Add or overwrite the value of a key, and let it expires in N seconds.
     * @param string $key
     * @param unknown $val
     * @return bool
     */
    public function setEx(string $key, $val, int $ttl): bool
    {
        return $this->sendCommand('SETEX', $key, $val, $ttl) === 1;
    }

    /**
     * Removes the specified keys.
     * 
     * A key is ignored if it does not exist.
     * 
     * @param string $keys
     * @return bool
     */
    public function del(string ...$keys): bool
    {
        return $this->sendCommand('DEL', ...$keys) === 1;
    }

    /**
     * Removes the specified keys (synchronous).
     * 
     * A key is ignored if it does not exist.
     * 
     * @param string $key
     * @return bool
     */
    public function unlink(string ...$key): bool
    {
        return $this->sendCommand('UNLINK', ...$key) === 1;
    }

    /**
     * Return a random key from the currently selected database.
     * @param string $key
     * @return bool
     */
    public function randomKey()
    {
        return $this->sendCommand('RANDOMKEY');
    }

    /**
     * Returns all keys matching pattern.
     * @param string $key
     * @return int
     */
    public function keys(string $pattern): array
    {
        return $this->sendCommand('KEYS', $pattern);
    }

    /**
     * Tell how many seconds before the specific key expires.
     * @param string $key
     * @return int
     */
    public function ttl(string $key): int
    {
        return $this->sendCommand('TTL', $key);
    }

    /**
     * Tell how many milliseconds before the specific key expires.
     * @param string $key
     * @return int
     */
    public function pttl(string $key): int
    {
        return $this->sendCommand('PTTL', $key);
    }

    /**
     * Make a key never expire.
     * @param string $key
     * @return int
     */
    public function persist(string $key): int
    {
        return $this->sendCommand('PERSIST', $key);
    }

    /**
     * Let a key expire in N seconds.
     * @param string $key
     * @param int $ttl
     * @return bool
     */
    public function expire(string $key, int $ttl): bool
    {
        return $this->sendCommand('EXPIRE', $key, $ttl) === 1;
    }

    /**
     * Let a key expire at specific time (unit: seconds).
     * @param string $key
     * @param int $timeStamp
     * @return bool
     */
    public function expireAt(string $key, int $timeStamp): bool
    {
        return $this->sendCommand('EXPIREAT', $key, $timeStamp) === 1;
    }

    /**
     * Let a key expire in N milliseconds.
     * @param string $key
     * @param int $ttl
     * @return bool
     */
    public function pexpire(string $key, int $ttl): bool
    {
        return $this->sendCommand('PEXPIRE', $key, $ttl) === 1;
    }

    /**
     * Let a key expire at specific time (unit: milliseconds).
     * @param string $key
     * @param int $timeStamp
     * @return bool
     */
    public function pexpireAt(string $key, int $timeStamp): bool
    {
        return $this->sendCommand('PEXPIREAT', $key, $timeStamp) === 1;
    }

    /**
     * Select the DB with having the specified zero-based numeric index.
     * 
     * New connections always use DB 0.
     * 
     * @param int $db
     * @return bool
     */
    public function select(int $db): bool
    {
        try {

            $this->sendCommand('SELECT', $db);
        }
        catch (Exception $e) {

            return false;
        }

        return true;
    }

    /**
     * Request for authentication in a password-protected Redis server.
     * 
     * @param string $passwd
     * @return bool
     */
    public function auth(string $passwd): bool
    {
        try {

            return $this->sendCommand('AUTH', $passwd) === 'OK';
        }
        catch (Exception $e) {

            return false;
        }
    }

    /**
     * Returns message.
     * 
     * @param string $msg
     * @return string
     */
    public function echo(string $msg): string
    {
        return $this->sendCommand('ECHO', $msg);
    }

    /**
     * Returns PONG if no argument is provided, otherwise return a copy
     * of the argument as a bulk.
     * 
     * @param string $msg
     * @return string
     */
    public function ping(string $msg = null): string
    {
        if (isset($msg)) {

            return $this->sendCommand('PING', $msg);
        }

        return $this->sendCommand('PING');
    }
}