<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use app\models\oauth2\Oauth2Code;
use app\models\oauth2\CodeForm;
use app\models\oauth2\Oauth2Token;
use app\models\oauth2\TokenForm;

class Oauth2Controller extends Controller {

	public function behaviors() {
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'token' => ['post'],
					'user' => ['post'],
				],
			],
		];
	}

	public function init() {
		parent::init();
		Yii::$app->response->format = Response::FORMAT_JSON;
	}

	public function actionToken() {
		// Load data from post and validate.
		$codeForm = new CodeForm(['scenario' => CodeForm::SCENARIO_VERIFY]);
		$codeForm->load(Yii::$app->request->post(), '');
		if (!$codeForm->validate()) {
			// Throw the first error.
			$error = 'unknown';
			$errors = $codeForm->getErrors();
			foreach ($errors as $ek=>$ev) {
				$error = $ev[0];
				break;
			}
			throw new BadRequestHttpException($error);
		}

		// Attempt to load the code from the database.
		$authCode = Oauth2Code::findCodeOnce($codeForm->code);
		if (!$authCode) {
			throw new BadRequestHttpException('Invalid code');
		}

		// Create new auth token with the user and scope of the token.
		$authToken = new Oauth2Token($authCode->user_id, $authCode->scope);
		$authToken->save();
		$expireTime = $authToken->getExpiration();

		// Create the respone data.
		$data = [
			'access_token' => $authToken->token,
			'token_type' => 'bearer',
		];

		// Add expire durection if it expires.
		if ($expireTime >= 0) {
			$data['expires_in'] = $expireTime;
		}
		return $data;
	}

	public function actionUser() {
		$tokenForm = new TokenForm();
		$tokenForm->load(Yii::$app->request->post(), '');
		if (!$tokenForm->validate()) {
			// Throw the first error.
			$error = 'unknown';
			$errors = $tokenForm->getErrors();
			foreach ($errors as $ek=>$ev) {
				$error = $ev[0];
				break;
			}
			throw new BadRequestHttpException($error);
		}

		// Attempt to load the token from the database.
		$authToken = Oauth2Token::findToken($tokenForm->access_token);
		if (!$authToken) {
			throw new BadRequestHttpException('Invalid access_token');
		}

		// Parse the scope and validate it.
		$scopes = explode(' ', $authToken->scope);
		$scopeUser = in_array('user', $scopes);
		if (!$scopeUser) {
			throw new BadRequestHttpException('Token missing scope: user');
		}
		$scopeEmail = in_array('email', $scopes);
		$scopeProfile = in_array('profile', $scopes);

		// Get the associated user.
		$user = $authToken->user;
		$data = [
			'id' => $user->id,
			'username' => $user->username,
		];
		if ($scopeEmail) {
			$data['email'] = $user->email;
		}
		if ($scopeProfile) {
			$userClass = Yii::$app->modules['user']->modelMap['User'];
			$profile = $user->profile;
			$pk = $profile->primaryKey()[0];
			$attrs = array_keys($profile->attributes);
			foreach ($attrs as $attr) {
				// Skip the primary key.
				if ($attr === $pk) {
					continue;
				}
				// Check if the field is available.
				if (!$userClass::userProfileFieldIsShown($attr)) {
					continue;
				}
				// Get value, skip null.
				$val = $profile->{$attr};
				if (!isset($val)) {
					continue;
				}
				$data[$attr] = $val;
			}
		}
		return $data;
	}
}
