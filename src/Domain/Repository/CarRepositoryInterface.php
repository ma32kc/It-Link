<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Car;

interface CarRepositoryInterface
{
    // сохраняет объявление вместе с характеристиками и проставляет id
    public function save(Car $car): void;

    public function findById(int $id): ?Car;

    /**
     * Страница объявлений, новые первыми.
     *
     * @return list<Car>
     */
    public function findPage(int $limit, int $offset): array;

    public function count(): int;
}
