<?php

/**
 * @var $this yiiwebView
 * @var $user dektrium\user\models\User
 * @var $model dektrium\user\models\ResendForm
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('user', 'Request new confirmation message');
?>
<div class="row">
	<div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h1 class="panel-title"><?= Html::encode($this->title) ?></h1>
			</div>
			<div class="panel-body">
				<?php $form = ActiveForm::begin([
					'id' => 'resend-form',
				]); ?>
				<?= $form->field($model, 'email', ['enableAjaxValidation' => true])
					->textInput(['autofocus' => true])
				?>
				<?= Html::submitButton(
					Yii::t('user', 'Continue'),
					['class' => 'btn btn-primary btn-block']
				) ?>
				<?php ActiveForm::end(); ?>
			</div>
		</div>
	</div>
</div>
