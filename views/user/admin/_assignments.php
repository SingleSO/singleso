<?php

/**
 * @var yiiwebView $this
 * @var dektrium\user\models\User $user
 */

use dektrium\rbac\widgets\Assignments;

?>
<?php $this->beginContent('@dektrium/user/views/admin/update.php', ['user' => $user]) ?>

<?= yii\bootstrap\Alert::widget([
	'options' => [
		'class' => 'alert-info',
	],
	'body' => Yii::t('user', 'You can assign multiple roles or permissions to user by using the form below'),
]) ?>

<?= Assignments::widget(['userId' => $user->id]) ?>

<?php $this->endContent() ?>
