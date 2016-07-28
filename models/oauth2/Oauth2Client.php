<?php

namespace app\models\oauth2;

use app;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Oauth2Client extends ActiveRecord {

	// 6 hours in seconds.
	const LOGOUT_TIMEOUT = 21600;

	/** @inheritdoc */
	public static function tableName() {
		return '{{%oauth2_client}}';
	}

	/** @inheritdoc */
	public function behaviors() {
		return [
			TimestampBehavior::className(),
		];
	}

	/** @inheritdoc */
	public function rules() {
		return [
			'clientLength' => ['client', 'string', 'min' => 1, 'max' => 255],
			'clientTrim' => ['client', 'trim'],
			'clientRequired' => ['client', 'required'],

			'client_nameLength' => ['client_name', 'string', 'min' => 1, 'max' => 255],
			'client_nameTrim' => ['client_name', 'trim'],
			'client_nameRequired' => ['client_name', 'required'],

			'client_secretLength' => ['client_secret', 'string', 'max' => 255],
			'client_secretTrim' => ['client_secret', 'trim'],

			'scopesLength' => ['scopes', 'string', 'max' => 255],
			'scopesTrim' => ['scopes', 'trim'],
			'scopesFormat' => ['scopes', 'validateFormat'],

			'redirect_urisLength' => ['redirect_uris', 'string'],
			'redirect_urisTrim' => ['redirect_uris', 'trim'],

			'logout_uriLength' => ['logout_uri', 'string'],
			'logout_uriTrim' => ['logout_uri', 'trim'],
		];
	}

	public function generateSecret() {
		$this->client_secret = Yii::$app->getSecurity()->generateRandomString(64);
	}

	public function getRedirectURIsList() {
		$uris = $this->redirect_uris;
		return $uris ? array_filter(preg_split('/\s+/', $uris)) : [];
	}

	public function getScopesList() {
		$scopes = $this->scopes;
		return $scopes ?
			array_filter(preg_split('/[^a-z0-9-_\.]+/i', (string)$scopes)) : [];
	}

	public function validateRedirectURI($value) {
		// Only exact matches allowed, avoids directory traversal tricks.
		return $value && in_array($value, $this->getRedirectURIsList());
	}

	public function validateLogoutURI($value) {
		return $value && $value === $this->logout_uri;
	}

	public function validateFormat($attribute, $params) {
		$this->{$attribute} = implode(' ', $this->scopesList);
	}

	public function logoutTokenGenerate($user_id, $params = []) {
		// Create the expiration time.
		$expiration = time() + self::LOGOUT_TIMEOUT;

		// Create the token and add to the parameters.
		$params['token'] = $expiration . '|' . $user_id . '|' .
			static::logoutTokenHash(
				$expiration,
				$user_id,
				$this->client_secret
			);

		// Return the complete URL.
		return $this->logout_uri .
			(parse_url($this->logout_uri, PHP_URL_QUERY) ? '&' : '?') .
			http_build_query($params);
	}

	public function logoutTokenVerify($token) {
		$parts = explode('|', $token);
		$expires = isset($parts[0]) ? (int)$parts[0] : null;
		$user_id = isset($parts[1]) ? (int)$parts[1] : null;
		$hash = isset($parts[2]) ? $parts[2] : null;

		// Check that all parts are set.
		if (!$expires || !$user_id || !$hash) {
			return null;
		}

		// Hash them all together.
		$hashed = static::logoutTokenHash(
			$expires,
			$user_id,
			$this->client_secret
		);

		// Sanity check.
		if (!$hashed) {
			return null;
		}

		// Check if valid using best hash checking available.
		$valid = function_exists('hash_equals') ?
			hash_equals($hash, $hashed) :
			($hash === $hashed);

		// If valid, return the user ID, else null.
		return $valid ? $user_id : null;
	}

	public function logoutTokenHash($expiration, $user_id, $secret) {
		return hash('sha512', $expiration . '|' . $user_id . '|' . $secret);
	}

	public static function getAllLogoutGroupedValid() {
		$all = static::allServers();
		$r = [
			'valid' => [],
			'invalid' => []
		];
		foreach ($all as $server) {
			// If logout is a valid URL, add it to the logout list.
			if (filter_var($server->logout_uri, FILTER_VALIDATE_URL)) {
				$r['valid'][] = $server;
			}
			else {
				$r['invalid'][] = $server;
			}
		}
		return $r;
	}

	public static function allServers() {
		return static::find()->all();
	}
}
