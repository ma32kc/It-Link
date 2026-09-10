<?php

declare(strict_types=1);

namespace App\Application\Exception;

use RuntimeException;

/**
 * Ошибки валидации по полям (для ответа 422).
 */
final class ValidationException extends RuntimeException
{
    /**
     * @param array<string, list<string>> $errors
     */
    public function __construct(private readonly array $errors)
    {
        parent::__construct('Переданные данные не прошли валидацию.');
    }

    /**
     * @return array<string, list<string>>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
