<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Room */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Список номеров гостиницы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="room-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить этот номер ?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Добавить номер', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'description',
            [
                'attribute' => 'image',
                'format' => 'raw',
                'value' => function ($model) {
                    /** @var $model \app\models\Room */
                    return Html::img($model->getThumbUploadUrl('image'), ['class' => 'img-thumbnail']);
                },
                'filter' => false,
            ],
            [
                'label' => 'Брони',
                'format' => 'raw',
                'value' => function ($model) {
                    /** @var $model \app\models\Room */
                    $dataProvider = new \yii\data\ActiveDataProvider(['query' => $model->getBookings()]);
                    return \kartik\grid\GridView::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => [
                            'id',
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
                    ]);
                },
                'filter' => false,
            ],
        ],
    ]) ?>

</div>
