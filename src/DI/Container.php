<?php

declare(strict_types=1);

namespace Demai\Config\DI;

use ReflectionMethod;
use Psr\Container\ContainerInterface;
use Demai\Config\Exception\NotFoundException;
use Demai\Config\Exception\ContainerException;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use Override;

/**
 * Класс контейнер.
 * Используется как хранилище созданных объектов.
 *
 * @package Demai\Config
 * @author Artem Isamiddinov <artemisamiddinov@gmail.com>
 * @version 1.0.0
 */
class Container implements ContainerInterface
{
    public function __construct()
    {
    }

    /**
     * @var object[] Массив объектов контейнера.
     */
    private array $instances = [];

    /**
     * @var ReflectionClass[] Массив загруженных рефлекторов.
     */
    private array $loading = [];

    #[Override]
    public function get(string $id): object
    {
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }

        if (isset($this->loading[$id])) {
            throw new ContainerException("Circular dependency detected for class: {$id}");
        }

        if (!class_exists($id)) {
            throw new NotFoundException("Class «{$id}» not found and cannot be resolved by container.");
        }

        $this->loading[$id] = true;

        try {
            $reflectionClass = new ReflectionClass($id);
            $constructor = $reflectionClass->getConstructor();

            if ($constructor === null) {
                $instance = $this->getInstanceFromName($id);
                $this->instances[$id] = $instance;
                return $instance;
            }

            $dependencies = $this->getDependencies($constructor);
            $instance = $this->getInstanceFromReflection($reflectionClass, $dependencies);

            $this->instances[$id] = $instance;
            return $instance;
        } catch (ReflectionException $e) {
            throw new ContainerException("Reflection error while resolving «{$id}»: " . $e->getMessage(), 0, $e);
        } catch (\Throwable $e) {
            throw new ContainerException("Could not resolve «{$id}»: " . $e->getMessage(), 0, $e);
        } finally {
            unset($this->loading[$id]);
        }
    }

    #[Override]
    public function has(string $id): bool
    {
        // Контейнер может отдать объект, если он уже создан ИЛИ если класс физически существует
        return array_key_exists($id, $this->instances) || class_exists($id);
    }

    /**
     * Получить объект по имени класса.
     *
     * @param string $className Имя класса.
     * @return object Созданный объект.
     */
    protected function getInstanceFromName(string $className): object
    {
        return new $className();
    }

    /**
     * Получить объект из ReflectionClass.
     *
     * @param ReflectionClass $reflection Рефлекция класса.
     * @param array $dependencies Массив зависимостей класса.
     * @return object Созданный объект.
     */
    protected function getInstanceFromReflection(ReflectionClass $reflection, array $dependencies): object
    {
        return $reflection->newInstanceArgs($dependencies);
    }

    /**
     * Получить зависимости конструктора.
     *
     * @param ReflectionMethod $constructor Метод коструктора.
     * @return array Массив объектов зависимостей
     */
    protected function getDependencies(ReflectionMethod $constructor): array
    {
        $constructorParameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($constructorParameters as $parameter) {
            $type = $parameter->getType();

            if ($type === null) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                    continue;
                }
                throw new ContainerException("Cannot resolve parameter «{$parameter->getName()}» without a type.");
            }

            if (!$type instanceof ReflectionNamedType) {
                throw new ContainerException(
                    "Union or Intersection types are not supported " .
                    "for parameter '{$parameter->getName()}'."
                );
            }

            if (!$type->isBuiltin()) {
                $dependencies[] = $this->get($type->getName());
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
            } elseif ($type->allowsNull()) {
                $dependencies[] = null;
            } else {
                throw new ContainerException(
                    "Cannot resolve built-in parameter «{$parameter->getName()}» " .
                    "of type «{$type->getName()}» without a default value."
                );
            }
        }

        return $dependencies;
    }
}
