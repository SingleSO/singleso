<?php

use yii\db\Schema;
use app\migrations\Migration;

class m160209_141255_oauth2_code extends Migration {
	public function up() {
		$this->createTable('{{%oauth2_code}}', [
			'user_id' => $this->integer(),
			'code' => $this->string(64)->notNull()->unique(),
			'scope' => $this->string(255),
			'created_at' => $this->integer(),
			'updated_at' => $this->integer(),
		], $this->tableOptions);
		$this->addPrimaryKey('code__pk', '{{%oauth2_code}}', 'code');
		$this->addForeignKey('fk_user_oauth2_code', '{{%oauth2_code}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
	}

	public function down() {
		$this->dropTable('{{%oauth2_code}}');
	}
}
