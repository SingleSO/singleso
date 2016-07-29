<?php

/**
 * @var yiiwebView $this
 * @var yii\bootstrap\ActiveForm $form
 * @var app\models\site\ContactForm $model
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Logout all?';
?>

<div class="row">
	<div class="col-md-6 col-md-offset-3">
		<?php if (!$model->active_user_logout) { ?>
			<div class="alert alert-danger">
				<p><strong>Warning:</strong> The user being logged out is not the same as the one logged in. You will need to logout again to fully logout.</p>
			</div>
		<?php } ?>
		<div class="form-group logout-panel">
		<?php
		$form = ActiveForm::begin(['id' => 'logout-form']);
		?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title">Logout the following services?</h2>
			</div>
			<div class="panel-body"><?php
				if (empty($model->logouts['valid'])) {
					?>No additional services registred to be logged out.<?php
				}
				else {
					?><ul class="logout-list"><?php
					foreach ($model->logouts['valid'] as $service) {
						?><li><?= Html::encode($service->client_name) ?></li><?php
					}
					?></ul><?php
				}
			?></div>
		</div>
		<?php
		if (!empty($model->logouts['invalid'])) {
			?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Note: The following services will not be logged out.</h2>
				</div>
				<div class="panel-body">
					<ul class="logout-list"><?php
						foreach ($model->logouts['invalid'] as $service) {
							?><li data-name="1" class="logout-list-name"><?= Html::encode($service->client_name) ?></li><?php
						}
					?></ul>
					<p>Automatic logout is not supported by these services, so you will still be logged in to these services until you manually logout.</p>
				</div>
			</div>
			<?php
		}
		?>
		<?= Html::activeHiddenInput($model, 'confirm', ['value' => 1]) ?>
		<?= Html::submitButton('Confirm Logout', [
			'class' => 'btn btn-primary btn-block',
		]) ?>
		<?= Html::a('Cancel', ['/user/security/logout-redirect'], [
			'class' => 'btn btn-default btn-block',
		]) ?>
		<?php ActiveForm::end(); ?>
		</div>
	</div>
</div>
