<?php

declare(strict_types=1);

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '5432';
$name = getenv('DB_NAME') ?: 'car_ads';

return [
    'class' => yii\db\Connection::class,
    'dsn' => sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $name),
    'username' => getenv('DB_USER') ?: 'postgres',
    'password' => getenv('DB_PASSWORD') ?: 'postgres',
    'charset' => 'utf8',
    'enableSchemaCache' => !YII_DEBUG,
];
