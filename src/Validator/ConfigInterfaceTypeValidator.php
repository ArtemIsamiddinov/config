<?php

declare(strict_types=1);

namespace Demai\Config\Validator;

use Demai\Config\ConfigInterface;

/**
 * Класс валидатора типа интерфейса
 * Проверяет, является ли конфиг наследником интерфейса ConfigInterface.
 *
 * @package Demai\Config
 * @author Artem Isamiddinov <artemisamiddinov@gmail.com>
 * @version 1.0.0
 */
class ConfigInterfaceTypeValidator
{
    /**
     * Выполнить проверку.
     *
     * @param mixed $config Данные которые необходимо проверить.
     * @return bool|string true - Если объект является наследником ConfigInterface, иначе сообщение об ошибке.
     */
    public function validate(mixed $config): bool|string
    {
        if ($config instanceof ConfigInterface) {
            return true;
        }

        return "Incorrect config class. Config must be instance of ConfigInterface";
    }
}
