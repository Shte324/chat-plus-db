<?php

/**
 * Конфигурация проекта.
 * Загружает переменные из .env и возвращает массив с настройками.
 */

declare(strict_types=1);

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ .'/..');
$dotenv->load();

return [
    'db'=> [
        'host'    => $_ENV['DB_HOST'],
        'port'    =>  (int)$_ENV['DB_PORT'],
        'database'=>  $_ENV['DB_NAME'],
        'user'    =>  $_ENV['DB_USER'],
        'password'=>  $_ENV['DB_PASSWORD']
    ],
];

