<?php

namespace app\models\authclient;

use Yii;
use app;
use app\models\oauth\OauthServer;
use yii\authclient\Collection as CollectionBase;

class Collection extends CollectionBase {

	protected static $_servers = [
		'google.oauth2' => 'app\models\user\clients\Google',
		'facebook.oauth2' => 'app\models\user\clients\Facebook',
		'twitter.oauth1' => 'app\models\user\clients\Twitter',
	];

	protected static $__clients = [];

	public static function initClients() {
		// Only init once if not loaded from config.
		if (!empty(static::$__clients)) {
			return;
		}
		// Load servers from database.
		$servers = OauthServer::allServers();
		foreach ($servers as $serv) {
			// Get properties.
			$server = $serv->server;
			if (!isset(static::$_servers[$server])) {
				continue;
			}
			$class = static::$_servers[$server];
			$client_id = $serv->client_id;
			$client_secret = $serv->client_secret;
			if (!$client_id || !$client_secret) {
				continue;
			}
			// Setup keys for the different servers.
			if (is_subclass_of($class, 'yii\authclient\OAuth2')) {
				static::$__clients[$server] = [
					'class' => $class,
					'clientId' => $client_id,
					'clientSecret' => $client_secret,
				];
			}
			elseif (is_subclass_of($class, 'yii\authclient\OAuth1')) {
				static::$__clients[$server] = [
					'class' => $class,
					'consumerKey' => $client_id,
					'consumerSecret' => $client_secret,
				];
			}
		}
	}

	/** @inheritdoc */
	public function setClients(array $clients) {
		static::$__clients = $clients;
	}

	/** @inheritdoc */
	public function getClients() {
		static::initClients();
		$clients = [];
		foreach (static::$__clients as $id => $client) {
			$clients[$id] = $this->getClient($id);
		}
		return $clients;
	}

	/** @inheritdoc */
	public function getClient($id) {
		static::initClients();
		if (!array_key_exists($id, static::$__clients)) {
			throw new InvalidParamException("Unknown auth client '{$id}'.");
		}
		if (!is_object(static::$__clients[$id])) {
			static::$__clients[$id] = $this->createClient($id, static::$__clients[$id]);
		}
		return static::$__clients[$id];
	}

	/** @inheritdoc */
	public function hasClient($id) {
		static::initClients();
		return array_key_exists($id, static::$__clients);
	}
}
