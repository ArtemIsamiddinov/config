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

abstract class BaseConfig implements ConfigInterface
{
    protected ReaderInterface $reader;
    protected PropertyInitializationService $initializationService;

    public function __construct()
    {
        $this->initializationService = new PropertyInitializationService($this);
        $this->reader = new NullReader();
    }

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
            } else {
                throw new IncorrectParameterNameException("Incorrect parameter name «{$name}» for set value");
            }
        }

        if (!property_exists($this, $name)) {
            throw new ParameterNotFoundException("Parameter for set value not found");
        }

        $this->$name = $value;

        return $this;
    }

    public function getReader(): ReaderInterface
    {
        return $this->reader;
    }

    public function setReader(ReaderInterface $reader): static
    {
        $this->reader = $reader;
        return $this;
    }

    public function toArray(): array
    {
        return ConfigDataMapper::mapConfigToArray(
            $this,
            (new KeyService())->getKeys($this, ['reader', 'initializationService'])
        );
    }
}
