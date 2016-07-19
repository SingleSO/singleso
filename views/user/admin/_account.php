<?php

/**
 * @var yiiwebView $this
 * @var dektrium\user\models\User $user
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>
<?php $this->beginContent(
	'@dektrium/user/views/admin/update.php',
	['user' => $user]
) ?>

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
		<?= Html::submitButton(
			Yii::t('user', 'Update'),
			['class' => 'btn btn-block btn-success']
		) ?>
	</div>
</div>

<?php ActiveForm::end(); ?>

<?php $this->endContent() ?>
