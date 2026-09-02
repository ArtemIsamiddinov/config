<?php

declare(strict_types=1);

namespace Demai\Config\Service;

use Demai\Config\ConfigInterface;
use ReflectionProperty;
use Demai\Config\Exception\ParameterNotFoundException;
use Demai\Config\Exception\IncorrectParameterNameException;

/**
 * Сервис для работы с ключами доступа.
 * Сервис выполняет работу с ключами для доступа к свойствам конфига.
 *
 * @package Demai\Config
 * @author Artem Isamiddinov <artemisamiddinov@gmail.com>
 * @version 1.0.0
 */
class PropertyAccessService
{
    /**
     * @var string Строка-сепаратор для разделения вложенности.
     */
    public readonly string $waySeparator;

    /**
     * @param ConfigInterface $config Конфиг, с которым будет работать сервис.
     */
    public function __construct(protected ConfigInterface $config)
    {
        $this->waySeparator = '.';
    }

    /**
     * Проверить, является ли имя свойства вложенным.
     *
     * @param string $name Имя свойства.
     * @return bool True для вложенного типа свойства вида «name.other», false для обычного имени.
     */
    public function nameIsWay(string $name): bool
    {
        return count($this->getWay($name)) > 1;
    }

    /**
     * Получить путь из имени свойства.
     *
     * @param string $name Имя свойства.
     * @return string[] Массив строк, определяющий путь до конечного свойства.
     */
    public function getWay(string $name): array
    {
        return explode($this->waySeparator, $name);
    }

    /**
     * Получить имя из пути вложенности.
     *
     * @param string[] $way Массив пути вложенности.
     * @return string Имя свойства, соединенное разделителем.
     */
    public function getName(array $way): string
    {
        return implode($this->waySeparator, $way);
    }

    /**
     * Есть ли свойство в конфиге.
     *
     * @param string $name Имя свойства.
     * @return bool Если в конфиге есть указанное свойство, будет возвращено true, иначе будет возвращено false.
     */
    public function has(string $name): bool
    {
        return property_exists($this->config, $name);
    }

    /**
     * Инициализировано ли свойство.
     *
     * @param string $name Имя свойства.
     * @return bool Для инициализированного свойства будет возвращено true, если не инициализировано, то false.
     */
    public function isInitialized(string $name): bool
    {
        return (new ReflectionProperty($this->config, $name))->isInitialized($this->config);
    }

    /**
     * Получить значение, если свойство является конфигом.
     *
     * @param string $name Имя свойства.
     * @return mixed Свойство или вложенный конфиг.
     */
    public function getValueForConfig(string $name): mixed
    {
        $way = $this->getWay($name);
        $key = array_shift($way);
        $way = $this->getName($way);

        if ($this->has($key)) {
            $value = $this->config->get($key);
            if ($value instanceof ConfigInterface) {
                return $value->get($way);
            }

            throw new IncorrectParameterNameException("Parameter {$key} for {$way} has incorrect type");
        }

        throw new ParameterNotFoundException("Parameter {$key} for {$way} not found");
    }
}
