<?php

declare(strict_types=1);

// В докере переменные уже в окружении, .env может не быть.
$envFile = dirname(__DIR__) . '/.env';

if (!is_file($envFile)) {
    return;
}

foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);

    if ($line === '' || str_starts_with($line, '#')) {
        continue;
    }

    [$name, $value] = array_pad(explode('=', $line, 2), 2, '');
    $name = trim($name);
    $value = trim($value, " \t\n\r\0\x0B\"'");

    if ($name === '' || getenv($name) !== false) {
        continue;
    }

    putenv("{$name}={$value}");
    $_ENV[$name] = $value;
}
