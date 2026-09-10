<?php

declare(strict_types=1);

namespace App\Application\Dto;

use App\Domain\Entity\CarOption;
use yii\base\Model;

/**
 * Валидация характеристик. Все поля обязательны.
 */
final class CarOptionForm extends Model
{
    public ?string $brand = null;
    public ?string $model = null;
    public int|string|null $year = null;
    public ?string $body = null;
    public int|string|null $mileage = null;

    public function rules(): array
    {
        return [
            [['brand', 'model', 'year', 'body', 'mileage'], 'required'],
            [['brand', 'model'], 'string', 'max' => 100],
            ['body', 'string', 'max' => 50],
            ['year', 'integer', 'min' => 1900, 'max' => (int) date('Y') + 1],
            ['mileage', 'integer', 'min' => 0],
        ];
    }

    public function toEntity(): CarOption
    {
        return new CarOption(
            brand: (string) $this->brand,
            model: (string) $this->model,
            year: (int) $this->year,
            body: (string) $this->body,
            mileage: (int) $this->mileage,
        );
    }
}
