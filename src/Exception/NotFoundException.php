<?php

declare(strict_types=1);

namespace Demai\Config\Exception;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class NotFoundException extends RuntimeException implements NotFoundExceptionInterface 
{
}
