<?php

declare(strict_types=1);

namespace GeoIp2\Record;

abstract class AbstractNamedRecord implements \JsonSerializable
{
    /**
     * @var string|null The name based on the locales list
     *                  passed to the constructor. This attribute is returned by all location
     *                  services and databases.
     */
    public readonly ?string $name;

    /**
     * @var array<string, string> An array map where the keys are locale codes
     *                            and the values are names. This attribute is returned by all location
     *                            services and databases.
     */
    public readonly array $names;

    /**
     * @ignore
     *
     * @param array<string, mixed> $record
     * @param list<string>         $locales
     */
    public function __construct(array $record, array $locales = ['en'])
    {
        $this->names = $record['names'] ?? [];

        foreach ($locales as $locale) {
            if (isset($this->names[$locale])) {
                $this->name = $this->names[$locale];

                return;
            }
        }
        $this->name = null;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $js = [];
        if (!empty($this->names)) {
            $js['names'] = $this->names;
        }

        return $js;
    }
}
