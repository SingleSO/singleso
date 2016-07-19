<?php

/**
 * @var yiiwidgetsActiveForm $form
 * @var dektrium\user\models\User $user
 */

?>
<?= $form->field($user, 'email', ['enableAjaxValidation' => true])->textInput(['maxlength' => 255]) ?>
<?= $form->field($user, 'username', ['enableAjaxValidation' => true])->textInput(['maxlength' => 255]) ?>
<?= $form->field($user, 'password')->passwordInput() ?>
<?php if ($user->scenario === 'update') { ?>
	<?= $form->field($user, 'is_admin')->checkbox([], false); ?>
<?php } ?>
