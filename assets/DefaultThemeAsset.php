<?php

namespace app\assets;

use Yii;
use app\assets\AppAsset;
use yii\web\AssetBundle;

class DefaultThemeAsset extends AssetBundle {
	public $sourcePath = '@app/assets/static/default-theme';
	public $css = [
		'css/site.css',
	];
	public $js = [
	];
	public $depends = [
		'yii\web\YiiAsset',
		'yii\bootstrap\BootstrapAsset',
		'app\assets\OauthAsset',
	];
}
