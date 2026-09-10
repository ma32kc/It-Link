<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\Car;
use App\Domain\Entity\CarOption;
use DateTimeImmutable;

/**
 * Маппинг строк БД в сущности и обратно.
 */
final class CarMapper
{
    /**
     * @param array<string, mixed> $carRow
     * @param array<string, mixed>|null $optionRow
     */
    public function hydrateCar(array $carRow, ?array $optionRow): Car
    {
        $option = $optionRow !== null ? $this->hydrateOption($optionRow) : null;

        $car = new Car(
            title: (string) $carRow['title'],
            description: (string) $carRow['description'],
            price: (float) $carRow['price'],
            photoUrl: (string) $carRow['photo_url'],
            contacts: (string) $carRow['contacts'],
            createdAt: new DateTimeImmutable((string) $carRow['created_at']),
            option: $option,
        );
        $car->assignId((int) $carRow['id']);

        return $car;
    }

    /**
     * @param array<string, mixed> $row
     */
    public function hydrateOption(array $row): CarOption
    {
        $option = new CarOption(
            brand: (string) $row['brand'],
            model: (string) $row['model'],
            year: (int) $row['year'],
            body: (string) $row['body'],
            mileage: (int) $row['mileage'],
        );
        $option->assignIdentity((int) $row['id'], (int) $row['car_id']);

        return $option;
    }

    /**
     * @return array<string, mixed>
     */
    public function extractCar(Car $car): array
    {
        return [
            'title' => $car->title,
            'description' => $car->description,
            'price' => $car->price,
            'photo_url' => $car->photoUrl,
            'contacts' => $car->contacts,
            'created_at' => $car->createdAt->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function extractOption(CarOption $option, int $carId): array
    {
        return [
            'car_id' => $carId,
            'brand' => $option->brand,
            'model' => $option->model,
            'year' => $option->year,
            'body' => $option->body,
            'mileage' => $option->mileage,
        ];
    }
}
