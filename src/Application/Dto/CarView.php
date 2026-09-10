<?php

declare(strict_types=1);

namespace App\Application\Dto;

use App\Domain\Entity\Car;
use App\Domain\Entity\CarOption;

/**
 * Сериализация сущности в ответ API.
 */
final class CarView
{
    /**
     * @return array<string, mixed>
     */
    public static function fromEntity(Car $car): array
    {
        return [
            'id' => $car->getId(),
            'title' => $car->title,
            'description' => $car->description,
            'price' => $car->price,
            'photo_url' => $car->photoUrl,
            'contacts' => $car->contacts,
            'created_at' => $car->createdAt->format(DATE_ATOM),
            'options' => self::renderOption($car->getOption()),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function renderOption(?CarOption $option): ?array
    {
        if ($option === null) {
            return null;
        }

        return [
            'brand' => $option->brand,
            'model' => $option->model,
            'year' => $option->year,
            'body' => $option->body,
            'mileage' => $option->mileage,
        ];
    }
}
