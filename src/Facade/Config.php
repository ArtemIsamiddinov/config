<?php

declare(strict_types=1);

namespace Demai\Config\Facade;

use Demai\Config\ConfigInterface;
use Demai\Config\Repository\ConfigRepository;
use Demai\Config\Service\ReadService;
use Demai\Config\Service\KeyService;
use Demai\Config\DI\ConfigContainer;

/**
 * Фасад для работы с конфигами.
 * Класс обеспечивает упрощенную работу с конфигами.
 *
 * @package Demai\Config
 * @author Artem Isamiddinov <artemisamiddinov@gmail.com>
 * @version 1.0.0
 */
class Config
{
    /**
     * @var null|ConfigRepository Репозиторий для работы с конфигами.
     */
    private static ?ConfigRepository $repository = null;

    /**
     * Получить конфиг по имени класса.
     *
     * @param string $config Имя класса конфига.
     * @return ConfigInterface
     */
    public static function get(string $config): ConfigInterface
    {
        if (self::$repository === null) {
            $readService = new ReadService(new KeyService());
            $container = new ConfigContainer($readService);
            self::$repository = new ConfigRepository($container);
        }

        return self::$repository->get($config);
    }
}
