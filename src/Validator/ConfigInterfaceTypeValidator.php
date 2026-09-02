<?php

declare(strict_types=1);

namespace Demai\Config\Validator;

use Demai\Config\ConfigInterface;

class ConfigInterfaceTypeValidator
{
    public function validate(mixed $config): bool|string
    {
        return $config instanceof ConfigInterface ? 
            true : 
            "Incorrect config class. Config must be instance of ConfigInterface";
    }
}
