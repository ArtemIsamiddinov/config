<?php

declare(strict_types=1);

namespace Demai\Config\Repository;

use Demai\Config\ConfigInterface;
use Demai\Config\DI\ConfigContainer;

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
     * @param ConfigContainer $storage Контейнер конфигов.
     */
    public function __construct(protected ConfigContainer $storage)
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
        $id = is_string($config) ? $config : $config::class;

        return $this->storage->get($id);
    }
}
