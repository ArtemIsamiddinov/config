<?php

declare(strict_types=1);

namespace Demai\Config\Reader;

/**
 * Интерфейс для Reader реализаций.
 * Описывает необходимые методы для Reader.
 *
 * @package Demai\Config
 * @author Artem Isamiddinov <artemisamiddinov@gmail.com>
 * @version 1.0.0
 */
interface ReaderInterface
{
    /**
     * Получить источник данных для чтения.
     *
     * @return string Путь к файлу/Имя таблицы и т.д.
     */
    public function getSource(): string;

    /**
     * Прочитать данные из источника.
     *
     * @return array Данные источника.
     */
    public function read(): array;

    /**
     * Существует ли источник данных.
     *
     * @return bool
     */
    public function isSourceExists(): bool;
}
