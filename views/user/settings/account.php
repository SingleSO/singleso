<?php

/**
 * @var $this yiiwebView
 * @var $form yii\widgets\ActiveForm
 * @var $model dektrium\user\models\SettingsForm
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('user', 'Account settings');
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row">
	<div class="col-sm-3 col-sm-offset-1 col-md-3 col-md-offset-2">
		<?= $this->render('_menu') ?>
	</div>
	<div class="col-sm-7 col-md-5">
		<div class="panel panel-default">
			<div class="panel-heading"><?= Html::encode($this->title) ?></div>
			<div class="panel-body">
				<?php $form = ActiveForm::begin([
					'id' => 'account-form',
					'options' => ['class' => 'form-horizontal'],
					'fieldConfig' => [
						'template' => '{label}' . "\n" .
							'<div class="col-lg-9">{input}</div>' . "\n" .
							'<div class="col-sm-offset-3 col-lg-9">' .
								'{error}' . "\n" . '{hint}' .
							'</div>',
						'labelOptions' => ['class' => 'col-lg-3 control-label'],
					],
				]); ?>
				<?= $form->field($model, 'email', ['enableAjaxValidation' => true]) ?>
				<?= $form->field($model, 'username', ['enableAjaxValidation' => true]) ?>
				<?= $form->field($model, 'new_password')->passwordInput() ?>
				<hr />
				<?= $form->field($model, 'current_password', ['enableAjaxValidation' => true])->passwordInput() ?>
				<div class="form-group">
					<div class="col-lg-offset-3 col-lg-9">
						<?= Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-block btn-success']) ?>
					</div>
				</div>
				<?php ActiveForm::end(); ?>
			</div>
		</div>
	</div>
</div>
