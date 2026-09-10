<?php

declare(strict_types=1);

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/vendor/yiisoft/yii2/Yii.php';

// нужен Yii::$app для валидации форм; БД тут не используется
new yii\console\Application([
    'id' => 'car-ads-test',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-RU',
]);
