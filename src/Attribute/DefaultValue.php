<?php

declare(strict_types=1);

namespace Demai\Config\Attribute;

use Attribute;

/**
 * Класс-атрибут для значений по умолчанию.
 * Позволяет создавать аннотации к свойствам, по которым будет происходить установка значений по умолчанию.
 *
 * @package Demai\Config
 * @author Artem Isamiddinov <artemisamiddinov@gmail.com>
 * @version 1.0.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class DefaultValue
{
    public function __construct(public mixed $value)
    {
    }
}
