<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this yiiwebView
 * @var $model app\models\oauth2\Oauth2Client
 * @var $form yii\widgets\ActiveForm
 */
?>

<div class="oauth2-client-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'client_name')->textInput(['maxlength' => true]) ?>
	<p>A user-friendly name.</p>

	<?= $form->field($model, 'client')->textInput(['maxlength' => true]) ?>
	<p>A short, unique ID.</p>

	<?= $form->field($model, 'client_secret')->textInput(['maxlength' => true]) ?>
	<p>A secure string of random characters unique and secret to each client.</p>

	<?= $form->field($model, 'scopes')->textInput(['maxlength' => true]) ?>
	<p>A space delimited list of scopes accessible to the client.</p>

	<?= $form->field($model, 'redirect_uris')->textarea(['rows' => 6]) ?>
	<p>A list of URL's that the client can request redirects to. Only exact matches to this list can be redirect to.</p>

	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
