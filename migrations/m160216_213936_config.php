<?php

use app\migrations\Migration;

class m160216_213936_config extends Migration
{
	public function up() {
		$this->createTable('{{%config}}', [
			'key' => $this->string(255)->notNull()->unique(),
			'value' => $this->text(),
			'created_at' => $this->integer(),
			'updated_at' => $this->integer(),
		], $this->tableOptions);
		$this->addPrimaryKey('config__pk', '{{%config}}', 'key');
	}

	public function down() {
		$this->dropTable('{{%config}}');
	}
}
