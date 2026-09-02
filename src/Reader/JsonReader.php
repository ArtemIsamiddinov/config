<?php

declare(strict_types=1);

namespace Demai\Config\Reader;

use Demai\Config\Exception\ConfigReadException;
use Demai\Config\Service\KeyService;

/**
 * Класс чтения данных из json файлов.
 * Класс читает данные из json файла и преобразует ключи в необходимый вид.
 *
 * @package Demai\Config
 * @author Artem Isamiddinov <artemisamiddinov@gmail.com>
 * @version 1.0.0
 */
abstract class JsonReader extends FileReader
{
    /**
     * @inheritdoc
     */
    public function read(): array
    {
        $data = parent::read();
        $content = @file_get_contents($this->getSource());

        if ($content === false) {
            throw new ConfigReadException("Failed to read json file.");
        }

        $jsonData = @json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ConfigReadException("Failed to decode data.");
        }

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
