<?php

namespace app\models\oauth2;

use Yii;
use app\models\oauth2\Oauth2;
use app\models\oauth2\Oauth2Client;
use yii\base\Model;

class CodeForm extends Model {

	const SCENARIO_CREATE = 'create';
	const SCENARIO_VERIFY = 'verify';

	public $client_id;
	public $client_secret;
	public $redirect_uri;
	public $code;
	public $scope;
	public $state;

	protected $_client = false;

	public function scenarios() {
		return [
			self::SCENARIO_CREATE => [
				'client_id',
				'redirect_uri',
				'scope',
				'state',
			],
			self::SCENARIO_VERIFY => [
				'client_id',
				'client_secret',
				'redirect_uri',
				'code',
				'state',
			],
		];
	}

	public function rules() {
		$this->scope = 'user email profile';
		return [
			'client_idTrim' => ['client_id', 'trim'],
			'client_idRequired' => ['client_id', 'required'],
			'client_idValidate' => ['client_id', 'validateClientID'],

			'client_secretTrim' => ['client_secret', 'trim'],
			'client_secretRequired' => ['client_secret', 'required'],
			'client_secretValidate' => ['client_secret', 'validateClientSecret'],

			'redirect_uriTrim' => ['redirect_uri', 'trim'],
			'redirect_uriRequired' => ['redirect_uri', 'required'],
			'redirect_uriValidate' => ['redirect_uri', 'validateRedirectURI'],

			'codeLength' => ['code', 'string', 'min' => 64, 'max' => 64],
			'codeTrim' => ['code', 'trim'],
			'codeRequired' => ['code', 'required'],

			'scopeLength' => ['scope', 'string', 'max' => 255],
			'scopeTrim' => ['scope', 'trim'],
			'scopeRequired' => ['scope', 'required'],
			'scopeValidate' => ['scope', 'validateScope'],

			'state' => ['state', 'string']
		];
	}

	public function getClient() {
		// Load on client by id on first call.
		if ($this->_client === false) {
			$this->_client = Oauth2Client::findOne($this->client_id);
		}
		return $this->_client;
	}

	public function validateClientID($attribute, $params) {
		$value = $this->{$attribute};
		// Verify the client exists.
		if (!$this->client) {
			$this->addError($attribute, 'Invalid client_id: ' . $value);
		}
	}

	public function validateClientSecret($attribute, $params) {
		$client = $this->client;
		// This should be validate in the other validator.
		if (!$client) {
			return;
		}
		$value = $this->{$attribute};
		// Validate client_secret is set and matches.
		if (!$client->client_secret || $client->client_secret !== $value) {
			$this->addError($attribute, 'Invalid client_secret: ' . $value);
		}
	}

	public function validateRedirectURI($attribute, $params) {
		$client = $this->client;
		// This should be validate in the other validator.
		if (!$client) {
			return;
		}
		$value = $this->{$attribute};
		$validated = $client->validateRedirectURI($value);
		if ($validated) {
			$this->{$attribute} = $validated;
		}
		else {
			$this->addError($attribute, 'Invalid redirect_uri: ' . $value);
		}
	}

	public function validateScope($attribute, $params) {
		$value = $this->{$attribute};
		// Parse scopes and validate.
		$scopes = array_filter(preg_split('/[^a-z0-9-_\.]+/i', (string)$value));
		$this->{$attribute} = $value = implode(' ', $scopes);
		$client = $this->client;
		// This should be validate in the other validator.
		if (!$client) {
			return;
		}
		// Validate allowed scopes list, and that requested scopes are listed.
		$allowedScopes = $client->scopesList;
		if (empty($allowedScopes)) {
			$this->addError($attribute, 'Scopes unspecified');
			return;
		}
		// Ensure scope not empty.
		if (empty($scopes) || !is_array($scopes)) {
			$this->addError($attribute, 'Empty scope');
			return;
		}
		$invalidScopes = [];
		foreach ($scopes as $scope) {
			if (!in_array($scope, $allowedScopes)) {
				$invalidScopes[] = $scope;
			}
		}
		// If any not available, show error.
		if (!empty($invalidScopes)) {
			$this->addError($attribute, 'Invalid scopes: ' . implode(',', $invalidScopes));
		}
	}

	public function redirectURL() {
		$redirect_uri = $this->redirect_uri;
		$state = $this->state;
		// Create the response parameters.
		$args = [
			'code' => $this->code,
			'state' => $state ? $state : null,
		];
		// Create the full URL, adding the query args.
		return $redirect_uri .
			(parse_url($redirect_uri, PHP_URL_QUERY) ? '&' : '?') .
			http_build_query($args);
	}
}
