<?php

declare(strict_types=1);

namespace Demai\Config\Reader;

/**
 * Класс null-значение для Reader свойств.
 * Используется для предотвращения использования null в свойствах конфигов.
 *
 * @package Demai\Config
 * @author Artem Isamiddinov <artemisamiddinov@gmail.com>
 * @version 1.0.0
 */
class NullReader implements ReaderInterface
{
    /**
     * @inheritdoc
     */
    public function read(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getSource(): string
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function isSourceExists(): bool
    {
        return true;
    }
}
