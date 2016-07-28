<?php

namespace app\models\user;

use Yii;
use yii\base\Model;
use app\models\oauth2\Oauth2Client;

class LogoutForm extends Model {

	public $confirm;
	public $active_user_logout = null;
	protected $logouts = null;

	/**
	 * @return array the validation rules.
	 */
	public function rules() {
		return [
			'confirmRequired' => ['confirm', 'required'],
		];
	}

	public function getLogouts() {
		if (!is_array($this->logouts)) {
			$this->logouts = Oauth2Client::getAllLogoutGroupedValid();
		}
		return $this->logouts;
	}
}
