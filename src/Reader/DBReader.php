<?php

declare(strict_types=1);

namespace Demai\Config\Reader;

abstract class DBReader implements ReaderInterface
{
    protected string $source;

    public function isCachable(): bool
    {
        return true;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): static
    {
        $this->source = $source;
        return $this;
    }
}