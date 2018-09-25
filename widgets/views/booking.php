<?php

/* @var $this \yii\web\View */

use yii\bootstrap\Modal;

Modal::begin([
    'header' => '<h2>Бронирование</h2>',
    'id' => 'bookingModal',
]);

echo 'Say hello...';
Modal::end();

$strJS = <<<JS
$(document).on('click', '.js-booking', function(e) {
    var formUrl = $(this).data('url');
    var formSendUrl = $(this).data('form');
    console.log('js-booking click ', [formUrl]);
    $('#bookingModal .modal-body').load(formUrl, function( response, status, xhr ) {
        if ( status != "error" ) {
            $('#bookingModal').modal();
            $('#bookingForm').off('submit');
            $('#bookingForm').on('submit', function(event) {
                event.preventDefault();
                $.ajax({
                    url: formSendUrl,
                    data: $('#bookingForm').serializeArray(),
                    method: 'POST'
                })
                .done(function(data){
                    console.log('ajax submit done', [data]);
                    if (data.result) {
                        //@todo: успешно добавлен
                        $('#bookingModal .modal-body').html('<h4>Запрос на бронь успешно добавлен</h4>');
                    } else if (data.errors) {
                        var form = $('#bookingForm');
                        form.find('.form-group.has-error').removeClass('has-error');
                        form.find('.form-group .help-block').html("");

                        $.each(data.errors, function (index, item) {
                            console.log('data.errors index, item ', [data.errors, index, item]);
                            form.find('.field-booking-' + index.toLowerCase()).addClass('has-error');
                            form.find('.field-booking-' + index.toLowerCase() + ' .help-block').html(item);
                        });
                        
                    }
                })
            });
        }
    });
    
});
JS;
$this->registerJs($strJS);
