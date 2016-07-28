<?php

use app\migrations\Migration;

class m160210_203529_user_is_admin extends Migration
{
	public function up() {
		$this->addColumn(
			'{{%user}}',
			'is_admin',
			$this->integer()->notNull()->defaultValue(0)
		);
	}

	public function down() {
		$this->dropColumn('{{%user}}', 'is_admin');
	}
}
