<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Booking;
use app\models\Room;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Booking */
/* @var $form yii\widgets\ActiveForm */

$rangeDelimeter = Booking::$rangeDelimiter;
?>

<div class="booking-form">

    <?php $form = ActiveForm::begin([
        'id' => 'bookingForm',
        'enableClientValidation' => false,
    ]); ?>

    <?php if (!$model->fillOnClient) { ?>
    <?= $form->field($model, 'room_id')->widget(Select2::class, [
        'data' => Room::getList(),
        'initValueText' => Room::getList()[$model->room_id] ?? null,
        'options' => [
            'placeholder' => 'Выберите номер',
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'debug' => true,
        ],
    ]) ?>
    <?php } else { ?>
        <?= $form->field($model, 'room_id', ['options' => ['tag' => false], 'template' => '{input}'])->hiddenInput() ?>
    <?php } ?>

    <?= $form->field($model, 'bookingRange')->widget(\kartik\daterange\DateRangePicker::class, [
        'presetDropdown' => true,
        'convertFormat' => false,
        'pluginOptions' => [
            'allowClear' => false,
            'alwaysShowCalendars' => true,
            'opens' => 'center',
            'drops' => 'down',
            'separator' => $rangeDelimeter,
            'format' => 'DD-MM-YYYY HH:00',
            'locale' => [
                'format' => 'DD-MM-YYYY HH:00',
                'cancelLabel' => 'Сбросить',
            ],
            'timePicker' => true,
            'timePicker24Hour' => true,
            'timePickerIncrement' => 60*60,
            'todayHighlight' => true,
        ],
        'pluginEvents' => [
            'cancel.daterangepicker' => 'function() { console.log("cancel.daterangepicker"); }',
            'apply.daterangepicker' => 'function() { console.log("apply.daterangepicker"); var range = bookingParseSetRange($("#booking-bookingrange").val()); console.log("range", [range]);}',
        ],
    ]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?php if (!$model->fillOnClient) { ?>
    <?= $form->field($model, 'status')->widget(Select2::class, [
        'data' => Booking::getStatusList(),
        'initValueText' => Booking::getStatusList()[$model->room_id] ?? null,
        'options' => [
            'placeholder' => 'Выберите статус',
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'debug' => true,
        ],
    ]) ?>
    <?php } ?>

    <?= $form->field($model, 'start', ['options' => ['tag' => false], 'template' => '{input}'])->hiddenInput() ?>

    <?= $form->field($model, 'end', ['options' => ['tag' => false], 'template' => '{input}'])->hiddenInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$strJS = <<<JS
function bookingParseSetRange(value) {
    var ret = null;
    if (value) {
        arr = value.split('$rangeDelimeter');
        if (2 === arr.length) {
            var start = moment(arr[0] + ' +0000', 'DD-MM-YYYY HH:mm Z').unix();
            var end = moment(arr[1] + ' +0000', 'DD-MM-YYYY HH:mm Z').unix();
            if (start && end) {
                $('#booking-start').val(start);
                $('#booking-end').val(end);
                ret = [start, end];
            }
        }
    }
    return ret;
}
JS;
$this->registerJs($strJS);
