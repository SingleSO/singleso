<?php

namespace app\assets;

use Yii;
use yii\web\AssetBundle;
use app\models\Theme;

class AppAsset extends AssetBundle {

	protected static $defaultThemeAsset = 'app\assets\DefaultThemeAsset';

	public function __construct() {
		parent::__construct();

		// Change the default jQuery UI theme.
		$this->configureJuiTheme('base');

		// Get the current theme if set and valid.
		$theme = Theme::theme();
		// If not set or not valid, use the default theme.
		if (!$theme) {
			$this->depends = [
				static::$defaultThemeAsset
			];
			return;
		}

		// Configure bootstrap and jQuery UI theme.
		$this->configureBootstrap($theme->bootstrap);
		$this->configureJuiTheme($theme->juiTheme);

		// Setup the dependencies.
		$this->configureDependencies($theme->depends);
		$this->configureDependencies($theme->depends);

		// Set source path for the theme assets and register CSS and JS.
		$this->sourcePath = $theme->sourcePath;
		$this->configureDependencies($theme->depends);
		$this->configureCSS($theme->css);
		$this->configureJS($theme->js);
	}

	public function assetManager($class, $prop, $value) {
		// Entierh edit existing object or set config array.
		$bundles = &Yii::$app->assetManager->bundles;
		if (isset($bundles[$class]) && is_object($bundles[$class])) {
			$bundles[$class]->$prop = $value;
		}
		else {
			$bundles[$class][$prop] = $value;
		}
	}

	public function configureBootstrap($value) {
		// If false, remove CSS and JS and let theme supply.
		if ($value === false) {
			$bundles = &Yii::$app->assetManager->bundles;
			$this->assetManager('yii\bootstrap\BootstrapAsset', 'css', []);
			$this->assetManager('yii\bootstrap\BootstrapPluginAsset', 'js', []);
		}
	}

	public function configureJuiTheme($value) {
		$bundles = Yii::$app->assetManager->bundles;
		$juiAsset = 'yii\jui\JuiAsset';
		if (isset($bundles[$juiAsset])) {
			// If false, remove CSS and let theme supply.
			if ($value === false) {
				$this->assetManager($juiAsset, 'css', []);
			}
			// Else if a string, then change the theme.
			elseif (is_string($value)) {
				$this->assetManager($juiAsset, 'css', [
					'themes/' . $value . '/jquery-ui.css',
				]);
			}
		}
	}

	public function configureDependencies($value) {
		$this->depends = is_array($value) ? $value : [];
	}

	public function configureCSS($value) {
		$this->css = is_array($value) ? $value : [];
	}

	public function configureJS($value) {
		$this->js = is_array($value) ? $value : [];
	}
}
