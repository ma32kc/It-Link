<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use DateTimeImmutable;

/**
 * Объявление о продаже автомобиля. Характеристики (has-one) необязательны.
 */
final class Car
{
    private ?int $id = null;

    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly float $price,
        public readonly string $photoUrl,
        public readonly string $contacts,
        public readonly DateTimeImmutable $createdAt,
        private ?CarOption $option = null,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOption(): ?CarOption
    {
        return $this->option;
    }

    public function hasOption(): bool
    {
        return $this->option !== null;
    }

    // id проставляется после вставки в БД
    public function assignId(int $id): void
    {
        $this->id = $id;
    }
}
