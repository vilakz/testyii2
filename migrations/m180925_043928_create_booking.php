<?php

use yii\db\Migration;

/**
 * Class m180925_043928_create_booking
 */
class m180925_043928_create_booking extends Migration
{
    const TABLE_NAME = 'booking';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'room_id' => $this->integer()->notNull()->comment('Номер'),
            'name' => $this->string()->comment('Имя клиента'),
            'phone' => $this->string(18)->comment('Телефон'),
            'status' => $this->integer()->defaultValue(0)->comment('Статус'),
            'start' => $this->integer()->unsigned()->comment('Начало брони'),
            'end' => $this->integer()->unsigned()->comment('Окончание брони'),
            'created_at' => $this->integer()->notNull()->comment('Дата добавления'),
        ]);

        $this->addForeignKey('fk_booking_room', self::TABLE_NAME, 'room_id', 'room', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
