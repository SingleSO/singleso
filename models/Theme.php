<?php

namespace app\models;

use Yii;
use app\models\Config;
use yii\base\Model;

class Theme extends Model {

	public $name = null;
	public $sourcePath = null;
	public $css = null;
	public $js = null;
	public $depends = null;
	public $bootstrap = null;
	public $juiTheme = null;

	protected static $themesPath = '@app/themes';

	public function rules() {
		return [
			'nameString' => ['name', 'string'],
			'nameRequired' => ['name', 'required'],

			'sourcePathString' => ['sourcePath', 'string'],
			'sourcePathRequired' => ['sourcePath', 'required'],

			'cssArray' => ['css', 'each', 'rule' => ['string']],

			'jsArray' => ['js', 'each', 'rule' => ['string']],

			'dependsArray' => ['depends', 'each', 'rule' => ['string']],

			'bootstrapString' => ['bootstrap', 'vaidateStringNullBool'],

			'juiThemeString' => ['juiTheme', 'vaidateStringNullBool'],
		];
	}

	public function vaidateStringNullBool($key, $params) {
		$val = $this->$key;
		if (!(is_string($val) || $val === null || $val === false || $val === true)) {
			$this->addError($key, 'Invalid value');
		}
	}

	public static function theme() {
		$themeName = Config::setting('application.theme');
		if (!$themeName) {
			return null;
		}
		return static::loadTheme($themeName);
	}

	public static function loadTheme($name) {
		// Resolve aliases and paths.
		$themeAlias = static::$themesPath . '/' . $name;
		$themeDir = Yii::getAlias($themeAlias);
		$themeJson = $themeDir . '/theme.json';
		// Try to load and parse the JSON file.
		$themeJsonStr = @file_get_contents($themeJson);
		if (!$themeJsonStr) {
			return null;
		}
		$themeData = @json_decode($themeJsonStr, true);
		if (!is_array($themeData)) {
			return null;
		}
		// Add the theme alias the sourcePath.
		$themeData['sourcePath'] = isset($themeData['sourcePath']) ?
			($themeAlias . '/' . $themeData['sourcePath']) :
			$themeAlias;
		// Create a theme instance.
		$theme = new static();
		// Manually assign properties to avoid unknown property exceptions.
		foreach ($themeData as $k=>&$v) {
			if ($theme->hasProperty($k)) {
				$theme->$k = $v;
			}
		}
		unset($v);
		// Validate the JSON and return theme or null.
		return $theme->validate() ? $theme : null;
	}

	public static function enumerate($includeDefault = true) {
		$themes = $includeDefault ? ['' => 'Default'] : [];
		$themesDir = Yii::getAlias(static::$themesPath);
		foreach (scandir($themesDir) as $entry) {
			// Filter for directories excluding dot directories.
			if ($entry[0] === '.' || !is_dir($themesDir . '/' . $entry)) {
				continue;
			}
			// Try to load as a theme, if successful, register theme.
			if (($theme = static::loadTheme($entry))) {
				$themes[$entry] = $theme->name;
			}
		}
		return $themes;
	}
}
