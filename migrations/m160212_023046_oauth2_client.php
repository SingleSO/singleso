<?php

use app\migrations\Migration;

class m160212_023046_oauth2_client extends Migration {
	public function up() {
		$this->createTable('{{%oauth2_client}}', [
			'client' => $this->string(255)->notNull()->unique(),
			'client_name' => $this->string(255),
			'client_secret' => $this->string(255),
			'scopes' => $this->string(255),
			'redirect_uris' => $this->text(),
			'created_at' => $this->integer(),
			'updated_at' => $this->integer(),
		], $this->tableOptions);
		$this->addPrimaryKey('client__pk', '{{%oauth2_client}}', 'client');
	}

	public function down() {
		$this->dropTable('{{%oauth2_client}}');
	}
}
