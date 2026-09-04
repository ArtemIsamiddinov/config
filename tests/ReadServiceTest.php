<?php

declare(strict_types=1);

use Demai\Config\BaseConfig;
use Demai\Config\Reader\EnvReader;
use Demai\Config\Service\KeyService;
use PHPUnit\Framework\TestCase;
use Demai\Config\Service\ReadService;

class ReadServiceTest extends TestCase
{

    public function testRead()
    {
        $reader = $this->createMock(EnvReader::class);
        $reader->method('read')->willReturn(['test' => true, 'test1' => 'a', 'test3' => "[1,2,3]"]);

        $cfg = new class($reader) extends BaseConfig {

            protected bool $test;
            protected string $test1;
            protected ?int $test2;
            protected array $test3;

            public function __construct(EnvReader $reader)
            {
                parent::__construct();
                $this->setReader($reader);
            }
        };

        (new ReadService(new KeyService))->read($cfg);

        $awaitValues = [
            [true, 'test'],
            ['a', 'test1'],
            [null, 'test2'],
            [[1, 2, 3], 'test3']
        ];

        foreach ($awaitValues as $awaitValue) {
            $this->assertSame($awaitValue[0], $cfg->get($awaitValue[1]));
        }
    }
}
