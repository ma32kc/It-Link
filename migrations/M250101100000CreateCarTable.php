<?php

declare(strict_types=1);

namespace App\Migrations;

use yii\db\Migration;

final class M250101100000CreateCarTable extends Migration
{
    private const TABLE = '{{%car}}';

    public function safeUp(): void
    {
        $this->createTable(self::TABLE, [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->text()->notNull(),
            'price' => $this->decimal(12, 2)->notNull(),
            'photo_url' => $this->string(1024)->notNull(),
            'contacts' => $this->string(255)->notNull(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-car-created_at', self::TABLE, 'created_at');
    }

    public function safeDown(): void
    {
        $this->dropTable(self::TABLE);
    }
}
