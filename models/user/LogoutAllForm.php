<?php

namespace app\models\user;

use Yii;
use yii\base\Model;
use app\models\oauth2\Oauth2Client;

class LogoutAllForm extends Model {

	public $user_id = null;
	protected $logouts = null;

	public function getLogouts() {
		if (!is_array($this->logouts)) {
			$this->logouts = Oauth2Client::getAllLogoutGroupedValid();
		}
		return $this->logouts;
	}
}
