<?php

declare(strict_types=1);

namespace Demai\Config\Exception;

use RuntimeException;

class IncorrectConfigClass extends RuntimeException implements ConfigExceptionInterface
{
}
