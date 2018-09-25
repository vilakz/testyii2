<?php

use yii\db\Migration;

/**
 * Class m180925_035240_create_room
 */
class m180925_035240_create_room extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('room', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->comment('Название'),
            'description' => $this->string(1024)->comment('Краткое описание'),
            'image' => $this->string()->comment('Фото'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('room');
    }
}
