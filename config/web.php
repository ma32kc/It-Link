<?php

declare(strict_types=1);

use App\Application\Controller\CarController;
use App\Domain\Repository\CarRepositoryInterface;
use App\Infrastructure\Persistence\CarRepository;
use yii\web\JsonParser;
use yii\web\Response;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

return [
    'id' => 'car-ads',
    'name' => 'Car Ads API',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-RU',
    'controllerNamespace' => 'App\\Application\\Controller',
    'controllerMap' => [
        'car' => CarController::class,
    ],
    'bootstrap' => [],
    'components' => [
        'db' => $db,
        'request' => [
            'cookieValidationKey' => getenv('COOKIE_VALIDATION_KEY') ?: 'car-ads-secret-key',
            'enableCsrfValidation' => false,
            'parsers' => [
                'application/json' => JsonParser::class,
            ],
        ],
        'response' => [
            'format' => Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'errorHandler' => [
            'errorAction' => null,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                'POST car/create' => 'car/create',
                'GET car/list' => 'car/list',
                'GET car/<id:\d+>' => 'car/view',
            ],
        ],
    ],
    'container' => [
        'singletons' => [
            CarRepositoryInterface::class => static fn (): CarRepository => new CarRepository(Yii::$app->db),
        ],
    ],
    'params' => $params,
];
