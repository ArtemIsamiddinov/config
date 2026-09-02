<?php

declare(strict_types=1);

namespace Demai\Config\Exception;

use RuntimeException;

class ConfigReadException extends RuntimeException implements ConfigExceptionInterface
{
}
