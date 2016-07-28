<?php

use app\migrations\Migration;

class m160719_191844_oauth2_client_logout_uri extends Migration
{
	public function up() {
		$this->addColumn(
			'{{%oauth2_client}}',
			'logout_uri',
			$this->text()
		);
	}

	public function down() {
		$this->dropColumn('{{%oauth2_client}}', 'logout_uri');
	}
}
