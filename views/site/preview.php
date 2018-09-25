<?php

/* @var $this \yii\web\View */

use yii\helpers\Html;

/* @var $model \app\models\Room */

$this->title = 'Подробный просмотр номера ' . $model->name;
?>
<div class="site-index">
    <div class="body-content">
        <h1><?= $this->title ?></h1>
        <?= \yii\widgets\DetailView::widget([
            'model' => $model,
            'attributes' => [
                'name',
                'description',
                [
                    'attribute' => 'image',
                    'format' => 'raw',
                    'value' => function ($model) {
                        /** @var $model \app\models\Room */
                        return Html::img($model->getThumbUploadUrl('image'), ['class' => 'img-thumbnail']);
                    },
                ],
            ],
        ]) ?>
        <?= Html::tag('span', 'Забронировать', [
        'class' => ['btn', 'btn-warning', 'js-booking'],
        'data-url' => \yii\helpers\Url::to(['/site/booking', 'id' => $model->id]),
        'data-form' => \yii\helpers\Url::to(['/site/booking-form', 'id' => $model->id]),
        ]); ?>

    </div>
</div>
<?= \app\widgets\BookingWidget::widget([]); ?>
