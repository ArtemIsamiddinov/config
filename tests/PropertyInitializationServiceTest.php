<?php

declare(strict_types=1);

use Demai\Config\Attribute\DefaultValue;
use Demai\Config\BaseConfig;
use PHPUnit\Framework\TestCase;
use Demai\Config\Service\PropertyInitializationService;
use Demai\Config\Exception\InitializePropertyException;

class PropertyInitializationServiceTest extends TestCase
{
    public function testWithDefault()
    {
        $cfg = new class extends BaseConfig {
            protected ?string $value;
            #[DefaultValue('aaa')]
            protected string $value1;
        };

        $service = new PropertyInitializationService($cfg);

        $service->initialize('value');
        $this->assertEquals(null, $cfg->get('value'));

        $service->initialize('value1');
        $this->assertEquals('aaa', $cfg->get('value1'));
    }

    public function testInitializeException()
    {
        $this->expectException(InitializePropertyException::class);

        $cfg = new class extends BaseConfig {
            protected string $value;
        };

        $service = new PropertyInitializationService($cfg);

        $service->initialize('value');
    }
}
