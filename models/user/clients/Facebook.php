<?php

namespace app\models\user\clients;

use dektrium\user\clients\ClientInterface;
use dektrium\user\clients\Facebook as BaseFacebook;

class Facebook extends BaseFacebook implements ClientInterface {

	public function getEmail() {
		return parent::getEmail();
	}

	public function getUsername() {
		return parent::getUsername();
	}
}
