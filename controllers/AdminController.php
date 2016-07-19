<?php

namespace app\controllers;

use Yii;
use app\models\admin\OauthServersForm;
use app\models\authclient\Collection;
use yii\web\Response;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\widgets\ActiveForm;
use dektrium\user\models\Profile;
use app\models\admin\SettingsForm;

class AdminController extends Controller {

	public function actions() {
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
		];
	}

	/** @inheritdoc */
	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
						'matchCallback' => function() {
							return Yii::$app->user->identity->getIsAdmin();
						},
					],
				],
			],
		];
	}

	public function actionIndex() {
		return $this->redirect(['settings']);
	}

	public function actionSettings() {
		$model = new SettingsForm();
		$model->initFromDB();

		$this->performAjaxValidation($model);

		if ($model->load(Yii::$app->request->post()) && $model->saveSettings()) {
			Yii::$app->session->setFlash('success', 'Settings have been updated');
			return $this->refresh();
		}

		// Render the settings page.
		return $this->render('settings', [
			'model' => $model,
		]);
	}

	public function actionOauthServers() {
		$model = new OauthServersForm();
		$model->initFromDB();

		$this->performAjaxValidation($model);

		if ($model->load(Yii::$app->request->post()) && $model->saveServers()) {
			Yii::$app->session->setFlash('success', 'Servers have been updated');
			return $this->refresh();
		}

		return $this->render('oauth-servers', [
			'model' => $model,
		]);
	}

	protected function performAjaxValidation($model) {
		if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
			if ($model->load(Yii::$app->request->post())) {
				Yii::$app->response->format = Response::FORMAT_JSON;
				echo json_encode(ActiveForm::validate($model));
				Yii::$app->end();
			}
		}
	}
}
