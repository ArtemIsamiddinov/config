<?php

declare(strict_types=1);

namespace Demai\Config\DataMapper;

use Demai\Config\ConfigInterface;

/**
 * Класс для преобразования данных конфигов
 * Позволяет преобразовывать конфиги в другой тип данных.
 *
 * @package Demai\Config
 * @author Artem Isamiddinov <artemisamiddinov@gmail.com>
 * @version 1.0.0
 */
class ConfigDataMapper
{
    /**
     * Преобразовать конфиг в ассоциативный массив.
     *
     * @param ConfigInterface $config Конфиг, который необходимо преобразовать.
     * @param array $keys Массив ключей по которым необходимо выполнить преобразование.
     * @return array Массив данных конфига и вложенных конфигов, если есть.
     */
    public static function mapConfigToArray(ConfigInterface $config, array $keys): array
    {
        $result = [];

        foreach ($keys as $key) {
            $value = $config->get($key);
            if ($value instanceof ConfigInterface) {
                $result[$key] = $value->toArray();
                continue;
            }

            $result[$key] = $value;
        }

        return $result;
    }
}
