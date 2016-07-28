<?php

namespace app\controllers\user;

use Yii;
use app\models\oauth2\Oauth2;
use app\models\user\LogoutForm;
use app\models\user\LogoutAllForm;
use dektrium\user\controllers\SecurityController as BaseSecurityController;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;

class SecurityController extends BaseSecurityController {

	/** @inheritdoc */
	public function behaviors() {
		// Get the parent behaviors.
		$r = parent::behaviors();

		// Remove the forced POST action for the logout.
		unset($r['verbs']['actions']['logout']);

		// Allow both logged in and logged out users to access logout page.
		$r['access']['rules'][] = [
			'allow' => true,
			'actions' => ['logout', 'logout-redirect', 'logout-all'],
			'roles' => ['@', '?'],
		];

		// Return the modified list.
		return $r;
	}

	public function actionLogout() {
		// Throw an error if the user is not logged in.
		$user = Yii::$app->user;
		$isGuest = Yii::$app->user->isGuest;

		// Get the user requesting logout form session if valid.
		$logout_user = Oauth2::getSessionLogoutRedirectUser();

		// We need either a logout user or an active login to continue.
		if (!$logout_user && $isGuest) {
			throw new BadRequestHttpException('No active user session.');
		}

		// If the logout user not set, make it the active user id.
		if (!$logout_user) {
			$logout_user = $user->id;
		}

		// If the active user is the same as the user to logout.
		$active_user_logout = $user->id === $logout_user;

		$model = new LogoutForm();
		$model->load(Yii::$app->request->post());
		$model->active_user_logout = $active_user_logout;
		if ($model->validate()) {
			// If a token is set, remove it.
			Oauth2::clearSessionLogoutRedirectUser();

			// Remember the redirect session data before destroying it.
			$sessionRedirect = Oauth2::getSessionLogoutRedirect();

			// If the active user is being logged out, actually logout.
			if ($active_user_logout) {
				// Trigger the actual logout.
				$event = $this->getUserEvent(Yii::$app->user->identity);
				$this->trigger(self::EVENT_BEFORE_LOGOUT, $event);
				Yii::$app->getUser()->logout();
				$this->trigger(self::EVENT_AFTER_LOGOUT, $event);
			}

			// Set the redirect session again after the logout.
			Oauth2::setSessionLogoutRedirect($sessionRedirect);

			// Create a session token for remembering this ID to logout.
			$token = Oauth2::setLogoutSession($logout_user);

			// Redirect to logout all the services.
			return $this->redirect(['logout-all', 'token' => $token]);
		}

		return $this->render('@app/views/user/logout/prompt', [
			'model' => $model,
		]);
	}

	public function actionLogoutAll() {
		// Check for the token, and that the data in the session matches it.
		$token = Yii::$app->request->get('token');
		$user_id = Oauth2::getLogoutSessionIfValid($token);
		if ($user_id === null) {
			throw new BadRequestHttpException('Invalid token.');
		}

		// Setup the model with the user to logout.
		$model = new LogoutAllForm();
		$model->user_id = $user_id;

		return $this->render('@app/views/user/logout/all', [
			'model' => $model,
		]);
	}

	public function actionLogoutRedirect() {
		// Use logout redirect in session if available, else return home.
		$redirect = Oauth2::getSessionLogoutRedirectLocation();
		return $this->redirect($redirect ? $redirect : ['/']);
	}

	public function actionLogin() {
		// Clear any logout redirect from any previous login.
		Oauth2::setSessionLogoutRedirect(null);

		// Then continue as normal
		return parent::actionLogin();
	}
}
