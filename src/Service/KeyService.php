<?php

declare(strict_types=1);

namespace Demai\Config\Service;

use Demai\Config\ConfigInterface;
use ReflectionClass;

final class KeyService
{
    public function getKeys(ConfigInterface $config, array $skipKeys = []): array
    {
        $keys = [];
        foreach ((new ReflectionClass($config))->getProperties() as $prop) {
            if (in_array($prop->getName(), $skipKeys)) {
                continue;
            }

            $keys[] = $prop->getName();
        }

        return $keys;
    }

    public function getCorrect(string $key): string
    {
        $key = strtolower(trim($key));

        return $this->replaceSpecChars($key);
    }



    public function replaceSpecChars(string $key): string
    {
        $camelCase = preg_replace_callback(
            '/[-_]([a-z0-9])/',
            fn($matches) => strtoupper($matches[1]),
            $key
        );

        return $camelCase;
    }
}
