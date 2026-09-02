<?php

declare(strict_types=1);

namespace Demai\Config\Service;

use Demai\Config\ConfigInterface;
use Demai\Config\Reader\NullReader;
use Demai\Config\Reader\ReaderInterface;

/**
 * Сервис для чтения источников данных для конфигов.
 * Сервис выполняет чтение источника данных при помощи ReaderInterface и заполняет конфиги данными.
 *
 * @package Demai\Config
 * @author Artem Isamiddinov <artemisamiddinov@gmail.com>
 * @version 1.0.0
 */
final class ReadService
{
    /**
     * @var array Уже прочитанные источники и их данные.
     */
    private array $storage = [];

    /**
     * @param KeyService $keyService Сервис для работы с ключами данных.
     * @param array $skipKeys Массив ключей, которые не будут заполняться.
     */
    public function __construct(private KeyService $keyService, private array $skipKeys = [])
    {
    }

    /**
     * Прочитать источник и заполнить конфиг данными.
     *
     * @param ConfigInterface $config Конфиг, для которого выполняется чтение.
     */
    public function read(ConfigInterface $config): void
    {
        $this->fillConfig($config);
    }

    /**
     * Выполнить рекурсивное заполнение конфига и его дочерних конфигов.
     *
     * @param ConfigInterface $config Заполняемый конфиг.
     */
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
                } else {
                    $config->set($key, $data[$key]);
                }
            }
        }
    }

    /**
     * Попробовать прочитать данные из кэша. Если в кэше данных нет, будет прочитан источник и внесен в кэш.
     *
     * @param ReaderInterface $reader Reader, который будет читать данные.
     * @return array Данные, которые прочитал Reader.
     */
    private function tryReadCache(ReaderInterface $reader): array
    {
        if ($reader->isCachable()) {
            if (($data = $this->getSourceData($reader->getSource())) !== null) {
                return $data;
            }

            return $this
                ->setSourceData($reader->getSource(), $reader->read())
                ->getSourceData($reader->getSource());
        }
        return $reader->read();
    }

    /**
     * Записать данные в кэш.
     *
     * @param string $source Имя источника.
     * @param array $data Данные источника.
     * @return static
     */
    public function setSourceData(string $source, array $data): static
    {
        $this->storage[$source] = $data;
        return $this;
    }

    /**
     * Получить данные из кэша.
     *
     * @param string $source Имя источника, для которого получаем данные из кэша.
     * @return null|array null - если данных в кэше нет, array - если данные есть.
     */
    public function getSourceData(string $source): ?array
    {
        return array_key_exists($source, $this->storage) ? $this->storage[$source] : null;
    }
}
