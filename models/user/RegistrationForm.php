<?php

namespace app\models\user;

use Yii;
use dektrium\user\models\RegistrationForm as BaseRegistrationForm;

class RegistrationForm extends BaseRegistrationForm {

	/**
	 * @var string
	 */
	public $verifyCode;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		$rules = parent::rules();
		$rules[] = ['verifyCode', 'required'];
		$rules[] = ['verifyCode', 'captcha'];

		$userClass = $this->module->modelMap['User'];
		$rules = $userClass::addSharedRules($rules);

		$rules['usernameBlacklist'] = ['username', 'validateUsernameBlacklist'];
		return $rules;
	}

	/**
	 * @return array customized attribute labels
	 */
	public function attributeLabels() {
		$attrs = parent::attributeLabels();
		$attrs['verifyCode'] = 'Verification Code';
		return $attrs;
	}

	public function validateUsernameBlacklist($attribute, $params) {
		$userClass = $this->module->modelMap['User'];
		$why = $userClass::usernameBlacklisted($this->{$attribute});
		if ($why) {
			$this->addError($attribute, $why);
		}
	}
}
