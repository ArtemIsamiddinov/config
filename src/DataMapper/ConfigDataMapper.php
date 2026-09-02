<?php

declare(strict_types=1);

namespace Demai\Config\DataMapper;

use Demai\Config\ConfigInterface;
use Demai\Config\Service\ConfigKeyService;
use ReflectionClass;

class ConfigDataMapper
{
    public static function mapConfigToArray(ConfigInterface $config, array $keys): array
    {
        $mapConfig = function (ConfigInterface $cfg) use(&$mapConfig, $keys): array
        {
            $result = [];
            
            foreach ($keys as $key) {
                $value = $cfg->get($key);
                if ($value instanceof ConfigInterface) {
                    $result[$key] = $value->toArray();
                }
                else {
                    $result[$key] = $value;
                }
            }

            return $result;
        };

        return $mapConfig($config);
    }
}
