<?php

namespace Detection\Cache;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Psr\Cache\CacheItemInterface;

/**
 * Simple cache item (key, value, ttl) that is being
 * used by all the detection methods of Mobile Detect class.
 */
class CacheItem implements CacheItemInterface
{
    /**
     * @var string Unique key for the cache record.
     */
    protected string $key;
    /**
     * @var bool|null Mobile Detect only needs to store booleans (e.g. "isMobile" => true)
     */
    protected bool|null $value = null;
    /**
     * @var DateTimeInterface|null
     */
    public DateTimeInterface|null $expiresAt = null;
    /**
     * @var DateInterval|null
     */
    public DateInterval|null $expiresAfter = null;

    public function __construct($key, $value = null)
    {
        $this->key = $key;
        if (!is_null($value)) {
            $this->value = $value;
        }
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return bool|null
     */
    public function get(): ?bool
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isHit(): bool
    {
        // Item never expires.
        if ($this->expiresAt === null && $this->expiresAfter === null) {
            return true;
        }

        if (!is_null($this->expiresAt) && $this->expiresAt > new DateTime()) {
            return true;
        }

        if (!is_null($this->expiresAfter)) {
            try {
                $future_date = (new DateTime())->add($this->expiresAfter);
            } catch (\Exception $e) {
                return false;
            }

            if ($future_date > new DateTime()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function set(mixed $value): static
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param \DateTimeInterface|null $expiration
     * @return $this
     */
    public function expiresAt(?\DateTimeInterface $expiration): static
    {
        $this->expiresAt = $expiration instanceof \DateTime ? $expiration : null;

        return $this;
    }

    /**
     * @param int|\DateInterval|null $time
     * @return $this
     */
    public function expiresAfter(\DateInterval|int|null $time): static
    {
        $expiresAfter = null;

        if ($time instanceof \DateInterval) {
            $expiresAfter = $time;
        } elseif (is_int($time)) {
            if ($time > 0) {
                $expiresAfter = new \DateInterval("PT{$time}S");
            }
        }

        $this->expiresAfter = $expiresAfter;

        return $this;
    }
}
