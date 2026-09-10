<?php

declare(strict_types=1);

namespace App\Migrations;

use yii\db\Migration;

// has-one к car: FK + уникальный индекс по car_id
final class M250101100100CreateCarOptionTable extends Migration
{
    private const TABLE = '{{%car_option}}';
    private const CAR_TABLE = '{{%car}}';

    public function safeUp(): void
    {
        $this->createTable(self::TABLE, [
            'id' => $this->primaryKey(),
            'car_id' => $this->integer()->notNull(),
            'brand' => $this->string(100)->notNull(),
            'model' => $this->string(100)->notNull(),
            'year' => $this->integer()->notNull(),
            'body' => $this->string(50)->notNull(),
            'mileage' => $this->integer()->notNull(),
        ]);

        $this->createIndex('uq-car_option-car_id', self::TABLE, 'car_id', true);

        $this->addForeignKey(
            'fk-car_option-car_id',
            self::TABLE,
            'car_id',
            self::CAR_TABLE,
            'id',
            'CASCADE',
            'CASCADE',
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk-car_option-car_id', self::TABLE);
        $this->dropTable(self::TABLE);
    }
}
