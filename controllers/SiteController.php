<?php

namespace app\controllers;

use app\models\Booking;
use app\models\Room;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'booking-form' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider(['query' => Room::find()]);
        return $this->render('index', compact('dataProvider'));
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Предосмотр комнаты для бронирования
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPreview($id)
    {
        $model = Room::find()->andWhere(['id' => $id])->one();
        if (!$model) {
            throw new NotFoundHttpException('Такая комната не найдена');
        }
        return $this->render('preview', compact('model'));
    }

    /**
     * Вывод диалога бронирования
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionBooking($id)
    {
        $room = Room::find()->andWhere(['id' => $id])->one();
        if (!$room) {
            throw new NotFoundHttpException('Такая комната не найдена');
        }
        $model = new Booking();
        $model->room_id = $room->id;
        $model->status = Booking::STATUS_NEW;

        $date = new \DateTime('now', new \DateTimeZone('UTC'));
        $date->setTime($date->format("H"), 0);
        $model->start = $date->getTimestamp();

        $date->modify("+4 Hour");
        $model->end = $date->getTimestamp();
        $model->fillBookingRange();

        $model->fillOnClient = true;

        return $this->renderAjax('/booking/_form', compact('model'));
    }

    /**
     * Валидация и сохранение новой брони
     * @param int $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionBookingForm($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $room = Room::find()->andWhere(['id' => $id])->one();
        if (!$room) {
            throw new NotFoundHttpException('Такая комната не найдена');
        }
        $model = new Booking();
        $model->room_id = $room->id;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            \Yii::info(\yii\helpers\VarDumper::dumpAsString(compact('model')));
            if ($model->save()) {
                $ret = ['result' => true];
            } else {
                $ret = ['errors' => $model->getErrors()];
            }
        } else {
            $ret = ['errors' => $model->getErrors()];
        }

        return $ret;
    }
}
