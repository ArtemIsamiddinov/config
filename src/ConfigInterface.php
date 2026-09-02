<?php

declare(strict_types=1);

namespace Demai\Config;

use Demai\Config\Reader\ReaderInterface;

interface ConfigInterface
{   
    public function get(string $name): mixed;

    public function set(string $name, mixed $value): static;

    public function getReader(): ReaderInterface;

    public function setReader(ReaderInterface $reader): static;

    public function toArray(): array;
}
