<?php

declare(strict_types=1);

namespace Demai\Config\Facade;

use Demai\Config\ConfigInterface;
use Demai\Config\Repository\ConfigRepository;

class Config
{
    public static function get(string $config): ?ConfigInterface
    {
        return (new ConfigRepository())->get($config);
    }
}
