<?php

/**
 * @var yiiwebView $this
 * @var yii\widgets\ActiveForm $form
 */

use yii\helpers\Html;

$userClass = Yii::$app->modules['user']->modelMap['User'];
$this->title = Yii::t('user', 'Profile settings');
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
				<?php $form = \yii\widgets\ActiveForm::begin([
					'id' => 'profile-form',
					'options' => ['class' => 'form-horizontal'],
					'fieldConfig' => [
						'template' => '{label}' . "\n" .
							'<div class="col-lg-9">{input}</div>' . "\n" .
							'<div class="col-sm-offset-3 col-lg-9">' .
								'{error}' . "\n" . '{hint}' .
							'</div>',
						'labelOptions' => ['class' => 'col-lg-3 control-label'],
					],
					'validateOnBlur' => false,
				]); ?>
				<?= $userClass::userProfileFieldIsShown('name') ? $form->field($model, 'name') : '' ?>
				<?= $userClass::userProfileFieldIsShown('public_email') ? $form->field($model, 'public_email') : '' ?>
				<?= $userClass::userProfileFieldIsShown('website') ? $form->field($model, 'website') : '' ?>
				<?= $userClass::userProfileFieldIsShown('location') ? $form->field($model, 'location') : '' ?>
				<?= $userClass::userProfileFieldIsShown('gravatar_email') ? $form->field($model, 'gravatar_email')->hint(\yii\helpers\Html::a(Yii::t('user', 'Change your avatar at Gravatar.com'), 'http://gravatar.com')) : '' ?>
				<?= $userClass::userProfileFieldIsShown('bio') ? $form->field($model, 'bio')->textarea() : '' ?>
				<div class="form-group">
					<div class="col-lg-offset-3 col-lg-9">
						<?= \yii\helpers\Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-block btn-success']) ?>
					</div>
				</div>
				<?php \yii\widgets\ActiveForm::end(); ?>
			</div>
		</div>
	</div>
</div>
