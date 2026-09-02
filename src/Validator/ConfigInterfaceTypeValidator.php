<?php

declare(strict_types=1);

namespace Demai\Config\Validator;

use Demai\Config\ConfigInterface;

class ConfigInterfaceTypeValidator
{
    public function validate(mixed $config): bool|string
    {
        $error = "Incorrect config class. Config must be instance of ConfigInterface";
        return $config instanceof ConfigInterface ? true : $error;
    }
}
