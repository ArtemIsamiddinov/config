<?php

declare(strict_types=1);

namespace Demai\Config\Service;

use Demai\Config\ConfigInterface;
use ReflectionProperty;
use Demai\Config\Attribute\DefaultValue;
use Demai\Config\Exception\InitializePropertyException;

/**
 * Сервис для инициализации свойств.
 * Сервис выполняет инициализацию свойств, если они еще не инициализированы.
 *
 * @package Demai\Config
 * @author Artem Isamiddinov <artemisamiddinov@gmail.com>
 * @version 1.0.0
 */
class PropertyInitializationService
{
    /**
     * @param ConfigInterface $config Конфиг для свойств которого будет выполняться инициализация.
     */
    public function __construct(protected ConfigInterface $config)
    {
    }

    /**
     * Инициализировать свойство.
     *
     * @param string $name Имя свойства для инициализации.
     */
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

            throw new InitializePropertyException("Service can't resolve default value for property «{$name}»");
        }

        $this->config->set($name, array_pop($attributes)->newInstance()->value);
    }
}
