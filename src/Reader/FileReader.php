<?php

declare(strict_types=1);

namespace Demai\Config\Reader;

use Demai\Config\Exception\ConfigNotFoundException;
use Demai\Config\Exception\ConfigReadException;

/**
 * Абстрактный класс для Reader, которые получают данные из файла.
 * Реализует часть методов для упрощенного создания конкретной реализации.
 *
 * @package Demai\Config
 * @author Artem Isamiddinov <artemisamiddinov@gmail.com>
 * @version 1.0.0
 */
abstract class FileReader implements ReaderInterface
{
    /**
     * Прочитать файл.
     *
     * @inheritdoc
     */
    public function read(): array
    {
        if (!$this->isSourceExists()) {
            throw new ConfigNotFoundException("Configuration source not found");
        }

        if (!is_readable($this->getSource())) {
            throw new ConfigReadException("Failed to read config file.");
        }

        return [];
    }

    /**
     * Получить путь к файлу.
     *
     * @inheritdoc
     */
    abstract public function getSource(): string;

    /**
     * Существует ли файл.
     *
     * @return bool
     */
    public function isSourceExists(): bool
    {
        return file_exists($this->getSource());
    }
}
