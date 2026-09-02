<?php

declare(strict_types=1);

namespace Demai\Config;

use Demai\Config\Reader\ReaderInterface;

/**
 * Интерфейс конфига
 * Конфиг позволяет хранить данные в объекте из источников конфигураций.
 * Хранение происходит через свойства класса. Свойством может также быть объявлен другой конфиг
 * для вложенной структуры.
 *
 * @package Demai\Config
 * @author Artem Isamiddinov <artemisamiddinov@gmail.com>
 * @version 1.0.0
 */
interface ConfigInterface
{
    /**
     * Получить параметр или конфиг.
     * Допустимые варианты:
     * - propName - Получить данные из текущего конфига
     * - cfgName - Получить конфиг
     * - cfgName.cfgName.propName - Получить данные или конфиг из указанного пути
     * @param string $name Имя параметра или путь до параметра.
     * @return mixed Параметр или дочерний конфиг.
     */
    public function get(string $name): mixed;

    /**
     * Установить данные или конфиг для параметра.
     * Допустимые варианты:
     * - propName - Установить данные для текущего конфига
     * - cfgName - Установить конфиг
     * - cfgName.cfgName.propName - Установить данные или конфиг по указанному пути
     */
    public function set(string $name, mixed $value): static;

    /**
     * Получить Reader для текущего конфига.
     * @return ReaderInterface Reader, отвечающий за чтение источника данных.
     */
    public function getReader(): ReaderInterface;

    /**
     * Установить Reader для текущего конфига.
     * @param ReaderInterface $reader Reader, который будет отвечать за чтение источника данных.
     * @return static
     */
    public function setReader(ReaderInterface $reader): static;

    /**
     * Преобразоват конфиг в ассоциативный массив.
     * @return array Ассоциативный массив параметров и конфигов.
     */
    public function toArray(): array;
}
