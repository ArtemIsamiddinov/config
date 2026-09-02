<?php

declare(strict_types=1);

namespace Demai\Config\Repository;

use Demai\Config\ConfigInterface;
use Demai\Config\Exception\IncorrectConfigClass;
use Demai\Config\Service\KeyService;
use Demai\Config\Service\ReadService;
use Demai\Config\Validator\ConfigInterfaceTypeValidator;

class ConfigRepository
{
    /**
     * @var ConfigInterface[]
     */
    protected static array $configs = [];
    protected ReadService $readService;

    public function __construct(?ReadService $readService = null)
    {
        if (empty($readService)) {
            $readService = new ReadService(new KeyService(), ['reader', 'initializationService']);
        }

        $this->readService = $readService;
    }

    public function get(string|ConfigInterface $config): ?ConfigInterface
    {
        if (is_string($config)) {
            $config = new $config();
        }

        if (($checkType = (new ConfigInterfaceTypeValidator())->validate($config)) !== true) {
            throw new IncorrectConfigClass($checkType);
        }

        if (array_key_exists($config::class, static::$configs)) {
            return static::$configs[$config::class];
        }
        else {
            return $this->load($config);
        }
    }

    public function has(string|ConfigInterface $config): bool
    {
        if (!is_string($config)) {
            $config = $config::class;
        }
        
        return array_key_exists($config, static::$configs);
    }

    public function save(ConfigInterface $config): static
    {
        static::$configs[$config::class] = $config;
        return $this;
    }

    public function load(string|ConfigInterface $config): ConfigInterface
    {
        if (is_string($config)) {
            $config = new $config();
        }

        if (($checkType = (new ConfigInterfaceTypeValidator())->validate($config)) !== true) {
            throw new IncorrectConfigClass($checkType);
        }

        $this->readService->read($config);

        return $this->save($config)->get($config::class);
    }

    public function getKeys(): array
    {
        return array_keys(static::$configs);
    }
}
