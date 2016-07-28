<?php

/**
 * @var yiiwebView $this
 * @var yii\bootstrap\ActiveForm $form
 * @var app\models\site\ContactForm $model
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Logging out.';
?>

<div class="row">
	<div class="col-md-6 col-md-offset-3">
		<div class="form-group logout-panel">
		<?php
		if (empty($model->logouts['valid'])) {
			?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">No services to logout.</h2>
				</div>
				<div class="panel-body">
					There are no services to logout.
				</div>
			</div>
			<?= Html::a('Continue', ['/user/security/logout-redirect'], [
				'class' => 'btn btn-primary btn-block',
			]) ?>
			<?php
		}
		else {
			?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Logging out:</h2>
				</div>
				<div class="panel-body">
					<noscript>
						<div class="alert alert-warning">
							<p>JavaScript is recommended to detect successful logout progress and completion, however logout is still being attempted and should complete when the page loading indicator for the browser has finished.</p>
						</div>
					</noscript>
					<div class="alert alert-danger" id="failed-logout-error" style="display: none;">
						<p>Some of the servies did not logout succesfully. Click to retry failed logouts or continue to ignore.</p>
						<p>Note, continuing may leave you logged in to some services.</p>
						<p>
							<?= Html::button('Retry Failed?', [
								'class' => 'btn btn-default btn-block',
								'id' => 'logout-retry',
							]) ?>
						</p>
					</div>
					<ul class="logout-list" id="logout-list"><?php
					foreach ($model->logouts['valid'] as $server) {
						$logout_attr = Html::encode($server->logoutTokenGenerate($model->user_id));
						?><li data-logout="<?= $logout_attr ?>" data-status="pending"><?php
							?><span data-status="1" class="logout-list-status" data-success="&#x2713;" data-failure="&#10008;"></span> <?php
							?><span data-name="1" class="logout-list-name"><?= Html::encode($server->client_name) ?></span><?php
							// Enable logout with JavaScript disabled, even if not ideal method.
							?><noscript><?php
								?><span style="width: 0; height: 0; display: block; overflow: hidden;"><?php
									?><img src="<?= $logout_attr ?>" /><?php
								?></span><?php
							?></noscript><?php
							?><?php
						?></li><?php
					}
					?></ul>
				</div>
			</div>
			<?= Html::a('Continue', ['/user/security/logout-redirect'], [
				'class' => 'btn btn-primary btn-block',
				'style' => 'display: none;',
				'id' => 'logout-redirect',
			]) ?>
			<script>/*<!--*/<?php readfile(__FILE__ . '.js'); ?>/*-->*/</script>
			<noscript>
				<?= Html::a('Continue when loading finishes...', ['/user/security/logout-redirect'], [
					'class' => 'btn btn-primary btn-block',
				]) ?>
			</noscript>
			<?php
		}
		?>
		</div>
	</div>
</div>
