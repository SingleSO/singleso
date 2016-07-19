<?php

namespace app\migrations;

use Yii;
use yii\db\Migration as MigrationBase;

class Migration extends MigrationBase {
	/**
	 * @var string
	 */
	protected $tableOptions;

	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();
		if ($this->db->driverName === 'mysql') {
			$this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}
	}
}
