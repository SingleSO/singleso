<?php

namespace app\controllers;

use Yii;
use app\models\Config;
use app\models\oauth2\Oauth2Code;
use app\models\oauth2\Oauth2Token;
use yii\web\Controller;
use app\models\oauth2\Oauth2;
use app\models\site\ContactForm;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;

class SiteController extends Controller {

	public function actions() {
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
			'captcha' => [
				'class' => 'app\models\captcha\CaptchaAction',
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
			],
		];
	}

	public function actionIndex() {
		return $this->redirectUser(
			['/user/security/login'],
			['/user/settings']
		);
	}

	public function actionRegister() {
		return $this->redirectUser(
			['/user/registration/register'],
			['/user/settings']
		);
	}

	public function actionAbout() {
		$content = Config::setting('page.about.content');
		if (!trim($content)) {
			throw new NotFoundHttpException('Page not found.');
		}
		$page = (object)[
			'content' => $content,
		];
		return $this->render('about', [
			'page' => $page,
		]);
	}

	public function actionContact() {
		$content = Config::setting('page.contact.content');
		if (!trim($content)) {
			throw new NotFoundHttpException('Page not found.');
		}
		$page = (object)[
			'content' => $content,
		];
		$model = new ContactForm();
		if (
			$model->load(Yii::$app->request->post()) &&
			$model->contact(Yii::$app->params['adminEmail'])
		) {
			Yii::$app->session->setFlash('success', Config::setting('page.contact.submitted'));
			return $this->refresh();
		}
		return $this->render('contact', [
			'model' => $model,
			'page' => $page,
		]);
	}

	// TODO: Is the redirect being valid outside the first auth page an issue?
	public function actionFirstRegistrationRedirect() {
		$location = Oauth2::getSessionRedirectLocation(null, false);
		if (!$location) {
			throw new BadRequestHttpException('Redirect expired or invalid.');
		}
		return $this->redirect($location);
	}

	public function redirectUser($noauth, $authdefault) {
		// Get any of supplied redirect params.
		$redirectData = Oauth2::redirectData();

		// Check if user is logged in.
		if (!Yii::$app->user->isGuest) {
			// Create redirect URL using current parameters if specified.
			$redirectURL = Oauth2::getSessionRedirectLocation($redirectData, true);
			// If the parameters created a valid redirect URL, go now.
			if ($redirectURL) {
				return $this->redirect($redirectURL);
			}
			// Failing that, send them to the auth default.
			return $this->redirect($authdefault);
		}

		// Not authenticated, send them to the non-authenticated page.
		// Store redirect information in session data if present.
		// Simplest way to reliably persist across any redirects.
		// Also persists across email activation links.
		if ($redirectData) {
			Oauth2::setSessionRedirect($redirectData);
		}
		return $this->redirect($noauth);
	}
}
