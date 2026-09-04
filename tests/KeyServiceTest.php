<?php

declare(strict_types=1);

use Demai\Config\BaseConfig;
use PHPUnit\Framework\TestCase;
use Demai\Config\Service\KeyService;
use PHPUnit\Framework\Attributes\DataProvider;

class KeyServiceTest extends TestCase
{
    public static function keysProvider(): array
    {
        return [
            ['testKey', 'test_key'],
            ['testKey', 'test-key'],
            ['testKey', 'TEST_KEY'],
            ['test1Key', 'TEST_1-key'],
        ];
    }

    #[DataProvider('keysProvider')]
    public function testGetCorrect(string $expected, string $actual)
    {
        $service = new KeyService();
        $this->assertSame($expected, $service->getCorrect($actual));
    }

    public function testGetKeys()
    {
        $service = new KeyService();
        
        $cfg = new class extends BaseConfig {
            protected string $test;
            protected bool $someBool;
            protected float $someFloat;
        };

        $keys = $service->getKeys($cfg);
        $awaitKeys = ['test', 'someBool', 'someFloat'];

        foreach ($awaitKeys as $awaitKey) {
            $this->assertContains($awaitKey, $keys, "Key «{$awaitKey}» not found in keys.");
        }
    }
}
