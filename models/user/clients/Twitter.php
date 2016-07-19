<?php

namespace app\models\user\clients;

use dektrium\user\clients\ClientInterface;
use dektrium\user\clients\Twitter as BaseTwitter;

class Twitter extends BaseTwitter implements ClientInterface {

	public function getEmail() {
		// Email requires special request from Twitter.
		// No support for the scope anyway.
		return parent::getEmail();
	}

	public function getUsername() {
		return parent::getUsername();
	}
}
