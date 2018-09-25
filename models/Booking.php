<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "booking".
 *
 * @property int $id
 * @property int $room_id Номер
 * @property string $name Имя клиента
 * @property string $phone Телефон
 * @property int $status Статус
 * @property int $start Начало брони
 * @property int $end Окончание брони
 * @property int $created_at Дата добавления
 *
 * @property Room $room
 */
class Booking extends \yii\db\ActiveRecord
{
    /**
     * Новая броня
     */
    const STATUS_NEW = 0;

    /**
     * Одобренная броня
     */
    const STATUS_APPROVED = 1;

    /**
     * Отклоненная броня
     */
    const STATUS_REJECTED = 2;

    /**
     * Для выбора периода бронирования
     * @var string
     */
    public $bookingRange;

    /**
     * Разделитель пероида выбора бронирования
     * @var string
     */
    public static $rangeDelimiter = ' - ';

    /**
     * Заполняется на строне клиента или в админке
     * @var bool
     */
    public $fillOnClient = false;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'booking';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['room_id', 'name', 'phone'], 'required'],
            [['room_id', 'status', 'start', 'end', 'created_at'], 'integer'],
            [['name'], 'string', 'max' => 255, 'min' => 2, 'message' => 'Длинна от 2 до 255 символов'],
            [['name'], 'match', 'pattern' => '/^[a-zA-Zа-яА-Я\- ]+$/iu', 'message' => 'только кириллический и латинские символы, пробелы, дефисы'],
            [['phone'], 'string', 'max' => 18],
            [['phone'], 'match', 'pattern' => '/^\+\d+$/'],
            [['room_id'], 'exist', 'skipOnError' => true, 'targetClass' => Room::class, 'targetAttribute' => ['room_id' => 'id']],
            [['status'], 'in', 'range' => array_keys(static::getStatusList())],
            [['bookingRange'], 'checkBookingRange', 'skipOnEmpty' => false],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'room_id' => 'Номер',
            'name' => 'Имя клиента',
            'phone' => 'Телефон',
            'status' => 'Статус',
            'start' => 'Начало брони',
            'end' => 'Окончание брони',
            'created_at' => 'Дата добавления',
            'bookingRange' => 'Период бронирования',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->fillBookingRange();
    }

    /**
     * Заполнить bookingRange по данным start, end
     */
    public function fillBookingRange()
    {
        $dateStart = new \DateTime('now', new \DateTimeZone('UTC'));
        $dateStart->setTimestamp($this->start);

        $dateEnd = new \DateTime('now', new \DateTimeZone('UTC'));
        $dateEnd->setTimestamp($this->end);
        $this->bookingRange = $dateStart->format("d-m-Y H:i") . static::$rangeDelimiter . $dateEnd->format("d-m-Y H:i");
    }

    /**
     * Валидация периода бронирования
     * @param string $attribute Имя атрибута
     * @param array $params
     * @return boolean
     */
    public function checkBookingRange($attribute, $params)
    {
        $ret = false;

        if ($this->start && !$this->end) {
            $this->addError( 'bookingRange', "Необходимо заполнить дату окончания бронирования" );
        } else if (!$this->start && $this->end) {
            $this->addError( 'bookingRange', "Необходимо заполнить дату начала бронирования" );
        } else if (!($this->start || $this->end)) {
            $this->addError( 'bookingRange', "Необходимо заполнить дату начала и окончания бронирования" );
        } else {
            // даты есть
            if ($this->start > $this->end) {
                $this->addError( 'bookingRange', "Дата начала бронирования должна быть раньше даты окончания" );
            } else {
                do {
                    if (0 != $this->start % (60 * 60)) {
                        $this->addError( 'bookingRange', "Дата начала бронирования должна быть с периодом в час" );
                        break; // exit do-while
                    }

                    if (0 != $this->end % (60 * 60)) {
                        $this->addError( 'bookingRange', "Дата окончания бронирования должна быть с периодом в час" );
                        break; // exit do-while
                    }

                    //@todo: проверка на отсутствие бронирования в этом же интервале
                    $count = static::find()->andWhere(['and',
                        ['room_id' => $this->room_id],
                        ['not', ['id' => $this->id]],
                        ['status' => [static::STATUS_APPROVED, static::STATUS_NEW]],
                            ['or',
                                ['and',
                                    ['>=', 'start', $this->start],
                                    ['<', 'start', $this->end],
                                ],
                                ['and',
                                    ['<=', 'end', $this->end],
                                    ['>', 'end', $this->start],
                                ],
                                ['and',
                                    ['<', 'start', $this->end],
                                    ['>', 'end', $this->start],
                                ],
                                ['and',
                                    ['>=', 'start', $this->start],
                                    ['<=', 'end', $this->end],
                                ],
                            ],
                    ])->count();
                    if (!$count) {
                        $ret = true;
                    } else {
                        $this->addError( 'bookingRange', "Бронь с входящим в Ваш интервал периодом уже есть" );
                    }
                } while(false);
            }
        }
        return $ret;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoom()
    {
        return $this->hasOne(Room::className(), ['id' => 'room_id']);
    }

    /**
     * Получить список статусов. Ключ - код статуса, значение - название статуса
     * @return array
     */
    public static function getStatusList()
    {
        $ret = [
            static::STATUS_NEW => 'Новая',
            static::STATUS_APPROVED => 'Одобренная',
            static::STATUS_REJECTED => 'Отклоненная',
        ];
        return $ret;
    }

    /**
     * Получить текстовое представление текущего Статуса
     * @return string
     */
    public function getStatusName()
    {
        return static::getStatusList()[$this->status];
    }
}
