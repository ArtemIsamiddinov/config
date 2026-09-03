<?php

declare(strict_types=1);

namespace Demai\Config;

use Demai\Config\Exception\IncorrectParameterNameException;
use Demai\Config\Exception\ParameterNotFoundException;
use Demai\Config\Reader\ReaderInterface;
use Demai\Config\DataMapper\ConfigDataMapper;
use Demai\Config\Reader\NullReader;
use Demai\Config\Service\KeyService;
use Demai\Config\Service\PropertyAccessService;
use Demai\Config\Service\PropertyInitializationService;
use Override;

/**
 * Абстрактный класс конфига.
 * Позволяет упростить создание реализации конфига.
 *
 * @package Demai\Config
 * @author Artem Isamiddinov <artemisamiddinov@gmail.com>
 * @version 1.0.0
 */
abstract class BaseConfig implements ConfigInterface
{
    /**
     * @var ReaderInterface Reader, через который происходит получение данных из источников.
     */
    protected ReaderInterface $reader;

    /**
     * @var PropertyInitializationService Сервис для инициализации еще неинициализированных свойств.
     */
    protected PropertyInitializationService $initializationService;

    public function __construct()
    {
        $this->initializationService = new PropertyInitializationService($this);
        $this->reader = new NullReader();
    }

    /**
     * @inheritdoc
     */
    public function get(string $name): mixed
    {
        $propService = new PropertyAccessService($this);

        if ($propService->nameIsWay($name)) {
            return $propService->getValueForConfig($name);
        }

        if ($propService->has($name)) {
            if (!$propService->isInitialized($name)) {
                $this->initializationService->initialize($name);
            }
            return $this->$name;
        }
        throw new ParameterNotFoundException("Parameter {$name} not found");
    }

    /**
     * @inheritdoc
     */
    public function set(string $name, mixed $value): static
    {
        $propService = new PropertyAccessService($this);
        if ($propService->nameIsWay($name)) {
            $way = $propService->getWay($name);
            $nameForConfig = array_pop($way);

            $config = $this->get($propService->getName($way));
            if ($config instanceof ConfigInterface) {
                $config->set($nameForConfig, $value);
                return $this;
            }

            throw new IncorrectParameterNameException("Incorrect parameter name «{$name}» for set value");
        }

        if (!property_exists($this, $name)) {
            throw new ParameterNotFoundException("Parameter for set value not found");
        }

        $this->$name = $value;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReader(): ReaderInterface
    {
        return $this->reader;
    }

    /**
     * @inheritdoc
     */
    public function setReader(ReaderInterface $reader): static
    {
        $this->reader = $reader;
        return $this;
    }

    /**
     * Получить свойства и вложенные конфиги в виде ассоциативного массива.
     * Свойства с именем reader и initializationService пропускаются.
     *
     * @inheritdoc
     */
    public function toArray(): array
    {
        return ConfigDataMapper::mapConfigToArray(
            $this,
            (new KeyService())->getKeys($this)
        );
    }

    #[Override]
    public function getSkipKeys(): array
    {
        return ['reader', 'initializationService'];
    }
}
