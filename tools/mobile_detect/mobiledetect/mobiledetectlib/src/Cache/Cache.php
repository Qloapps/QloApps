<?php

namespace Detection\Cache;

use DateTime;
use Psr\SimpleCache\CacheInterface;

/**
 * Generic naive implementation of a Simple Cache system using an associative array.
 * The cache items are PSR-6 compatible.
 */
class Cache implements CacheInterface
{
    /**
     * @var array|array{cache_key:string, cache_value:CacheItem} $cache_db
     */
    protected array $cache_db = [];

    public function count(): int
    {
        return count($this->cache_db);
    }

    /**
     * @return array{string}
     */
    public function getKeys(): array
    {
        return array_keys($this->cache_db);
    }

    /**
     * @throws CacheException
     */
    public function get(string $key, mixed $default = null): CacheItem|null
    {
        if (empty($key)) {
            throw new CacheException('Invalid cache key');
        }

        return $this->cache_db[$key] ?? null;
    }

    /**
     * @throws CacheException
     */
    public function set(string $key, mixed $value, \DateInterval|int|null $ttl = null): bool
    {
        if (empty($key)) {
            throw new CacheException('Invalid cache key');
        }
        $item = new CacheItem($key, $value);
        $item->expiresAfter($ttl);
        $this->cache_db[$key] = $item;
        return true;
    }

    public function delete(string $key): bool
    {
        unset($this->cache_db[$key]);
        return true;
    }

    public function clear(): bool
    {
        $this->cache_db = [];
        return true;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        return array_reduce((array)$keys, function ($result, $key) {
            $result[$key] = $this->get($key);
            return $result;
        }, []);
    }

    /**
     * @param array<array{key:string, value:string}> $values
     * @param \DateInterval|int|null $ttl
     * @return bool
     * @throws CacheException
     */
    public function setMultiple(iterable $values, \DateInterval|int|null $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }
        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            unset($this->cache_db[$key]);
        }
        return true;
    }

    public function has(string $key): bool
    {
        return isset($this->cache_db[$key]);
    }
}
