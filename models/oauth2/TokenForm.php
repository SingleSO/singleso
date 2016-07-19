<?php

namespace app\models\oauth2;

use Yii;
use app\models\oauth2\Oauth2;
use yii\base\Model;

class TokenForm extends Model {

	public $access_token;

	public function rules() {
		return [
			'access_tokenLength' => ['access_token', 'string', 'min' => 255, 'max' => 255],
			'access_tokenTrim' => ['access_token', 'trim'],
			'access_tokenRequired' => ['access_token', 'required'],
		];
	}
}
