<?php

declare(strict_types=1);

namespace Demai\Config\Service;

use Demai\Config\ConfigInterface;
use Reflection;
use ReflectionProperty;
use ReflectionNamedType;

class PropertyTypeService
{
    public function __construct(protected ConfigInterface $config)
    {
    }

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

    public function getBuiltinType(string $name): null|string
    {
        $prop = new ReflectionProperty($this->config, $name);
        if ($prop->hasType()) {
            if ($prop->getType()->isBuiltin()) {
                return $prop->getType()->getName();
            }
        }
        return null;
    }
}
