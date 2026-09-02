<?php

declare(strict_types=1);

namespace Demai\Config\Service;

use Demai\Config\ConfigInterface;
use ReflectionProperty;
use Demai\Config\Attribute\DefaultValue;

class PropertyInitializationService
{
    public function __construct(protected ConfigInterface $config)
    {
    }

    public function initialize(string $name): void
    {
        $prop = new ReflectionProperty($this->config, $name);

        $canBeNull = true;
        if ($prop->hasType()) {
            $canBeNull = $prop->getType()->allowsNull();
        }

        $attributes = $prop->getAttributes(DefaultValue::class);

        if (empty($attributes)) {
            if ($canBeNull) {
                $this->config->set($name, null);
                return;
            }
        }
        else {
            $this->config->set($name, array_pop($attributes)->newInstance()->value);
        }
    }
}
