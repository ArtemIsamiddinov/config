<?php

declare(strict_types=1);

namespace Demai\Config\DI;

use Demai\Config\ConfigInterface;
use Demai\Config\Exception\InvalidClassTypeException;
use Demai\Config\Service\ReadService;
use ReflectionClass;
use Override;

/**
 * Класс контейнер для конфигов.
 * Используется как хранилище созданных конфигов.
 *
 * @package Demai\Config
 * @author Artem Isamiddinov <artemisamiddinov@gmail.com>
 * @version 1.0.0
 */
class ConfigContainer extends Container
{
    public function __construct(protected ReadService $readService)
    {
        parent::__construct();
    }

    /**
     * @param string $id
     * @return ConfigInterface
     */
    #[Override]
    public function get(string $id): ConfigInterface
    {
        return parent::get($id);
    }

    /**
     * Получить объект конфига по имени класса конфига.
     *
     * @param string $className Имя класса конфига.
     * @return ConfigInterface Объект конфига.
     */
    #[Override]
    protected function getInstanceFromName(string $className): object
    {
        $instance = parent::getInstanceFromName($className);

        if ($instance instanceof ConfigInterface) {
            $this->readService->read($instance);
            return $instance;
        }

        throw new InvalidClassTypeException("Class must be instance of ConfigInterface.");
    }

    /**
     * Получить объект конфига по ReflectionClass класса конфига.
     *
     * @param ReflectionClass $reflection Рефлекция класса конфига.
     * @param array $dependencies Массив зависимостей конструктора конфига.
     * @return ConfigInterface Объект конфига.
     */
    #[Override]
    protected function getInstanceFromReflection(ReflectionClass $reflection, array $dependencies): object
    {
        $instance = parent::getInstanceFromReflection($reflection, $dependencies);

        if ($instance instanceof ConfigInterface) {
            $this->readService->read($instance);
            return $instance;
        }

        throw new InvalidClassTypeException("Class must be instance of ConfigInterface.");
    }
}
