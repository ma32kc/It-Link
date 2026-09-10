<?php

declare(strict_types=1);

namespace App\Domain\Entity;

/**
 * Технические характеристики объявления. Если задаются, то все поля сразу.
 */
final class CarOption
{
    private ?int $id = null;

    private ?int $carId = null;

    public function __construct(
        public readonly string $brand,
        public readonly string $model,
        public readonly int $year,
        public readonly string $body,
        public readonly int $mileage,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCarId(): ?int
    {
        return $this->carId;
    }

    // id и car_id проставляются после вставки в БД
    public function assignIdentity(int $id, int $carId): void
    {
        $this->id = $id;
        $this->carId = $carId;
    }
}
