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

	<?= $form->field($model, 'logout_uri')->textInput(['maxlength' => true]) ?>
	<p>The URL to have the client request to trigger a logout.</p>

	<div class="panel panel-default">
		<div class="panel-heading">Logout API</div>
		<div class="panel-body">
			<p>A hash of the expiration time, user id, and client secret will be passed to verify the logout request.</p>
			<p>The hash is generated as follows:</p>
			<p><code>expiration + '|' + user_id + '|' + sha512(expiration + '|' + user_id + '|' + client_secret)</code></p>
			<p>The hash is passed as follows:</p>
			<p><code>../endpoint?token=123456|ABCDEFG1234567890...</code></p>
			<p>This same token should also be passed back from the service requesting logout. doing so allows logout even if the main session has been lost.</p>
			<p>The JSON or JSONP response is formatted as follows:</p>
			<p><code>{"success":1}</code></p>
			<p>Possible values:</p>
			<ul>
				<li><code>0</code>: failed to logout</li>
				<li><code>1</code>: logged out</li>
				<li><code>-1</code>: no user to logout</li>
			</ul>
		</div>
	</div>

	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
