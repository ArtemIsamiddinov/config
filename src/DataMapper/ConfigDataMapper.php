<?php

declare(strict_types=1);

namespace Demai\Config\DataMapper;

use Demai\Config\ConfigInterface;

class ConfigDataMapper
{
    public static function mapConfigToArray(ConfigInterface $config, array $keys): array
    {
        $result = [];

        foreach ($keys as $key) {
            $value = $config->get($key);
            if ($value instanceof ConfigInterface) {
                $result[$key] = $value->toArray();
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
