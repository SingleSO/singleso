<?php

/**
 * @var yiiwebView $this
 * @var stdObject $page
 */

use yii\helpers\Html;
use yii\helpers\Markdown;

$this->title = 'About';
?>
<div class="row">
	<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
		<h1><?= Html::encode($this->title) ?></h1>
		<?= Markdown::process($page->content); ?>
	</div>
</div>
