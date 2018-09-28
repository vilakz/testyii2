<?php

use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BookingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Бронирование';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="booking-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(['id' => 'pjax-booking']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить Бронь', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax' => true,
        'floatHeader' => true,
        'columns' => [
            'id',
            [
                'class' => kartik\grid\DataColumn::class,
                'attribute' => 'room_id',
                'value' => function ($model) {
                    /** @var $model \app\models\Booking */
                    return $model->room->name;
                },
                'filter' => \app\models\Room::getList(),
                'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => 'Выберите Номер',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ],
                'headerOptions' => ['style' => 'width:150px;'],
            ],
            'name',
            'phone',
            [
                'class' => kartik\grid\DataColumn::class,
                'attribute' => 'status',
                'value' => function ($model) {
                    /** @var $model \app\models\Booking */
                    return $model->getStatusName();
                },
                'filter' => \app\models\Booking::getStatusList(),
                'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => 'Выберите Статус',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ],
                'headerOptions' => ['style' => 'width:150px;'],
            ],
            [
                'attribute' => 'start',
                'value' => function ($model) {
                    /** @var $model \app\models\Booking */
                    $date = new \DateTime('now', new \DateTimeZone('UTC'));
                    $date->setTimestamp($model->start);
                    return $date->format("d.m.Y H:i:s");
                }
            ],
            [
                'attribute' => 'end',
                'value' => function ($model) {
                    /** @var $model \app\models\Booking */
                    $date = new \DateTime('now', new \DateTimeZone('UTC'));
                    $date->setTimestamp($model->end);
                    return $date->format("d.m.Y H:i:s");
                }
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    /** @var $model \app\models\Booking */
                    $date = new \DateTime('now', new \DateTimeZone('UTC'));
                    $date->setTimestamp($model->created_at);
                    return $date->format("d.m.Y H:i:s");
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{approve} {reject} {view} {update} {delete}',
                'buttons' => [
                    'approve' => function ($url, $model) {
                        return Html::a('Одобрить', ['/booking/approve', 'id' => $model->id], ['class' => ['btn', 'btn-primary'], 'data-method' => 'post']);
                    },
                    'reject' => function ($url, $model) {
                        return Html::a('Отклонить', ['/booking/reject', 'id' => $model->id], ['class' => ['btn', 'btn-warning'], 'data-method' => 'post']);
                    },
                ],


            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
