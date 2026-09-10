<?php

declare(strict_types=1);

namespace App\Application\Dto;

use App\Domain\Entity\Car;

/**
 * Страница объявлений с метаданными пагинации.
 */
final class PaginatedCars
{
    /**
     * @param list<Car> $items
     */
    public function __construct(
        public readonly array $items,
        public readonly int $page,
        public readonly int $pageSize,
        public readonly int $totalCount,
    ) {
    }

    public function pageCount(): int
    {
        if ($this->pageSize <= 0) {
            return 0;
        }

        return (int) ceil($this->totalCount / $this->pageSize);
    }

    /**
     * @return array<string, mixed>
     */
    public function toResponse(): array
    {
        return [
            'items' => array_map(CarView::fromEntity(...), $this->items),
            'pagination' => [
                'page' => $this->page,
                'pageSize' => $this->pageSize,
                'totalCount' => $this->totalCount,
                'pageCount' => $this->pageCount(),
            ],
        ];
    }
}
