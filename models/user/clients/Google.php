<?php

namespace app\models\user\clients;

use dektrium\user\clients\ClientInterface;
use dektrium\user\clients\Google as BaseGoogle;

class Google extends BaseGoogle implements ClientInterface {

	public function getEmail() {
		// Parent looks for email in an old place.
		$email = parent::getEmail();
		if (!$email) {
			// Loop over all the email addresses.
			$emails = $this->getUserAttributes();
			if (
				isset($emails['emails']) &&
				($emails = $emails['emails']) &&
				is_array($emails)
			) {
				foreach ($emails as &$pair) {
					// Sanity check.
					if (isset($pair['value']) && $pair['value']) {
						// If the account email, use it.
						if (
							isset($pair['type']) &&
							$pair['type'] === 'account'
						) {
							$email = $pair['value'];
							break;
						}
						// Else use first email.
						elseif (!$email) {
							$email = $pair['value'];
						}
					}
				}
				unset($pair);
			}
			unset($emails);
		}
		return $email;
	}

	public function getUsername() {
		return parent::getUsername();
	}
}
