<?php

namespace app\models;

use Exception;
use app;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;

class Config extends ActiveRecord {

	protected static $_settings = [
		'application.admin.email' => '',
		'application.home.url' => 'http://',
		'application.name' => 'Single Sign On',
		'application.copyright' => 'Powered by SingleSO!',
		'application.theme' => '',
		'user.registration.enabled' => true,
		'user.registration.confirmation' => true,
		'user.registration.unconfirmed.login' => false,
		'user.registration.password.recovery' => true,
		'user.registration.confirm.time' => 86400,
		'user.registration.recover.time' => 21600,
		'user.login.remember.time' => 1209600,
		'user.profile.fields.all' => true,
		'user.profile.fields' => [],
		'user.name.length.max' => 25,
		'user.name.length.min' => 3,
		'user.email.length.max' => 35,
		'user.name.blacklist' => [],
		'oauth2.code.expire' => 60, // 1 minute
		'oauth2.token.expire' => 86400, // 24 hours
		'oauth2.code.lastgc' => 0,
		'oauth2.token.lastgc' => 0,
		'page.about.content' => 'Powered by SingleSO!',
		'page.contact.content' => 'Fill out the contact form below to send an email to the website operator.',
		'page.contact.submitted' => 'Contact form submitted successfully',
		'page.links' => '',
	];

	protected static $_configs = null;

	/** @inheritdoc */
	public static function tableName() {
		return '{{%config}}';
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
			'keyLength' => ['key', 'string', 'min' => 1, 'max' => 255],
			'keyRequired' => ['key', 'required'],

			'valueLength' => ['key', 'string', 'max' => 65000],
		];
	}

	public function getType() {
		return static::settingType($this->key);
	}

	public function getValueData() {
		return Json::decode($this->value, true);
	}

	public function setValueData($value) {
		$this->value = Json::encode(static::typeCast($value, $this->type));
	}

	public static function allConfigs() {
		return static::find()->all();
	}

	public static function initConfigs() {
		// Init once.
		if (static::$_configs !== null) {
			return;
		}
		static::$_configs = [];
		$all = static::allConfigs();
		foreach ($all as $config) {
			// Store the config if the key is known.
			$key = $config->key;
			if (isset(static::$_settings[$key])) {
				static::$_configs[$key] = $config;
			}
		}
	}

	public static function settingType($key) {
		if (!isset(static::$_settings[$key])) {
			throw new Exception('Unrecognized setting key: ' . $name);
		}
		// Dynamically get the type.
		$val = static::$_settings[$key];
		if (is_array($val)) {
			return 'array';
		}
		elseif (is_int($val)) {
			return 'integer';
		}
		elseif (is_bool($val)) {
			return 'boolean';
		}
		return 'string';
	}

	public static function typeCast($value, $type) {
		switch ($type) {
			case 'array': {
				return is_array($value) ? $value : [];
			}
			case 'integer': {
				return (int)$value;
			}
			case 'boolean': {
				return (bool)$value;
			}
		}
		return (string)$value;
	}

	public static function settingsKeys() {
		return array_keys(static::$_settings);
	}

	public static function setting($name, $value = null) {
		static::initConfigs();
		if ($value === null) {
			return static::settingGet($name);
		}
		static::settingSet($name, $value);
	}

	public static function settingGet($name) {
		static::initConfigs();
		// Validate the setting key.
		if (!isset(static::$_settings[$name])) {
			throw new Exception('Unrecognized setting key: ' . $name);
		}
		// Use the stored setting if available.
		if (isset(static::$_configs[$name])) {
			return static::$_configs[$name]->valueData;
		}
		// Use default if not.
		return static::$_settings[$name];
	}

	public static function settingSet($name, $value) {
		static::initConfigs();
		// Get the existing config or make new one.
		$config = null;
		$changed = false;
		if (isset(static::$_configs[$name])) {
			$config = static::$_configs[$name];
			// Set the new value, checking if it changes after transforms.
			$oldValue = $config->value;
			$config->valueData = $value;
			$changed = $oldValue !== $config->value;
		}
		else {
			$config = new static();
			// Set key and value.
			$config->key = $name;
			$config->valueData = $value;
			$changed = true;
		}
		// Save value if changed.
		if ($changed) {
			$config->save();
		}
		// Cache the new object.
		static::$_configs[$name] = $config;
	}

	public static function settingDefault($name) {
		if (!isset(static::$_settings[$name])) {
			throw new Exception('Unrecognized setting key: ' . $name);
		}
		return static::$_settings[$name];
	}

	public static function externalLinks() {
		$linksText = static::setting('page.links');
		$lines = array_filter(preg_split('/[\r\n]+/', $linksText));
		$label = null;
		$list = [];
		foreach ($lines as $line) {
			$line = trim($line);
			if ($label === null) {
				$label = $line;
			}
			else {
				$list[] = [
					'label' => $label,
					'url' => $line,
				];
				$label = null;
			}
		}
		return $list;
	}
}
