<?php

declare(strict_types=1);

namespace Demai\Config\Service;

use Demai\Config\Exception\ArgumentTypeException;

/**
 * Сервис для конвертации типов данных.
 * Сервис конвертирует типы данных по заданным параметрам.
 *
 * @package Demai\Config
 * @author Artem Isamiddinov <artemisamiddinov@gmail.com>
 * @version 1.0.0
 */
class ConvertTypeService
{
    /**
     * Удалить пробелы в начале и в конце если значение является строкой.
     *
     * @param mixed $value Значение для преобразования
     */
    private static function trimIfString(mixed $value): mixed
    {
        return is_string($value) ? trim($value) : $value;
    }

    /**
     * Конвертировать значение в указанный builtin тип данных.
     *
     * @param string $builtinType Имя типа данных.
     * @param mixed $value Значение которое будет конвертировано.
     * @return mixed Конвертированное значение.
     */
    public static function convertTo(string $builtinType, mixed $value): mixed
    {
        return match ($builtinType) {
            'string' => static::toString($value),
            'int' => static::toInt($value),
            'float' => static::toFloat($value),
            'bool' => static::toBool($value),
            'array' => static::toArray($value),
            default => $value
        };
    }

    /**
     * Преобразовать в целое число.
     *
     * @param mixed $value Значение для преобразования.
     * @param null|int $default Значение по умолчанию. Если null, то не устанавливать.
     * @return int Преобразованное значение.
     */
    public static function toInt(mixed $value, ?int $default = null): int
    {
        $options = [];

        if ($default !== null) {
            $options['options'] = ['default' => $default];
        }

        return static::toIntExt($value, $options);
    }

    /**
     * Преобразовать в целое число с использованием опций.
     * Выполнить преобразование через функцию filter_var с фильтром FILTER_VALIDATE_INT.
     *
     * @param mixed $value Значение для преобразования.
     * @param array $options Опции для преобразования данных.
     * @return int Преобразованное значение.
     */
    public static function toIntExt(mixed $value, array $options): int
    {
        $value = static::trimIfString($value);

        $filteredValue = filter_var($value, FILTER_VALIDATE_INT, $options);
        if ($filteredValue !== null && $filteredValue !== false) {
            return $filteredValue;
        }

        throw new ArgumentTypeException("Value {$value} can't be converted to int");
    }

    /**
     * Преобразовать в строку.
     *
     * @param mixed $value Значение для преобразования.
     * @param null|string $default Значение по умолчанию. Если null, то не устанавливать.
     * @param bool $needTrim Нужно ли удалить пробелы в начале и в конце строки.
     * @return string Преобразованное значение.
     */
    public static function toString(mixed $value, ?string $default = null, bool $needTrim = false): string
    {
        if ($value === null) {
            if ($default !== null) {
                return $default;
            }
            throw new ArgumentTypeException("Value null can't be converted to string");
        }

        if (!is_string($value)) {
            $value = strval($value);
        }

        if ($needTrim) {
            $value = trim($value);
        }

        return $value;
    }

    /**
     * Преобразовать значение в bool.
     * Метод преобразует данные при помощи функции filter_var, с использованием фильтра FILTER_VALIDATE_BOOLEAN.
     *
     * @param mixed $value Значение для преобразования.
     * @param null|bool $default Значение по умолчанию. Если null, то не устанавливать.
     * @return bool Преобразованное значение
     */
    public static function toBool(mixed $value, ?bool $default = null): bool
    {
        $value = static::trimIfString($value);

        if (($validateValue = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) !== null) {
            return $validateValue;
        }

        if ($default === null) {
            throw new ArgumentTypeException("Value {$value} can't be converted to bool");
        }

        return $default;
    }

    /**
     * Преобразовать значение в float.
     *
     * @param mixed $value Значение для преобразования.
     * @param null|float $default Значение по умолчанию. Если null, то не устанавливать.
     * @return float Преобразованное значение.
     */
    public static function toFloat(mixed $value, ?float $default = null): float
    {
        $options = [];

        if ($default !== null) {
            $options['options'] = ['default' => $default];
        }

        return static::toFloatExt($value, $options);
    }

    /**
     * Преобразовать значение в float с использованием опций.
     * Метод преобразует значение при помощи функции filter_var, с использованием фильтра FILTER_VALIDATE_FLOAT.
     *
     * @param mixed $value Значение для преобразования.
     * @param array $options Массив опций для преобразования.
     * @return float Преобразованное значение.
     */
    public static function toFloatExt(mixed $value, array $options): float
    {
        $value = static::trimIfString($value);

        $filteredValue = filter_var($value, FILTER_VALIDATE_FLOAT, $options);
        if ($filteredValue !== null && $filteredValue !== false) {
            return $filteredValue;
        }

        throw new ArgumentTypeException("Value {$value} can't be converted to float");
    }

    /**
     * Преобразовать значение в массив.
     *
     * @param mixed $value Значение для преобразования.
     * @param null|array $default Значение по умолчанию. Если null, то не устанавливать.
     * @return array Преобразованное значение.
     */
    public static function toArray(mixed $value, ?array $default = null): array
    {
        return static::toArrayExt($value, $default);
    }

    /**
     * Преобразовать значение в массив целых чисел.
     *
     * @param mixed $value Значение для преобразования.
     * @param null|int $itemDefault Значение по умолчанию для элементов. Если null, то не устанавливать.
     * @return array Преобразованное значение.
     */
    public static function toIntArray(mixed $value, ?int $itemDefault = null): array
    {
        $array = static::toArray($value);

        foreach ($array as &$val) {
            $val = static::toInt($val, $itemDefault);
        }

        return $array;
    }

    /**
     * Преобразовать значение в массив с использованием опций.
     * Метод преобразует данные при помощи функции filter_var_array.
     *
     * @param mixed $value Значение для преобразования.
     * @param null|array $default Значение по умолчанию. Если null, не устанавливать.
     * @param array $options Массив опций для преобразования.
     * @param bool $addEmpty Нужно ли заполнять пустые значения.
     * @return array Преобразованное значение.
     */
    public static function toArrayExt(
        mixed $value,
        ?array $default = null,
        array $options = [],
        bool $addEmpty = false
    ): array {
        if (is_array($value)) {
            if (empty($options)) {
                return $value;
            }
            return filter_var_array($value, $options, $addEmpty);
        }

        if (is_string($value)) {
            $tryJson = json_decode(trim($value), true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($tryJson)) {
                return static::toArrayExt($tryJson, $default, $options);
            }
        }

        if (empty($value) && $default !== null) {
            return $default;
        }

        $displayValue = is_scalar($value) ? (string)$value : gettype($value);
        throw new ArgumentTypeException("Value {$displayValue} can't be converted to array");
    }
}
