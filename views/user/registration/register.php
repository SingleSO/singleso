<?php

/**
 * @var $this yiiwebView
 * @var $user dektrium\user\models\User
 * @var $module dektrium\user\Module
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;
use app\widgets\user\widgets\Connect;

$this->title = Yii::t('user', 'Register');
?>
<div class="row">
	<div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h1 class="panel-title"><?= Html::encode($this->title) ?></h1>
			</div>
			<div class="panel-body">
				<?php $form = ActiveForm::begin(['id' => 'registration-form']); ?>
				<?= Connect::widget([
					'baseAuthUrl' => ['/user/security/auth'],
					'popupMode' => false,
					'itemPrefix' => 'Login with ',
				]) ?>
				<?= $form->field($model, 'email', ['enableAjaxValidation' => true]) ?>
				<?= $form->field($model, 'username', ['enableAjaxValidation' => true]) ?>
				<?php if ($module->enableGeneratingPassword == false): ?>
					<?= $form->field($model, 'password')->passwordInput() ?>
				<?php endif ?>
				<?= $form->field($model, 'verifyCode')
					->widget(Captcha::className(), [
						'template' =>
							'<div class="row">' .
								'<div class="col-xs-7">{input}</div>' .
								'<div class="col-xs-5">{image}</div>' .
							'</div>',
						'captchaAction' => ['/site/captcha']
					]) ?>
				<?= Html::submitButton(
					Yii::t('user', 'Register'),
					['class' => 'btn btn-success btn-block']
				) ?>
				<?php ActiveForm::end(); ?>
			</div>
		</div>
		<p class="text-center">
			<?= Html::a(
				Yii::t('user', 'Already registered? Login!'),
				['/user/security/login']
			) ?>
		</p>
	</div>
</div>
