<?php

/**
 * @var $this yiiwebView
 * @var $profile \dektrium\user\models\Profile
 */

use yii\helpers\Html;

$userClass = Yii::$app->modules['user']->modelMap['User'];
$this->title = (
		!$userClass::userProfileFieldIsShown('name') ||
		!$profile->name
	) ?
	Html::encode($profile->user->username) :
	Html::encode($profile->name);
?>
<div class="row">
	<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
		<div class="row">
			<div class="col-sm-6 col-md-4">
				<img src="http://gravatar.com/avatar/<?= $profile->gravatar_id ?>?s=230" alt="" class="img-rounded img-responsive" />
			</div>
			<div class="col-sm-6 col-md-8">
				<h4><?= $this->title ?></h4>
				<ul style="padding: 0; list-style: none outside none;">
					<?php if ($userClass::userProfileFieldIsShown('location') && !empty($profile->location)): ?>
						<li><i class="glyphicon glyphicon-map-marker text-muted"></i> <?= Html::encode($profile->location) ?></li>
					<?php endif; ?>
					<?php if ($userClass::userProfileFieldIsShown('website') && !empty($profile->website)): ?>
						<li><i class="glyphicon glyphicon-globe text-muted"></i> <?= Html::a(Html::encode($profile->website), Html::encode($profile->website)) ?></li>
					<?php endif; ?>
					<?php if ($userClass::userProfileFieldIsShown('public_email') && !empty($profile->public_email)): ?>
						<li><i class="glyphicon glyphicon-envelope text-muted"></i> <?= Html::a(Html::encode($profile->public_email), 'mailto:' . Html::encode($profile->public_email)) ?></li>
					<?php endif; ?>
					<li><i class="glyphicon glyphicon-time text-muted"></i> <?= Yii::t('user', 'Joined on {0, date}', $profile->user->created_at) ?></li>
				</ul>
				<?php if ($userClass::userProfileFieldIsShown('bio') && !empty($profile->bio)): ?>
					<p><?= Html::encode($profile->bio) ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
