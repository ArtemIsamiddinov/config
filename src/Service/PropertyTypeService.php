<?php

declare(strict_types=1);

namespace Demai\Config\Service;

use Demai\Config\ConfigInterface;
use ReflectionProperty;
use ReflectionNamedType;

/**
 * Сервис для определения типов свойств.
 * Сервис определяет типы свойств конфига.
 *
 * @package Demai\Config
 * @author Artem Isamiddinov <artemisamiddinov@gmail.com>
 * @version 1.0.0
 */
class PropertyTypeService
{
    /**
     * @param ConfigInterface $config Конфиг, для которого будет выполняться проверка типов.
     */
    public function __construct(protected ConfigInterface $config)
    {
    }

    /**
     * Является ли свойство конфигом.
     *
     * @param string $name Имя свойства.
     * @return bool True, если свойство является объектом конфигурации, иначе false.
     */
    public function isConfig(string $name): bool
    {
        $prop = new ReflectionProperty($this->config, $name);
        if ($prop->hasType()) {
            $type = $prop->getType();
            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                return is_subclass_of($type->getName(), ConfigInterface::class);
            }
        }
        return false;
    }

    /**
     * Получить тип builtin.
     *
     * @param string $name Имя свойства.
     * @return null|string Имя встроенного типа или null, если тип не является builtin.
     */
    public function getBuiltinType(string $name): null|string
    {
        $prop = new ReflectionProperty($this->config, $name);
        if ($prop->hasType()) {
            $type = $prop->getType();
            if ($type instanceof ReflectionNamedType && $type->isBuiltin()) {
                return $type->getName();
            }
        }
        return null;
    }
}
