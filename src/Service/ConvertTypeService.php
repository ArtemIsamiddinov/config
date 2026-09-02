<?php

declare(strict_types=1);

namespace Demai\Config\Service;

use Demai\Config\Exception\ArgumentTypeException;

class ConvertTypeService
{
    private static function trimIfString(mixed &$value): void
    {
        if (is_string($value)) {
            $value = trim($value);
        }
    }

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

    public static function toInt(mixed $value, ?int $default = null): int
    {
        $options = [];

        if ($default !== null) {
            $options['default'] = $default;
        }

        return static::toIntExt($value, $options);
    }

    public static function toIntExt(mixed $value, array $options): int
    {
        static::trimIfString($value);

        $filteredValue = filter_var($value, FILTER_VALIDATE_INT, $options);
        if ($filteredValue !== null && $filteredValue !== false) {
            return $filteredValue;
        }

        throw new ArgumentTypeException("Value {$value} can't be converted to int");
    }

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

    public static function toBool(mixed $value, ?bool $default = null): bool
    {
        static::trimIfString($value);

        if (($validateValue = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) !== null) {
            return $validateValue;
        }

        if ($default === null) {
            throw new ArgumentTypeException("Value {$value} can't be converted to bool");
        }

        return $default;
    }

    public static function toFloat(mixed $value, ?float $default = null): float
    {
        $options = [];

        if ($default !== null) {
            $options['default'] = $default;
        }

        return static::toFloatExt($value, $options);
    }

    public static function toFloatExt(mixed $value, array $options): float
    {
        static::trimIfString($value);

        $filteredValue = filter_var($value, FILTER_VALIDATE_FLOAT, $options);
        if ($filteredValue !== null && $filteredValue !== false) {
            return $filteredValue;
        }

        throw new ArgumentTypeException("Value {$value} can't be converted to float");
    }

    public static function toArray(mixed $value, ?array $default = null): array
    {
        return static::toArrayExt($value, $default);
    }

    public static function toIntArray(mixed $value, ?array $itemDefault = null): array
    {
        $array = static::toArray($value);

        foreach ($array as &$val) {
            $val = static::toInt($value, $itemDefault);
        }

        return $array;
    }

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
        } else {
            if (empty($value)) {
                if ($default !== null) {
                    return $default;
                }
            } else {
                if (is_array(($tryJson = json_decode($value, true)))) {
                    return static::toArrayExt($tryJson, $default, $options);
                }
            }
        }

        throw new ArgumentTypeException("Value {$value} can't be converted to array");
    }
}
