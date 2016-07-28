<?php

namespace app\models\admin;

use Yii;
use Exception;
use app\models\Config;
use app\models\Theme;
use dektrium\user\models\Profile;
use yii\base\Model;
use yii\helpers\Url;

class SettingsForm extends Model {

	public $application_admin_email;
	public $application_home_url;
	public $application_name;
	public $application_copyright;
	public $application_theme;

	public $user_profile_fields_all;
	public $user_profile_fields;
	public $user_name_length_max;
	public $user_name_length_min;
	public $user_email_length_max;
	public $user_name_blacklist;
	public $user_registration_enabled;
	public $user_registration_confirmation;
	public $user_registration_unconfirmed_login;
	public $user_registration_password_recovery;
	public $user_registration_confirm_time;
	public $user_registration_recover_time;
	public $user_login_remember_time;

	public $oauth2_code_expire;
	public $oauth2_token_expire;

	public $oauth2_loginurl;
	public $oauth2_registerurl;
	public $oauth2_logouturl;
	public $oauth2_endpoint;
	public $oauth2_domain_global_cookie_name;

	public $page_about_content;
	public $page_contact_content;
	public $page_contact_submitted;
	public $page_links;

	public function rules() {
		return [
			'application_admin_emailEmail' => ['application_admin_email', 'email'],
			'application_home_urlLength' => ['application_home_url', 'string', 'max' => 65000],
			'application_nameLength' => ['application_name', 'string', 'max' => 65000],
			'application_copyrightLength' => ['application_copyright', 'string', 'max' => 65000],
			'application_themeLength' => ['application_theme', 'string', 'max' => 65000],

			'user_registration_enabledValidate' => ['user_registration_enabled', 'boolean'],
			'user_registration_confirmationValidate' => ['user_registration_confirmation', 'boolean'],
			'user_registration_unconfirmed_loginValidate' => ['user_registration_unconfirmed_login', 'boolean'],
			'user_registration_password_recoveryValidate' => ['user_registration_password_recovery', 'boolean'],
			'user_registration_confirm_timeValidate' => ['user_registration_confirm_time', 'integer', 'min' => 0],
			'user_registration_recover_timeValidate' => ['user_registration_recover_time', 'integer', 'min' => 0],
			'user_login_remember_timeValidate' => ['user_login_remember_time', 'integer', 'min' => 0],
			'user_profile_fieldsArray' => ['user_profile_fields', 'each', 'rule' => ['string']],
			'user_profile_fields_allBoolean' => ['user_profile_fields_all', 'boolean'],
			'user_name_length_maxInteger' => ['user_name_length_max', 'integer', 'min' => 1, 'max' => 255],
			'user_name_length_maxRequired' => ['user_name_length_max', 'required'],
			'user_name_length_minInteger' => ['user_name_length_min', 'integer', 'min' => 1, 'max' => 255],
			'user_name_length_minRequired' => ['user_name_length_min', 'required'],
			'user_email_length_maxInteger' => ['user_email_length_max', 'integer', 'min' => 1, 'max' => 255],
			'user_email_length_maxRequired' => ['user_email_length_max', 'required'],
			'user_name_blacklistLength' => ['user_name_blacklist', 'string', 'max' => 65000],
			'user_name_blacklistValidate' => ['user_name_blacklist', 'validateBlacklist'],

			'oauth2_code_expireLength' => ['oauth2_code_expire', 'string', 'max' => 65000],
			'oauth2_code_expireInteger' => ['oauth2_code_expire', 'integer'],
			'oauth2_code_expireRequired' => ['oauth2_code_expire', 'required'],
			'oauth2_token_expireLength' => ['oauth2_token_expire', 'string', 'max' => 65000],
			'oauth2_token_expireInteger' => ['oauth2_token_expire', 'integer'],
			'oauth2_token_expireRequired' => ['oauth2_token_expire', 'required'],

			'page_about_contentLength' => ['page_about_content', 'string', 'max' => 65000],
			'page_contact_contentLength' => ['page_contact_content', 'string', 'max' => 65000],
			'page_contact_submittedLength' => ['page_contact_submitted', 'string', 'max' => 65000],
			'page_linksLength' => ['page_links', 'string', 'max' => 65000],
		];
	}

	public function validateBlacklist($attribute, $params) {
		$value = $this->{$attribute};
		$lines = array_filter(preg_split('/[\r\n]+/', $value));
		$validated = [];
		foreach ($lines as $line) {
			$entry = trim($line);
			// If a regex, try to use it as one and catch any errors.
			if ($entry[0] === '/') {
				// Error reporting is the only way to validate a regex.
				$error_reporting = @error_reporting();
				@error_reporting(E_ALL);
				try {
					preg_match($entry, '');
				}
				catch (Exception $e) {
					// Convert exceptions to a validation error.
					$this->addError($attribute,
						sprintf(
							'Invalid regex: "%s": %s',
							$entry,
							str_replace('preg_match(): ', '', $e->getMessage())
						)
					);
				}
				@error_reporting($error_reporting);
			}
			$validated[] = $entry;
		}
		$this->{$attribute} = $validated;
	}

	public function init() {
		parent::init();
		$this->oauth2_loginurl = Url::toRoute(['/site/login'], true);
		$this->oauth2_registerurl = Url::toRoute(['/site/register'], true);
		$this->oauth2_logouturl = Url::toRoute(['/site/logout'], true);
		$this->oauth2_endpoint = Url::toRoute(['/oauth2'], true);
		$params = Yii::$app->params;
		$cookie = 'global.auth.cookie.name';
		$this->oauth2_domain_global_cookie_name = (isset($params[$cookie]) && is_string($params[$cookie]) && $params[$cookie]) ?
			$params[$cookie] : '';
	}

	public function initFromDB() {
		// Populate the properties from the config.
		foreach (Config::settingsKeys() as $key) {
			$prop = static::keyToProp($key);
			if ($this->hasProperty($prop)) {
				$this->$prop = Config::setting($key);
			}
		}
	}

	public function saveSettings() {
		if (!$this->validate()) {
			return false;
		}
		foreach (Config::settingsKeys() as $key) {
			$prop = static::keyToProp($key);
			if ($this->hasProperty($prop)) {
				Config::setting($key, $this->$prop);
			}
		}
		return true;
	}

	public function profileFields() {
		// Get attributes from the profile field, add missing, and sort.
		$fields = (new Profile())->attributeLabels();
		$fields['gravatar_id'] = 'Gravatar ID (Computed from email)';
		asort($fields, SORT_NATURAL | SORT_FLAG_CASE);
		return $fields;
	}

	public function themes() {
		return Theme::enumerate();
	}

	public function defaultValue($prop) {
		return Config::settingDefault(static::propToKey($prop));
	}

	public static function propToKey($prop) {
		return str_replace('_', '.', $prop);
	}

	public static function keyToProp($key) {
		return str_replace('.', '_', $key);
	}
}
