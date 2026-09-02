<?php

declare(strict_types=1);

namespace Demai\Config\Reader;

use Demai\Config\Service\KeyService;

class EnvReader extends FileReader
{
    public function read(): array
    {
        parent::read();

        $data = [];
        $lines = file($this->getSource(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $keyService = new KeyService();
        foreach ($lines as $line) {
            $line = trim($line);

            if (str_starts_with($line, '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line);

            $data[$keyService->getCorrect($key)] = trim($value);
        }
        return $data;
    }
}
