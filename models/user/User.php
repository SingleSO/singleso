<?php

namespace app\models\user;

use Yii;
use yii\base\View;
use dektrium\user\helpers\Password;
use dektrium\user\models\User as BaseUser;
use app\models\Config;

class User extends BaseUser {

	public static $passwordLengthMin = 8;

	// Limit characters to only alpha-numeric, dash, and underscrore.
	// Original allows for @ and ., which makes email vs user ambiguous.
	public static $usernameRegexp = '/^[-a-zA-Z0-9_]+$/';

	// Describe the limit above to the end user.
	public static $usernameRegexpMessage =
		'Username can only contain alpha-numeric, dash, and underscore characters.';

	/** @inheritdoc */
	public function scenarios() {
		$scenarios = parent::scenarios();
		// Add field for the admin screen.
		$scenarios['update'][] = 'is_admin';
		return $scenarios;
	}

	/** @inheritdoc */
	public function rules() {
		$rules = parent::rules();
		$rules = static::addSharedRules($rules);
		// Set the admin rule.
		$rules['is_adminInteger'] = ['is_admin', 'integer', 'min' => 0, 'max' => 1];
		return $rules;
	}

	/** @inheritdoc */
	public function create() {
		// Create a random password, minimum length, but better than most users create.
		if ($this->password === null) {
			$this->password = Password::generate(static::$passwordLengthMin);
		}
		return parent::create();
	}

	public static function addSharedRules($rules) {
		// Add the configurable options.
		$rules['usernameLength'] = ['username', 'string',
			'min' => Config::setting('user.name.length.min'),
			'max' => Config::setting('user.name.length.max'),
		];
		$rules['emailLength'] = ['email', 'string',
			'max' => Config::setting('user.email.length.max'),
		];
		// Also add the regex.
		if (isset($rules['usernamePattern'])) {
			$rules['usernamePattern']['message'] = static::$usernameRegexpMessage;
		}
		// Set password minimum lengths.
		if (isset($rules['passwordLength']['min'])) {
			$rules['passwordLength']['min'] = static::$passwordLengthMin;
		}
		if (isset($rules['newPasswordLength']['min'])) {
			$rules['newPasswordLength']['min'] = static::$passwordLengthMin;
		}
		return $rules;
	}

	/** @inheritdoc */
	public function attributeLabels() {
		$labels = parent::attributeLabels();
		$labels['is_admin'] = Yii::t('user', 'Admin');
		return $labels;
	}

	public static function globalCookieName() {
		// If the parameter is set, set the global cookie flag.
		$params = Yii::$app->params;
		$cookie = 'global.auth.cookie.name';

		return (isset($params[$cookie]) && is_string($params[$cookie]) && $params[$cookie]) ?
			$params[$cookie] : null;
	}

	public static function globalCookieDomain() {
		// Get host if available, else use default empty.
		$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
		if (!isset($host)) {
			return '';
		}

		// Could contain post number, so split that off.
		$host_port = explode(':', $host);
		$host = $host_port[0];

		// If plain IP address, then use default empty.
		if (filter_var($host, FILTER_VALIDATE_IP)) {
			return '';
		}

		// Spit on the dots.
		$host = explode('.', $host);

		// If only 1 segment, use default.
		$hostc = count($host);
		if ($hostc < 2) {
			return '';
		}

		// Use the last 2 segments only, prepending a dot for legacy browsers.
		$host = '.' . $host[$hostc - 2] . '.' . $host[$hostc - 1];

		// Add back port number if present.
		if (isset($host_port[1])) {
			$host .= (':' . $host_port[1]);
		}

		return $host;
	}

	public static function globalCookiePath() {
		return '/';
	}

	public static function globalCookieSecure() {
		return false;
	}

	public static function globalCookieHttponly() {
		return false;
	}

	public static function globalCookieSet($duration = null) {
		if (!($name = static::globalCookieName())) {
			return;
		}

		// 0 means session, else expire time.
		$expire = 0;

		// Use existing remember me expiration if no duration was passed.
		if ($duration === null) {
			$cid = Yii::$app->user->identityCookie['name'];
			if (($cookie = Yii::$app->response->cookies->get($cid))) {
				$expire = $cookie->expire;
			}
		}
		// Else if using duration, set relative to now.
		// Otherwise leave it a session cookie.
		else if ($duration) {
			$expire = time() + $duration;
		}

		// Get the cookie details.
		$path = static::globalCookiePath();
		$domain = static::globalCookieDomain();
		$secure = static::globalCookieSecure();
		$httponly = static::globalCookieHttponly();

		// Set the cookie without the API which adds pointless encryption.
		setcookie($name, '1', $expire, $path, $domain, $secure, $httponly);
	}

	public static function globalCookieClear() {
		if (!($name = static::globalCookieName())) {
			return;
		}

		// If the cookie does not exist, skip clearing.
		if (!isset($_COOKIE[$name])) {
			return;
		}

		// Get time in past to expire this cookie.
		$expire = time() - 3600;

		// Get the cookie details.
		$path = static::globalCookiePath();
		$domain = static::globalCookieDomain();
		$secure = static::globalCookieSecure();
		$httponly = static::globalCookieHttponly();

		// Clear that cookie using the same settings.
		setcookie($name, '0', $expire, $path, $domain, $secure, $httponly);
	}

	public static function userProfileFieldIsShown($field) {
		// Limits the profile fields, and data field length.
		if (Config::setting('user.profile.fields.all')) {
			return true;
		}
		return in_array($field, Config::setting('user.profile.fields'));
	}

	public function getIsAdmin() {
		return $this->is_admin === 1;
	}

	public static function currentUserIsAdmin() {
		if (Yii::$app->user->isGuest) {
			return false;
		}
		return Yii::$app->user->identity->isAdmin;
	}

	public static function usernameBlacklisted($username) {
		$error = 'Blacklisted content in username.';
		$list = Config::setting('user.name.blacklist');
		$usernameLower = strtolower($username);
		foreach ($list as $entry) {
			if ($entry[0] === '/') {
				// Let regex use case insensitive flag if desired.
				if ((bool)preg_match($entry, $username)) {
					return $error;
				}
			}
			else {
				// Chack plain string case-insensitivly.
				if (strpos($usernameLower, strtolower($entry)) !== false) {
					return $error;
				}
			}
		}
		return null;
	}
}
