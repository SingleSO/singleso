<?php

namespace app\models\oauth2;

use app;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Oauth2Client extends ActiveRecord {

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

	public function validateFormat($attribute, $params) {
		$this->{$attribute} = implode(' ', $this->scopesList);
	}

	public static function allServers() {
		return static::find()->all();
	}
}
