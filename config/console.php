<?php

declare(strict_types=1);

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

return [
    'id' => 'car-ads-console',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-RU',
    'aliases' => [
        '@App' => dirname(__DIR__) . '/src',
        '@App/Migrations' => dirname(__DIR__) . '/migrations',
    ],
    'controllerNamespace' => 'App\\Console\\Controller',
    'bootstrap' => ['log'],
    'components' => [
        'db' => $db,
        'log' => [
            'targets' => [
                [
                    'class' => yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'controllerMap' => [
        'migrate' => [
            'class' => yii\console\controllers\MigrateController::class,
            'migrationNamespaces' => ['App\\Migrations'],
            'migrationPath' => null,
        ],
    ],
    'params' => $params,
];
