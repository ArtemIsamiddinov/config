<?php

declare(strict_types=1);

namespace Demai\Config\Reader;

use Demai\Config\Exception\ConfigReadException;
use Demai\Config\Service\KeyService;

/**
 * Класс чтения данных из env файлов.
 * Класс читает данные из env файла и преобразует ключи в необходимый вид.
 *
 * @package Demai\Config
 * @author Artem Isamiddinov <artemisamiddinov@gmail.com>
 * @version 1.0.0
 */
abstract class EnvReader extends FileReader
{
    /**
     * @inheritdoc
     */
    public function read(): array
    {
        $data = parent::read();

        $lines = @file($this->getSource(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            throw new ConfigReadException("Failed to get data from file.");
        }

        $keyService = new KeyService();
        foreach ($lines as $line) {
            $line = trim($line);

            if (str_starts_with($line, '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);

            $data[$keyService->getCorrect($key)] = trim($value);
        }
        return $data;
    }
}
