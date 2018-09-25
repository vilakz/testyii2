<?php

/* @var $this yii\web\View */

use yii\bootstrap\Modal;
use yii\helpers\Html;

/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = 'Система бронирования номеров';
?>
<div class="site-index">

    <div class="body-content">

        <?= \kartik\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'name',
                'description',
                [
                    'attribute' => 'image',
                    'format' => 'raw',
                    'value' => function ($model) {
                        /** @var $model \app\models\Room */
                        return Html::a(
                                Html::img($model->getThumbUploadUrl('image'), ['class' => 'img-thumbnail']),
                            $model->getUploadUrl('image'), ['target' => '_blank']);
                    },
                    'filter' => false,
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{preview} {booking}',
                    'buttons' => [
                        'preview' => function ($url, $model) {
                            return Html::a('Подробнее', ['/site/preview', 'id' => $model->id], ['class' => ['btn', 'btn-primary']]);
                        },
                        'booking' => function ($url, $model) {
                            return Html::tag('span', 'Забронировать', [
                                'class' => ['btn', 'btn-warning', 'js-booking'],
                                'data-url' => \yii\helpers\Url::to(['/site/booking', 'id' => $model->id]),
                                'data-form' => \yii\helpers\Url::to(['/site/booking-form', 'id' => $model->id]),
                            ]);
                        },
                    ],
                ],
            ],
        ]) ?>
    </div>
</div>
<?= \app\widgets\BookingWidget::widget([]); ?>