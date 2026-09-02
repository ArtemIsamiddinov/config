<?php

declare(strict_types=1);

namespace Demai\Config\Service;

use Demai\Config\ConfigInterface;
use ReflectionProperty;
use Demai\Config\Exception\ParameterNotFoundException;
use Demai\Config\Exception\IncorrectParameterNameException;

class PropertyAccessService
{
    public readonly string $waySeparator;

    public function __construct(protected ConfigInterface $config)
    {
        $this->waySeparator = '.';
    }

    public function nameIsWay(string $name): bool
    {
        return count($this->getWay($name)) > 1;
    }

    public function getWay(string $name): array
    {
        return explode($this->waySeparator, $name);
    }

    public function getName(array $way): string
    {
        return implode($this->waySeparator, $way);
    }

    public function has(string $name): bool
    {
        return property_exists($this->config, $name);
    }

    public function isInitialized(string $name): bool
    {
        return (new ReflectionProperty($this->config, $name))->isInitialized($this->config);
    }

    public function getValueForConfig(string $name): mixed
    {
        $way = $this->getWay($name);
        $key = array_shift($way);
        $way = $this->getName($way);

        if ($this->has($key)) {
            $value = $this->config->get($key);
            if ($value instanceof ConfigInterface) {
                return $value->get($way);
            } else {
                throw new IncorrectParameterNameException("Parameter {$key} for {$way} has incorrect type");
            }
        } else {
            throw new ParameterNotFoundException("Parameter {$key} for {$way} not found");
        }
    }
}
