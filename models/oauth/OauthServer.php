<?php

namespace app\models\oauth;

use app;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class OauthServer extends ActiveRecord {

	/** @inheritdoc */
	public static function tableName() {
		return '{{%oauth_server}}';
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
			'serverLength' => ['server', 'string', 'min' => 1, 'max' => 255],
			'serverTrim' => ['server', 'trim'],
			'serverRequired' => ['server', 'required'],

			'client_idLength' => ['client_id', 'string', 'max' => 255],
			'client_idTrim' => ['client_id', 'trim'],
			'client_idRequired' => ['client_id', 'required'],

			'client_secretLength' => ['client_secret', 'string', 'max' => 255],
			'client_secretTrim' => ['client_secret', 'trim'],
			'client_secretRequired' => ['client_secret', 'required'],
		];
	}

	public static function allServers() {
		return static::find()->all();
	}

	public static function bulkUpdate(array $updates) {
		// Map out existing servers.
		$existing = [];
		foreach (static::allServers() as $server) {
			$existing[$server->server] = $server;
		}
		// Loop over the updates.
		foreach ($updates as $k=>$v) {
			$server = null;
			// Get existing or create new entry.
			if (isset($existing[$k])) {
				$server = $existing[$k];
				// Check for changes before updating it.
				$client_id = $v['client_id'];
				$client_secret = $v['client_secret'];
				if (
					$server->client_id !== $client_id ||
					$server->client_secret !== $client_secret
				) {
					$server->client_id = $client_id;
					$server->client_secret = $client_secret;
					$server->save();
				}
			}
			else {
				$server = new static();
				$server->server = $k;
				$server->client_id = $v['client_id'];
				$server->client_secret = $v['client_secret'];
				$server->save();
			}
		}
	}
}
