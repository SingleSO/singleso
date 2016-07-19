<?php

/**
 * @var yiiwebView $this
 * @var dektriumusermodelsUser $user
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Nav;
use yii\helpers\Html;

$this->title = Yii::t('user', 'Create a user account');
?>

<?= $this->render('/_alert', [
	'module' => Yii::$app->getModule('user'),
]) ?>

<?= $this->render('_menu') ?>

<div class="row">
	<div class="col-sm-3 col-sm-offset-1 col-md-3 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-body">
				<?= Nav::widget([
					'options' => [
						'class' => 'nav-pills nav-stacked',
					],
					'items' => [
						['label' => Yii::t('user', 'Account details'), 'url' => ['/user/admin/create']],
						['label' => Yii::t('user', 'Profile details'), 'options' => [
							'class' => 'disabled',
							'onclick' => 'return false;',
						]],
						['label' => Yii::t('user', 'Information'), 'options' => [
							'class' => 'disabled',
							'onclick' => 'return false;',
						]],
					],
				]) ?>
			</div>
		</div>
	</div>
	<div class="col-sm-7 col-md-5">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="alert alert-info">
					<?= Yii::t('user', 'Credentials will be sent to the user by email') ?>.
					<?= Yii::t('user', 'A password will be generated automatically if not provided') ?>.
				</div>
				<?php $form = ActiveForm::begin([
					'layout' => 'horizontal',
					'fieldConfig' => [
						'horizontalCssClasses' => [
							'wrapper' => 'col-sm-9',
						],
					],
				]); ?>
				<?= $this->render('_user', ['form' => $form, 'user' => $user]) ?>
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
