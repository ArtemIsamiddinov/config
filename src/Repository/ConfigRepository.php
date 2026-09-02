<?php

declare(strict_types=1);

namespace Demai\Config\Repository;

use Demai\Config\ConfigInterface;
use Demai\Config\Service\ReadService;

/**
 * Класс репозиторий конфигов.
 * Репозиторий обеспечивает работу с конфигами.
 *
 * @package Demai\Config
 * @author Artem Isamiddinov <artemisamiddinov@gmail.com>
 * @version 1.0.0
 */
class ConfigRepository
{
    /**
     * @var ConfigInterface[] Массив конфигов, используемый как хранилище.
     */
    protected array $configs = [];

    /**
     * @param null|ReadService $readService Сервис чтения конфигов.
     */
    public function __construct(protected ReadService $readService)
    {
    }

    /**
     * Получить конфиг.
     *
     * @param string|ConfigInterface $config Класс или имя класса для получения конфига.
     * @return ConfigInterface Запрошенный конфиг.
     */
    public function get(string|ConfigInterface $config): ConfigInterface
    {
        $configClass = is_string($config) ? $config : $config::class;

        if (array_key_exists($configClass, $this->configs)) {
            return $this->configs[$configClass];
        }

        $configInstance = is_string($config) ? new $config() : $config;

        return $this->load($configInstance);
    }

    /**
     * Проверить существует ли конфиг.
     *
     * @param string|ConfigInterface $config Класс или имя класса конфига.
     * @return bool Существует ли конфиг.
     */
    public function has(string|ConfigInterface $config): bool
    {
        return array_key_exists(
            is_string($config) ? $config : $config::class,
            $this->configs
        );
    }

    /**
     * Загрузить конфиг.
     *
     * @param ConfigInterface $config Класс загружаемого конфига.
     * @return ConfigInterface Загруженный конфиг.
     */
    public function load(ConfigInterface $config): ConfigInterface
    {
        $this->readService->read($config);

        $this->configs[$config::class] = $config;

        return $config;
    }

    /**
     * Получить ключи хранилища конфигов.
     *
     * @return array Массив ключей хранилища.
     */
    public function getKeys(): array
    {
        return array_keys($this->configs);
    }
}
