<?php

namespace app\models\oauth2;

use app;
use Yii;
use app\models\Config;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Oauth2Token extends ActiveRecord {

	protected static $gcInterval = 3600; // GC once an hour.
	protected static $maybeGC = true;

	public function __construct($user = null, $scope = null, $config = []) {
		parent::__construct($config);
		// If creating new for a user, setup properties.
		if (!empty($user)) {
			$this->user_id = is_int($user) ? $user : $user->id;
			$this->token = $this->generateToken();
			$this->scope = $scope;
		}
		static::maybeGarbageCollect();
	}

	/** @inheritdoc */
	public static function tableName() {
		return '{{%oauth2_token}}';
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
			'tokenLength' => ['token', 'string', 'min' => 255, 'max' => 255],
			'tokenTrim' => ['token', 'trim'],
			'tokenRequired' => ['token', 'required'],

			'scopeLength' => ['scope', 'string', 'max' => 255],
			'scopeTrim' => ['scope', 'trim'],
			'scopeRequired' => ['scope', 'required'],
		];
	}

	public function generateToken() {
		$rules = $this->rules();
		$length = $rules['tokenLength']['max'];
		return Yii::$app->getSecurity()->generateRandomString($length);
	}

	/**
	 * @return \yii\db\ActiveQueryInterface
	 */
	public function getUser() {
		return $this->hasOne(
			Yii::$app->modules['user']->modelMap['User'],
			['id' => 'user_id']
		);
	}

	public static function findToken($code) {
		// Search on primary key.
		$query = static::findByCondition([
			static::primaryKey()[0] => $code
		]);
		// Ignore expired.
		$expire = static::getExpiration();
		if ($expire >= 0) {
			$query = $query->andWhere(['>=', 'updated_at', time() - $expire]);
		}
		// Return one and only, or null.
		return $query->one();
	}

	/**
	 * Get the time an entry last before it expires.
	 *
	 * @return integer Time in seconds.
	 */
	public static function getExpiration() {
		return Config::setting('oauth2.token.expire');
	}

	public static function maybeGarbageCollect() {
		// A cron-free way to garbage collect.
		if (!static::$maybeGC) {
			return false;
		}
		static::$maybeGC = false;
		$setting = 'oauth2.token.lastgc';
		$now = time();
		$lastGC = Config::setting($setting);
		// Check if time for another GC.
		if ($lastGC + static::$gcInterval >= $now) {
			return false;
		}
		// Update the current garbage collection time.
		Config::setting($setting, $now);
		$expire = static::getExpiration();
		static::deleteAll(['<', 'updated_at', $now - $expire]);
		return true;
	}
}
