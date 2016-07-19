<?php

/**
 * @var yiiwebView $this
 * @var string $name
 * @var string $message
 * @var Exception $exception
 */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="row">
	<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
		<h1><?= Html::encode($this->title) ?></h1>
		<div class="alert alert-danger">
			<?= nl2br(Html::encode($message ?: 'Page unavailable.')) ?>
		</div>
		<p>The above error occurred while processing your request.</p>
	</div>
</div>
