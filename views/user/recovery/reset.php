<?php

/**
 * @var $this yiiwebView
 * @var $form yii\widgets\ActiveForm
 * @var $model dektrium\user\models\RecoveryForm
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('user', 'Reset your password');
?>
<div class="row">
	<div class="col-md-4 col-md-offset-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h1 class="panel-title"><?= Html::encode($this->title) ?></h1>
			</div>
			<div class="panel-body">
				<?php $form = ActiveForm::begin([
					'id' => 'password-recovery-form',
				]); ?>
				<?= $form->field($model, 'password')->passwordInput() ?>
				<?= Html::submitButton(
					Yii::t('user', 'Finish'),
					['class' => 'btn btn-success btn-block']
				) ?>
				<?php ActiveForm::end(); ?>
			</div>
		</div>
	</div>
</div>
