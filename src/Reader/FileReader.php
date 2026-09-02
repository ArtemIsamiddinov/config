<?php

declare(strict_types=1);

namespace Demai\Config\Reader;

use Demai\Config\Exception\ConfigNotFoundException;
use Demai\Config\Exception\ConfigReadException;

abstract class FileReader implements ReaderInterface
{
    protected string $source;

    public function isCachable(): bool
    {
        return true;
    }

    public function read(): array
    {
        if (!$this->isSourceExists()) {
            throw new ConfigNotFoundException("Configuration source not found");
        }

        if (!is_readable($this->getSource())) {
            throw new ConfigReadException("Failed to read config file.");
        }

        return [];
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

    public function isSourceExists(): bool
    {
        return file_exists($this->getSource());
    }
}
