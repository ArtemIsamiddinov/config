<?php

declare(strict_types=1);

use Demai\Config\Service\ConvertTypeService;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class ConverTypeServiceTest extends TestCase
{
    public static function boolProvider(): array
    {
        return [
            [true, 'true'],
            [true, '1'],
            [true, 1],
            [true, 'Y'],
            [true, true],

            [false, ''],
            [false, 'false'],
            [false, 'N'],
            [false, null],
            [false, false],
            [false, 0],
            [false, '0']
        ];
    }

    #[DataProvider('boolProvider')]
    public function testBoolConvert(bool $expect, mixed $actual)
    {
        $result = ConvertTypeService::toBool($actual);
        $this->assertSame(
            $expect, 
            $result, 
            "Некорректная конвертация в string «" . var_export($actual, true) . "», " .
            "получено «" . var_export($result, true) . "», " .
            "ожидалось «" . var_export($expect, true) . "»"
        );
    }

    public static function intProvider(): array
    {
        return [
            [10, "10", null],
            [10, 10, null],
            [-10, "-10", null],
            [10, null, 10],
            [10, '', 10],
        ];
    }

    #[DataProvider('intProvider')]
    public function testIntConvert(mixed $expect, mixed $actual, mixed $default)
    {
        $result = ConvertTypeService::toInt($actual, $default);
        $this->assertSame(
            $expect, 
            $result, 
            "Некорректная конвертация в int «" . var_export($actual, true) . "», " .
            "получено «" . var_export($result, true) . "», " .
            "ожидалось «" . var_export($expect, true) . "»"
        );
    }

    public static function floatProvider(): array
    {
        return [
            [10.10, '10.10', null],
            [10.10, 10.10, null],
            [10.10, null, 10.10],
            [10.10, '', 10.10]
        ];
    }

    #[DataProvider('floatProvider')]
    public function testFloatConvert(mixed $expect, mixed $actual, mixed $default)
    {
        $result = ConvertTypeService::toFloat($actual, $default);
        $this->assertSame(
            $expect, 
            $result, 
            "Некорректная конвертация в float «" . var_export($actual, true) . "», " .
            "получено «" . var_export($result, true) . "», " .
            "ожидалось «" . var_export($expect, true) . "»"
        );
    }

    public static function arrayProvider(): array
    {
        return [
            [[1, 2, 3], [1, 2, 3], null],
            [[1, 2, 3], "[1,2,3]", null],
            [[1, 2, 3], "", [1, 2, 3]],
            [[1, 2, 3], null, [1, 2, 3]]
        ];
    }

    #[DataProvider('arrayProvider')]
    public function testArrayConvert(mixed $expect, mixed $actual, mixed $default)
    {
        $result = ConvertTypeService::toArray($actual, $default);
        $this->assertSame(
            $expect, 
            $result, 
            "Некорректная конвертация в array «" . var_export($actual, true) . "», " .
            "получено «" . var_export($result, true) . "», " .
            "ожидалось «" . var_export($expect, true) . "»"
        );
    }

    public static function stringProvider(): array
    {
        return [
            ["a", " a ", null, true],
            [" a ", " a ", null, false],
            ["", false, null, true],
            ["abc", null, "abc", true],
        ];
    }

    #[DataProvider('stringProvider')]
    public function testStringConvert(mixed $expect, mixed $actual, mixed $default, bool $needTrim)
    {
        $result = ConvertTypeService::toString($actual, $default, $needTrim);
        $this->assertSame(
            $expect, 
            $result, 
            "Некорректная конвертация в string «" . var_export($actual, true) . "», " .
            "получено «" . var_export($result, true) . "», " .
            "ожидалось «" . var_export($expect, true) . "»"
        );
    }

    public static function typesProvider(): array
    {
        return [
            ['int', 11, '11'],
            ['int', 11, 11],
            ['float', 11.11, '11.11'],
            ['float', 11.11, 11.11],
            ['bool', true, 'Y'],
            ['bool', false, null],
            ['array', [1, 2, 3], "[1,2,3]"]
        ];
    }

    #[DataProvider('typesProvider')]
    public function testTypesConvert(mixed $type, mixed $expect, mixed $actual)
    {
        $result = ConvertTypeService::convertTo($type, $actual);
        $this->assertSame(
            $expect, 
            $result, 
            "Некорректная конвертация в {$type} «" . var_export($actual, true) . "», " .
            "получено «" . var_export($result, true) . "», " .
            "ожидалось «" . var_export($expect, true) . "»"
        );
    }
}
