<?php

/**
 * @var yiiwebView $this
 * @var yii\bootstrap\ActiveForm $form
 * @var app\models\site\ContactForm $model
 * @var stdObject $page
 */

use yii\helpers\Html;
use yii\helpers\Markdown;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Contact';
?>

<?= $this->render('_alert') ?>

<div class="row">
	<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
		<h1><?= Html::encode($this->title) ?></h1>
		<?= Markdown::process($page->content); ?>
		<?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
			<?= $form->field($model, 'name') ?>
			<?= $form->field($model, 'email') ?>
			<?= $form->field($model, 'subject') ?>
			<?= $form->field($model, 'body')->textArea(['rows' => 6]) ?>
			<?= $form->field($model, 'verifyCode')
				->widget(Captcha::className(), [
					'template' =>
						'<div class="row">' .
							'<div class="col-xs-5">{input}</div>' .
							'<div class="col-xs-3">{image}</div>' .
						'</div>',
				]) ?>
			<div class="form-group">
				<?= Html::submitButton('Submit', [
					'class' => 'btn btn-primary',
					'name' => 'contact-button'
				]) ?>
			</div>
		<?php ActiveForm::end(); ?>
	</div>
</div>
