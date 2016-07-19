<?php

use yii\widgets\Menu;

/* @var $user dektrium\user\models\User */

$user = Yii::$app->user->identity;
$networksVisible = count(Yii::$app->authClientCollection->clients) > 0;

?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h1 class="panel-title">
			<img src="http://gravatar.com/avatar/<?= $user->profile->gravatar_id ?>?s=24" class="img-rounded" alt="<?= $user->username ?>" />
			<?= $user->username ?>
		</h1>
	</div>
	<div class="panel-body">
		<?= Menu::widget([
			'options' => [
				'class' => 'nav nav-pills nav-stacked',
			],
			'items' => [
				['label' => Yii::t('user', 'Profile'), 'url' => ['/user/settings/profile']],
				['label' => Yii::t('user', 'Account'), 'url' => ['/user/settings/account']],
				['label' => Yii::t('user', 'Networks'), 'url' => ['/user/settings/networks'], 'visible' => $networksVisible],
			],
		]) ?>
	</div>
</div>
