<?php

/**
 * @var $this yiiwebView
 * @var $model dektrium\user\models\LoginForm
 * @var $module dektrium\user\Module
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\user\widgets\Connect;

$this->title = Yii::t('user', 'Login');
?>
<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>
<div class="row">
	<div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h1 class="panel-title"><?= Html::encode($this->title) ?></h1>
			</div>
			<div class="panel-body">
				<?php $form = ActiveForm::begin([
					'id' => 'login-form',
					'validateOnBlur' => false,
					'validateOnType' => false,
					'validateOnChange' => false,
				]) ?>
				<?= Connect::widget([
					'baseAuthUrl' => ['/user/security/auth'],
					'popupMode' => false,
					'itemPrefix' => 'Login with ',
					'tabIndex' => '6',
				]) ?>
				<?= $form->field($model, 'login', [
						'inputOptions' => [
							'autofocus' => 'autofocus',
							'class' => 'form-control',
							'tabindex' => '1'
						],
					])
					->label(
						Yii::t('user', 'Login') .
							' <small>(Username or Email)</small>'
					) ?>
				<?= $form->field($model, 'password', [
					'inputOptions' => [
						'class' => 'form-control',
						'tabindex' => '2'
					]
				])
					->passwordInput()
					->label(
						Yii::t('user', 'Password') .
						($module->enablePasswordRecovery ?
							' <small>(' . Html::a(
								Yii::t('user', 'Forgot password?'),
								['/user/recovery/request'],
								['tabindex' => '5']
							) . ')</small>' : ''))
				?>
				<?= $form->field($model, 'rememberMe')
						->checkbox(['tabindex' => '3'])
				?>
				<?= Html::submitButton(
					Yii::t('user', 'Login'),
					[
						'class' => 'btn btn-primary btn-block',
						'tabindex' => '4'
					]
				) ?>
				<?php ActiveForm::end(); ?>
			</div>
		</div>
		<?php if ($module->enableRegistration): ?>
			<p class="text-center">
				<?= Html::a(Yii::t(
					'user',
					'Don\'t have an account? Register!'
				), ['/user/registration/register']) ?>
			</p>
			<?php if ($module->enableConfirmation): ?>
				<p class="text-center">
					<?= Html::a(Yii::t(
						'user',
						'Resend confirmation message?'
					), ['/user/registration/resend']) ?>
				</p>
			<?php endif ?>
		<?php endif ?>
	</div>
</div>
