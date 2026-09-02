<?php

declare(strict_types=1);

namespace Demai\Config\Reader;

class NullReader implements ReaderInterface
{
    public function isCachable(): bool
    {
        return false;
    }

    public function read(): array
    {
        return [];
    }

    public function getSource(): string
    {
        return '';
    }

    public function setSource(string $source): static
    {
        return $this;
    }

    public function isSourceExists(): bool
    {
        return true;
    }
}
