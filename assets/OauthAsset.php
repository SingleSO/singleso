<?php

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

class OauthAsset extends AssetBundle {
	public $sourcePath = '@app/assets/static/oauth';
	public $css = [
		'css/oauth.css',
	];
	public $js = [
	];
	public $depends = [
		'yii\authclient\widgets\AuthChoiceStyleAsset',
	];
	public function __construct() {
		parent::__construct();
		// Remove the default styles of the auth client.
		$bundles = Yii::$app->assetManager->bundles;
		$classPath = $this->depends[0];
		if (isset($bundles[$classPath])) {
			$bundles[$classPath]->css = [];
		}
	}
}
