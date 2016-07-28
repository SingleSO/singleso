<?php

use app\migrations\Migration;

class m160211_233910_oauth_server extends Migration {
	public function up() {
		$this->createTable('{{%oauth_server}}', [
			'server' => $this->string(255)->notNull()->unique(),
			'client_id' => $this->string(255),
			'client_secret' => $this->string(255),
			'created_at' => $this->integer(),
			'updated_at' => $this->integer(),
		], $this->tableOptions);
		$this->addPrimaryKey('server__pk', '{{%oauth_server}}', 'server');
	}

	public function down() {
		$this->dropTable('{{%oauth_server}}');
	}
}
