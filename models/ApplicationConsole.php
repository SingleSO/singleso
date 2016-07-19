<?php

namespace app\models;

use Yii;
use app\models\Config;
use yii\console\Application;

class ApplicationConsole extends Application {
	public function __construct($config) {
		parent::__construct($config);

		// Set name from database if not set by config file.
		if (!isset($config['name'])) {
			if (($name = Config::setting('application.name'))) {
				$this->name = $name;
			}
		}

		// Set email from database if not set by config file.
		if (!isset(Yii::$app->params['adminEmail'])) {
			Yii::$app->params['adminEmail'] = null;
			if (($email = Config::setting('application.admin.email'))) {
				Yii::$app->params['adminEmail'] = $email;
			}
		}
	}
}
