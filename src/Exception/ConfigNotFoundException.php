<?php

declare(strict_types=1);

namespace Demai\Config\Exception;

use RuntimeException;

class ConfigNotFoundException extends RuntimeException implements ConfigExceptionInterface
{
}
