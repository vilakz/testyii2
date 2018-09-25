<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "room".
 *
 * @property int $id
 * @property string $name Название
 * @property string $description Краткое описание
 * @property string $image Фото
 */
class Room extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'room';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 1024],
            ['image', 'image', 'extensions' => 'jpg, jpeg, gif, png', 'on' => ['insert', 'update']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'description' => 'Краткое описание',
            'image' => 'Фото',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \mohorev\file\UploadImageBehavior::class,
                'attribute' => 'image',
                'scenarios' => ['insert', 'update'],
                'placeholder' => '@app/web/NoImage.png',
                'path' => '@webroot/upload/room/{id}',
                'url' => '@web/upload/room/{id}',
                'thumbs' => [
                    'thumb' => ['width' => 200, 'quality' => 200],
                    'preview' => ['width' => 400, 'height' => 400],
                ],
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookings()
    {
        return $this->hasMany(Booking::class, ['room_id' => 'id']);
    }

    /**
     * Получить массив номеров в виде списка. Ключ - id номера, значение - название
     * @return array
     */
    public static function getList()
    {
        return static::find()->select(['name', 'id'])->indexBy('id')->column();
    }

    /**
     * Получить число новых броней
     * @return int
     */
    public function getNewBookings()
    {
        return $this->getBookings()->andWhere(['status' => Booking::STATUS_NEW])->count();
    }

    /**
     * Получить число одобреных броней
     * @return int
     */
    public function getApprovedBookings()
    {
        return $this->getBookings()->andWhere(['status' => Booking::STATUS_APPROVED])->count();
    }

    /**
     * Получить число отказаных броней
     * @return int
     */
    public function getRejectedBookings()
    {
        return $this->getBookings()->andWhere(['status' => Booking::STATUS_REJECTED])->count();
    }
}
