<?php

/**
 * @var yiiwebView $this
 * @var yii\bootstrap\ActiveForm $form
 * @var app\models\admin\OauthServersForm $model
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'OAuth Servers';
?>

<?= $this->render('_alert') ?>

<div class="row">
	<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
		<h1><?= Html::encode($this->title) ?></h1>
		<?php $form = ActiveForm::begin(['id' => 'oauth-servers']); ?>
			<?php foreach ($model->servers() as $serverKey=>$serverVal) {
				$propPre = $model->keyToProp($serverKey);
				?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h2 class="panel-title"><?= Html::encode($serverVal) ?></h2>
					</div>
					<div class="panel-body">
						<?= $form->field($model, $propPre . '_client_id')
							->label('Client ID') ?>
						<?= $form->field($model, $propPre . '_client_secret')
							->label('Client Secret') ?>
						<?= $form->field($model, $propPre . '_client_uri')
							->label('Redirect URI')
							->textinput(['readonly' => true]) ?>
				</div>
				</div>
			<?php } ?>
			<div class="form-group">
				<?= Html::submitButton('Submit', [
					'class' => 'btn btn-primary',
					'name' => 'contact-button'
				]) ?>
			</div>
		<?php ActiveForm::end(); ?>
	</div>
</div>
