<?php

use app\migrations\Migration;

class m160209_143756_oauth2_token extends Migration {
	public function up() {
		$this->createTable('{{%oauth2_token}}', [
			'user_id' => $this->integer(),
			'token' => $this->string(255)->notNull()->unique(),
			'scope' => $this->string(255),
			'created_at' => $this->integer(),
			'updated_at' => $this->integer(),
		], $this->tableOptions);
		$this->addPrimaryKey('token__pk', '{{%oauth2_token}}', 'token');
		$this->addForeignKey('fk_user_oauth2_token', '{{%oauth2_token}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
	}

	public function down() {
		$this->dropTable('{{%oauth2_token}}');
	}
}
