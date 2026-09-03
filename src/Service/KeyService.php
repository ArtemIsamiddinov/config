<?php

declare(strict_types=1);

namespace Demai\Config\Service;

use Demai\Config\ConfigInterface;
use ReflectionClass;

/**
 * Сервис для работы с ключами.
 * Сервис получает ключи конфига и преобразовывает их в необходимый формат.
 *
 * @package Demai\Config
 * @author Artem Isamiddinov <artemisamiddinov@gmail.com>
 * @version 1.0.0
 */
class KeyService
{
    /**
     * Получить ключи конфига.
     *
     * @param ConfigInterface $config Конфиг, у которого будут запрошены ключи.
     * @return array Массив ключей конфига.
     */
    public function getKeys(ConfigInterface $config): array
    {
        $keys = [];
        $skipKeys = $config->getSkipKeys();
        foreach ((new ReflectionClass($config))->getProperties() as $prop) {
            if (in_array($prop->getName(), $skipKeys)) {
                continue;
            }

            $keys[] = $prop->getName();
        }

        return $keys;
    }

    /**
     * Получить корректный ключ.
     * Преобразует строку в формат camelCase для обращения к свойствам конфига.
     *
     * @param string $key Ключ, для которого будет получен корректный вид.
     * @return string Преобразованный ключ.
     */
    public function getCorrect(string $key): string
    {
        $key = strtolower(trim($key));

        return $this->replaceSpecChars($key);
    }

    /**
     * Заменить специальные символы в ключе.
     *
     * @param string $key Ключ, для которого будет выполнена замена.
     * @return string Преобразованный ключ.
     */
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
