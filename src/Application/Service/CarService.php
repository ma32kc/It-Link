<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Dto\CreateCarForm;
use App\Application\Dto\PaginatedCars;
use App\Application\Exception\ValidationException;
use App\Domain\Entity\Car;
use App\Domain\Repository\CarRepositoryInterface;

/**
 * Бизнес-логика работы с объявлениями.
 */
final class CarService
{
    public function __construct(
        private readonly CarRepositoryInterface $repository,
    ) {
    }

    /**
     * @throws ValidationException при невалидных данных
     */
    public function create(CreateCarForm $form): Car
    {
        if (!$form->validate()) {
            throw new ValidationException($form->getErrors());
        }

        $car = $form->toEntity();
        $this->repository->save($car);

        return $car;
    }

    public function getById(int $id): ?Car
    {
        return $this->repository->findById($id);
    }

    public function list(int $page, int $pageSize): PaginatedCars
    {
        $page = max(1, $page);
        $pageSize = max(1, $pageSize);
        $offset = ($page - 1) * $pageSize;

        $items = $this->repository->findPage($pageSize, $offset);
        $totalCount = $this->repository->count();

        return new PaginatedCars($items, $page, $pageSize, $totalCount);
    }
}
