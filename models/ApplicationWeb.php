<?php

namespace app\models;

use Yii;
use app\models\Config;
use yii\web\Application;

class ApplicationWeb extends Application {
	public function __construct($config) {
		parent::__construct($config);

		// Set name from database if not set by config file.
		if (!isset($config['name'])) {
			if (($name = Config::setting('application.name'))) {
				$this->name = $name;
			}
		}

		// Set email from database if not set by config file.
		if (!isset(Yii::$app->params['adminEmail'])) {
			Yii::$app->params['adminEmail'] = null;
			if (($email = Config::setting('application.admin.email'))) {
				Yii::$app->params['adminEmail'] = $email;
			}
		}

		// Setup options for the user module.
		$user = Yii::$app->getModule('user');
		$user->enableRegistration = Config::setting('user.registration.enabled');
		$user->enableConfirmation = Config::setting('user.registration.confirmation');
		$user->enableUnconfirmedLogin = Config::setting('user.registration.unconfirmed.login');
		$user->enablePasswordRecovery = Config::setting('user.registration.password.recovery');
		$user->rememberFor = Config::setting('user.login.remember.time');
		$user->confirmWithin = Config::setting('user.registration.confirm.time');
		$user->recoverWithin = Config::setting('user.registration.recover.time');

		// Add some event listeners to the app user.
		$appUser = Yii::$app->user;
		$appUser->on($appUser::EVENT_AFTER_LOGIN, function($e) use ($appUser) {
			$class = $appUser->identityClass;
			$class::globalCookieSet($e->duration);
		});
		$appUser->on($appUser::EVENT_AFTER_LOGOUT, function($e) use ($appUser) {
			$class = $appUser->identityClass;
			$class::globalCookieClear();
		});

		$appUserClass = $appUser->identityClass;
		if ($appUser->isGuest) {
			$appUserClass::globalCookieClear();
		}
		else {
			$appUserClass::globalCookieSet();
		}
	}
}
