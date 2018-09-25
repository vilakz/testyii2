<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Booking */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Бронирование', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="booking-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить эту Бронь ?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Добавить Бронь', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'room_id',
                'value' => function ($model) {
                    /** @var $model \app\models\Booking */
                    return $model->room->name;
                }
            ],
            'name',
            'phone',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    /** @var $model \app\models\Booking */
                    return $model->getStatusName();
                }
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
        ],
    ]) ?>

</div>
