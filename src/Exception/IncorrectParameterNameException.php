<?php

declare(strict_types=1);

namespace Demai\Config\Exception;

use RuntimeException;

class IncorrectParameterNameException extends RuntimeException implements ConfigExceptionInterface
{
}
