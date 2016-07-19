<?php

use yii\helpers\Html;


/**
 * @var $this yiiwebView
 * @var $model app\models\oauth2\Oauth2Client
 */

$this->title = 'Create Oauth2 Client';
?>

<?= $this->render('_alert') ?>

<?= $this->render('_menu') ?>

<div class="row">
	<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-body">
				<?= $this->render('_form', [
					'model' => $model,
				]) ?>
			</div>
		</div>
	</div>
</div>
