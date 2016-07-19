<?php

namespace app\models\oauth2;

use app;
use Yii;
use app\models\oauth2\CodeForm;
use app\models\oauth2\Oauth2Client;
use yii\base\Model;
use yii\web\BadRequestHttpException;
use app\models\oauth2\Oauth2Code;

class Oauth2 extends Model {

	const SSO_REDIRECT_SESSION_KEY = 'sso.redirect';
	const SSO_REDIRECT_NO_AUTO_KEY = '_no_auto';

	public static function redirectData() {
		$request = Yii::$app->request;
		// Get any passed redirect parameters.
		$redirect_uri = $request->get('redirect_uri');
		$client_id = $request->get('client_id');
		// Check for minimal params.
		if ($redirect_uri && $client_id) {
			return [
				'redirect_uri' => $redirect_uri,
				'client_id' => $client_id,
				'scope' => $request->get('scope'),
				'state' => $request->get('state'),
			];
		}
		return null;
	}

	public static function setSessionRedirect($data) {
		// If null, remove, else set new.
		if ($data === null) {
			unset(Yii::$app->session[static::SSO_REDIRECT_SESSION_KEY]);
		}
		else {
			Yii::$app->session[static::SSO_REDIRECT_SESSION_KEY] = $data;
		}
	}

	public static function getSessionRedirect() {
		return Yii::$app->session[static::SSO_REDIRECT_SESSION_KEY];
	}

	/**
	 * Get the redirect URL using either data if valid, or session data.
	 * Existing session data will be cleared on call, even with valid data.
	 * This is to ensure that session-based redirects only happen once.
	 * Also handles all validation and token code creation.
	 *
	 * @param array|null $data Data to use if valid.
	 * @return string|null Redirect URI with parameters, or null.
	 */
	public static function getSessionRedirectLocation($data = null, $auto = true) {
		// Only works if authenticated.
		if (Yii::$app->user->isGuest) {
			return null;
		}
		$user = Yii::$app->user->identity;

		// If data does not contain cliend_id, load data from session.
		if (!isset($data['client_id'])) {
			// Read data from session, then clear.
			$data = static::getSessionRedirect();
		}

		// If must allow auto and set to not do auto, return null.
		if ($auto && isset($data[static::SSO_REDIRECT_NO_AUTO_KEY])) {
			return null;
		}

		// Remove any session redirect on auto, only ever redirect once, code expires.
		static::setSessionRedirect(null);

		// Check if data contains the minimal parameters before continuing.
		if (!isset($data['client_id'], $data['redirect_uri'])) {
			return null;
		}

		$model = new CodeForm(['scenario' => CodeForm::SCENARIO_CREATE]);
		$model->load($data, '');
		if (!$model->validate()) {
			// Throw the first error.
			$error = 'unknown';
			$errors = $model->getErrors();
			foreach ($errors as $ek=>$ev) {
				$error = $ev[0];
				break;
			}
			throw new BadRequestHttpException($error);
		}
		// Create single use code for the user and the scope.
		$authcode = new Oauth2Code($user->id, $model->scope);
		$authcode->save();
		// Set the code on the model and generate the URL.
		$model->code = $authcode->code;
		return $model->redirectURL();
	}

	public static function setSessionNoAuto() {
		// Add a new property to the data array.
		$data = static::getSessionRedirect();
		if (is_array($data)) {
			$data[static::SSO_REDIRECT_NO_AUTO_KEY] = 1;
			static::setSessionRedirect($data);
		}
	}

	public static function getSessionRedirectName() {
		$data = static::getSessionRedirect();
		if (!isset($data['client_id'], $data['redirect_uri'])) {
			return null;
		}
		$client = Oauth2Client::findOne($data['client_id']);
		if (!$client) {
			return null;
		}
		return $client->client_name;
	}
}
