<?php

declare(strict_types=1);

namespace Demai\Config\Facade;

use Demai\Config\ConfigInterface;
use Demai\Config\Repository\ConfigRepository;
use Demai\Config\Service\ReadService;
use Demai\Config\Service\KeyService;

class Config
{
    public static function get(string $config): ?ConfigInterface
    {
        $readService = new ReadService(new KeyService(), ['reader', 'initializationService']);
        return (new ConfigRepository($readService))->get($config);
    }
}
