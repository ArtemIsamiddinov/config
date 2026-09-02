<?php

declare(strict_types=1);

namespace Demai\Config\Reader;

use Demai\Config\Exception\ConfigReadException;
use Demai\Config\Service\KeyService;

class JsonReader extends FileReader
{
    public function read(): array
    {
        parent::read();

        $data = [];
        $jsonData = json_decode(file_get_contents($this->getSource()), true);
        if (!is_array($jsonData)) {
            throw new ConfigReadException("Failed to read config file.");
        }

        $keyService = new KeyService();
        foreach ($jsonData as $key => $itemData) {
            $data[$keyService->replaceSpecChars($key)] = $itemData;
        }

        return $data;
    }
}
