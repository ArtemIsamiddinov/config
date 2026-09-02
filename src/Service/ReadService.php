<?php

declare(strict_types=1);

namespace Demai\Config\Service;

use Demai\Config\ConfigInterface;
use Demai\Config\Reader\NullReader;
use Demai\Config\Reader\ReaderInterface;
use Demai\Config\Repository\ConfigRepository;
use ReflectionProperty;

final class ReadService
{
    private array $storage = [];

    public function __construct(private KeyService $keyService, private array $skipKeys = [])
    {
    }

    public function read(ConfigInterface $config): void
    {
        $this->fillConfig($config);
    }

    private function fillConfig(ConfigInterface $config): void
    {
        $reader = $config->getReader();
        $skipFillValue = $reader::class === NullReader::class;
        $typeService = new PropertyTypeService($config);
        $data = $this->tryReadCache($config->getReader());

        foreach ($this->keyService->getKeys($config, $this->skipKeys) as $key) {

            if ($typeService->isConfig($key)) {
                $this->fillConfig($config->get($key));
                continue;
            }

            if ($skipFillValue) {
                continue;
            }

            if (array_key_exists($key, $data)) {

                if (($type = $typeService->getBuiltinType($key)) !== null) {
                    $config->set($key, ConvertTypeService::convertTo($type, $data[$key]));
                }
                else {
                    $config->set($key, $data[$key]);
                }
            }
        }
    }

    private function tryReadCache(ReaderInterface $reader): array
    {
        if ($reader->isCachable()) {
            if (($data = $this->getSourceData($reader->getSource())) !== null) {
                return $data;
            }
            else {
                return $this
                    ->setSourceData($reader->getSource(), $reader->read())
                    ->getSourceData($reader->getSource());
            }
        }
        return $reader->read();
    }

    public function setSourceData(string $source, array $data): static
    {
        $this->storage[$source] = $data;
        return $this;
    }

    public function getSourceData(string $source): ?array
    {
        return array_key_exists($source, $this->storage) ? $this->storage[$source] : null;
    }

}
