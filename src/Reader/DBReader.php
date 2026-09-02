<?php

declare(strict_types=1);

namespace Demai\Config\Reader;

/**
 * Абстрактый класс для чтения источников из БД.
 * Используется как родитель для Reader, которые выполняют чтение из БД.
 *
 * @package Demai\Config
 * @author Artem Isamiddinov <artemisamiddinov@gmail.com>
 * @version 1.0.0
 */
abstract class DBReader implements ReaderInterface
{
    /**
     * @inheritdoc
     */
    public function isCachable(): bool
    {
        return true;
    }

    /**
     * Получить имя таблицы.
     *
     * @return string Имя таблицы.
     */
    abstract public function getSource(): string;
}
