<?php

/**
 * @var yiiwebView $this
 * @var string $content
 */

use app\assets\AppAsset;
use app\models\Config;
use app\widgets\Nav;
use app\widgets\NavBar;
use yii\helpers\Html;

AppAsset::register($this);

$this->beginPage();

?><!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= Html::encode($this->title) ?></title>
	<?= Html::csrfMetaTags() ?>
	<?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
	<header>
	<?php
	$homeUrlSetting = Config::setting('application.home.url');
	NavBar::begin([
		'brandLabel' => Yii::$app->name,
		'brandUrl' => $homeUrlSetting ? $homeUrlSetting : Yii::$app->homeUrl,
		'options' => [
			'class' => 'navbar-inverse navbar-fixed-top',
		],
	]);
	$items = [];
	foreach (Config::externalLinks() as $item) {
		$items[] = $item;
	}
	if (trim(Config::setting('page.about.content'))) {
		$items[] = [
			'label' => 'About',
			'url' => ['/site/about'],
		];
	}
	if (trim(Config::setting('page.contact.content'))) {
		$items[] = [
			'label' => 'Contact',
			'url' => ['/site/contact'],
		];
	}
	if (Yii::$app->user->isGuest) {
		$items[] = [
			'label' => 'Register',
			'url' => ['/user/registration/register'],
		];
		$items[] = [
			'label' => 'Login',
			'url' => ['/user/security/login'],
		];
	}
	else {
		if (Yii::$app->user->identity->isAdmin) {
			$items[] = [
				'label' => 'Admin',
				'items' => [
					['label' => 'Settings', 'url' => ['/admin/settings']],
					['label' => 'OAuth Servers', 'url' => ['/admin/oauth-servers']],
					['label' => 'Users', 'url' => ['/user/admin']],
					['label' => 'OAuth 2 Clients', 'url' => ['/oauth2-client']],
				],
			];
		}
		$items[] = [
			'label' => 'Profile (' . Yii::$app->user->identity->username . ')',
			'url' => ['/user/settings'],
		];
		$items[] = [
			'label' => 'Logout',
			'url' => ['/user/security/logout'],
			// 'linkOptions' => ['data-method' => 'post']
		];
	}
	echo Nav::widget([
		'options' => ['class' => 'navbar-nav navbar-right'],
		'items' => $items,
	]);
	NavBar::end();
	?>
	</header>
	<main class="container">
		<?= $content ?>
	</main>
</div>

<footer class="footer">
	<div class="container">
		<p class="text-center"><?php
			if (($copyright = Config::setting('application.copyright'))) {
				echo Html::encode(
					str_replace(
						'{{year}}',
						date('Y'),
						$copyright
					)
				);
			}
		?></p>
	</div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
