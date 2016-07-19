<?php

namespace app\models\user;

use Yii;
use yii\base\InvalidParamException;
use dektrium\user\helpers\Password;
use dektrium\user\models\LoginForm as BaseLoginForm;
use Exception;

class LoginForm extends BaseLoginForm {
	public function rules() {
		// Get the parent fules.
		$rules = parent::rules();
		// Replace the password validator.
		$rules['passwordValidate'] = [
			'password',
			function ($attribute) {
				$error = Yii::t('user', 'Invalid login or password');
				$user = $this->user;
				if ($user === null) {
					$this->addError($attribute, $error);
					return;
				}
				$validPass = false;
				$password = $this->password;
				$hash = $user->password_hash;
				// Try to validate hash, might throw if hash is invalid.
				try {
					$validPass = Password::validate($password, $hash);
				}
				catch (InvalidParamException $e) {
					// Do nothing.
				}
				// If a valid and modern hash, return now.
				if ($validPass) {
					return;
				}
				// Validate against legacy hashes.
				if ($this->validLegacyHash($password, $hash, $user)) {
					$user->resetPassword($this->password);
				}
				else {
					$this->addError($attribute, $error);
				}
			}
		];
		// Return the modified rules.
		return $rules;
	}

	public function validLegacyHash($password, $hash, $user) {
		$params = Yii::$app->params;
		// Check if an array of legacy hash validators is specified.
		$key = 'legacy.password.hashes';
		$legacyHashes = isset($params[$key]) ? $params[$key] : null;
		if (is_array($legacyHashes)) {
			foreach ($legacyHashes as $func) {
				// If function returns true, return true.
				if (is_callable($func) && $func($password, $hash, $user)) {
					return true;
				}
			}
		}
		return false;
	}
}
