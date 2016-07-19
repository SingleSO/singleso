<?php

namespace app\models\admin;

use Yii;
use app\models\oauth\OauthServer;
use yii\base\Model;
use yii\helpers\Url;

class OauthServersForm extends Model {

	public $facebook_oauth2_client_id = null;
	public $facebook_oauth2_client_secret = null;
	public $facebook_oauth2_client_uri = null;

	public $google_oauth2_client_id = null;
	public $google_oauth2_client_secret = null;
	public $google_oauth2_client_uri = null;

	public $twitter_oauth1_client_id = null;
	public $twitter_oauth1_client_secret = null;
	public $twitter_oauth1_client_uri = null;

	public function rules() {
		$rules = [];
		$servers = $this->servers();
		foreach ($servers as $k=>$v) {
			$propPre = $this->keyToProp($k);
			$propCliendID = $propPre . '_client_id';
			$propCliendSecret = $propPre . '_client_secret';
			$rules[$propCliendID . 'Length'] = [$propCliendID, 'string', 'max' => 255];
			$rules[$propCliendSecret . 'Length'] = [$propCliendSecret, 'string', 'max' => 255];
		}
		return $rules;
	}

	public function init() {
		parent::init();
		// Add the client URI's.
		foreach ($this->servers() as $k=>$v) {
			$propPre = $this->keyToProp($k);
			$prop = $propPre . '_client_uri';
			$this->$prop = Url::toRoute(
				['/user/security/auth', 'authclient' => $k],
				true
			);
		}
	}

	public function servers() {
		return [
			'facebook.oauth2' => 'Facebook OAuth 2',
			'google.oauth2' => 'Google OAuth 2',
			'twitter.oauth1' => 'Twitter OAuth 1',
		];
	}

	public function initFromDB() {
		$servers = OauthServer::allServers();
		foreach ($servers as $server) {
			$propPre = $this->keyToProp($server->server);
			$propCliendID = $propPre . '_client_id';
			$propCliendSecret = $propPre . '_client_secret';
			if (
				!$this->hasProperty($propCliendID) ||
				!$this->hasProperty($propCliendSecret)
			) {
				continue;
			}
			$this->$propCliendID = $server->client_id;
			$this->$propCliendSecret = $server->client_secret;
		}
	}

	public function saveServers() {
		if (!$this->validate()) {
			return false;
		}
		$servers = $this->servers();
		$update = [];
		foreach ($servers as $k=>$v) {
			$propPre = $this->keyToProp($k);
			$propCliendID = $propPre . '_client_id';
			$propCliendSecret = $propPre . '_client_secret';
			$key = $this->propToKey($propPre);
			$update[$key] = [
				'client_id' => $this->$propCliendID,
				'client_secret' => $this->$propCliendSecret,
			];
		}
		OauthServer::bulkUpdate($update);
		return true;
	}

	public static function propToKey($prop) {
		return str_replace('_', '.', $prop);
	}

	public static function keyToProp($key) {
		return str_replace('.', '_', $key);
	}
}
