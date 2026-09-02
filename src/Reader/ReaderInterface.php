<?php

declare(strict_types=1);

namespace Demai\Config\Reader;

interface ReaderInterface
{
    public function isCachable(): bool;

    public function getSource(): string;

    public function setSource(string $source): static;

    public function read(): array;

    public function isSourceExists(): bool;
}
