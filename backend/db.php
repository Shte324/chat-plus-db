<?php

/**
 * Подключение к PostgreSQL через PDO.
 * Возвращает объект PDO, готовый к работе.
 */

declare(strict_types=1);

$config = require __DIR__ . '/config.php';

$db = $config['db'];

$dsn = sprintf(
    'pgsql:host=%s;port=%d;dbname=%s',
    $db['host'],
    $db['port'],
    $db['database']
);

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try{
    $pdo = new PDO($dsn, $db['user'], $db['password'], $options);
} catch(PDOException $e){
     error_log('DB connection failed: ' . $e->getMessage());
    http_response_code(500);
    exit('Ошибка подключения к базе данных.');
}

return $pdo;