<?php

namespace app\models\user;

use Yii;
use dektrium\user\models\SettingsForm as BaseSettingsForm;

class SettingsForm extends BaseSettingsForm {

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		$rules = parent::rules();

		$userClass = $this->module->modelMap['User'];
		$rules = $userClass::addSharedRules($rules);

		$rules['usernameBlacklist'] = ['username', 'validateUsernameBlacklist'];
		return $rules;
	}

	public function validateUsernameBlacklist($attribute, $params) {
		$userClass = $this->module->modelMap['User'];
		// Allow admins to bypass name blacklist.
		if ($userClass::currentUserIsAdmin()) {
			return;
		}
		$why = $userClass::usernameBlacklisted($this->{$attribute});
		if ($why) {
			$this->addError($attribute, $why);
		}
	}
}
