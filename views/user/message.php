<?php

/**
 * @var yiiwebView $this
 * @var dektriumuserModule $module
 */

use app\models\oauth2\Oauth2;
use yii\helpers\Html;

$this->title = $title;
?>
<?= $this->render('/_alert', [
	'module' => $module,
]) ?>

<?php
// Add links to either return home or to edit profile.
// Also include a link to redirect if one is stored in the session.
?>
<div class="row">
	<div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
<?php if (Yii::$app->user->isGuest) : ?>
	<?= Html::a(
		Html::encode('Return Home'),
		['/'],
		['class'=>'btn btn-default btn-lg btn-block']
	) ?>
<?php else : ?>
	<?php if (($linkName = Oauth2::getSessionRedirectName())) : ?>
		<?php Oauth2::setSessionNoAuto(); ?>
		<?= Html::a(
			Html::encode('Continue to ' . $linkName),
			['/site/first-registration-redirect'],
			['class'=>'btn btn-primary btn-lg btn-block']
		) ?>
	<?php endif; ?>
	<?= Html::a(
		Html::encode('Edit your Profile'),
		['/user/settings'],
		['class'=>'btn btn-default btn-lg btn-block']
	) ?>
<?php endif; ?>
	</div>
</div>
