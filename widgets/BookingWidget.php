<?php
namespace app\widgets;

use yii\base\Widget;

class BookingWidget extends Widget
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('booking');
    }
}