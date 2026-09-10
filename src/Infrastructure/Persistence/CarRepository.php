<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\Car;
use App\Domain\Repository\CarRepositoryInterface;
use RuntimeException;
use Throwable;
use yii\db\Connection;
use yii\db\Query;

/**
 * Хранилище на Yii2 DAO, без ActiveRecord. Маппинг строк делает CarMapper.
 */
final class CarRepository implements CarRepositoryInterface
{
    private const CAR_TABLE = 'car';
    private const OPTION_TABLE = 'car_option';

    private readonly CarMapper $mapper;

    public function __construct(
        private readonly Connection $db,
        ?CarMapper $mapper = null,
    ) {
        $this->mapper = $mapper ?? new CarMapper();
    }

    public function save(Car $car): void
    {
        $transaction = $this->db->beginTransaction();

        try {
            $carPk = $this->db->getSchema()->insert(self::CAR_TABLE, $this->mapper->extractCar($car));

            if ($carPk === false) {
                throw new RuntimeException('Не удалось сохранить объявление.');
            }

            $carId = (int) $carPk['id'];
            $car->assignId($carId);

            $option = $car->getOption();
            if ($option !== null) {
                $optionPk = $this->db->getSchema()->insert(
                    self::OPTION_TABLE,
                    $this->mapper->extractOption($option, $carId),
                );

                if ($optionPk === false) {
                    throw new RuntimeException('Не удалось сохранить характеристики объявления.');
                }

                $option->assignIdentity((int) $optionPk['id'], $carId);
            }

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function findById(int $id): ?Car
    {
        $carRow = (new Query())
            ->from(self::CAR_TABLE)
            ->where(['id' => $id])
            ->one($this->db);

        if ($carRow === false) {
            return null;
        }

        $optionRow = (new Query())
            ->from(self::OPTION_TABLE)
            ->where(['car_id' => $id])
            ->one($this->db);

        return $this->mapper->hydrateCar($carRow, $optionRow ?: null);
    }

    public function findPage(int $limit, int $offset): array
    {
        $carRows = (new Query())
            ->from(self::CAR_TABLE)
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all($this->db);

        if ($carRows === []) {
            return [];
        }

        $carIds = array_map(static fn (array $row): int => (int) $row['id'], $carRows);
        $optionsByCarId = $this->loadOptionsIndexedByCarId($carIds);

        return array_map(
            fn (array $carRow): Car => $this->mapper->hydrateCar(
                $carRow,
                $optionsByCarId[(int) $carRow['id']] ?? null,
            ),
            $carRows,
        );
    }

    public function count(): int
    {
        return (int) (new Query())->from(self::CAR_TABLE)->count('*', $this->db);
    }

    /**
     * Характеристики для списка объявлений одним запросом, чтобы не ловить N+1.
     *
     * @param list<int> $carIds
     * @return array<int, array<string, mixed>>
     */
    private function loadOptionsIndexedByCarId(array $carIds): array
    {
        $rows = (new Query())
            ->from(self::OPTION_TABLE)
            ->where(['car_id' => $carIds])
            ->all($this->db);

        $indexed = [];
        foreach ($rows as $row) {
            $indexed[(int) $row['car_id']] = $row;
        }

        return $indexed;
    }
}
