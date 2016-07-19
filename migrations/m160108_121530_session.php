<?php

use yii\db\Schema;
use app\migrations\Migration;

class m160108_121530_session extends Migration {
	public function up() {
		$this->createTable('{{%session}}', [
			'id' => $this->string(64)->notNull()->unique(),
			'expire' => $this->integer(),
			'data' => $this->binary(),
		], $this->tableOptions);
		$this->addPrimaryKey('id__pk', '{{%session}}', 'id');
	}

	public function down() {
		$this->dropTable('{{%session}}');
	}
}
