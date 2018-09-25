<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\RoomSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Список номеров гостиницы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="room-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить номер', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'floatHeader' => true,
        'columns' => [

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
                'label' => '<span title="Число новых/одобренных/отказанных броней" class="glyphicon glyphicon-stats"></span>',
                'encodeLabel' => false,
                'value' => function ($model) {
                    /** @var $model \app\models\Room */
                    //@todo: можно оптимизировать, чтобы было 3 запроса на страницу, сейчас без оптимизации
                    return $model->getNewBookings() . ' / '. $model->getApprovedBookings() . ' / '. $model->getRejectedBookings();
                },
                'filter' => false,
                'enableSorting' => false,
],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
