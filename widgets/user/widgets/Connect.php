<?php

namespace app\widgets\user\widgets;

use yii\helpers\Html;
use dektrium\user\widgets\Connect as BaseConnect;

class Connect extends BaseConnect {

	protected static $itemPrefix;
	protected static $tabIndex;

	public function clientLink($client, $text = null, array $htmlOptions = []) {
		// Capture the parent HTML.
		ob_start();
		parent::clientLink($client, $text, $htmlOptions);
		$html = ob_get_clean();
		// Find the end of open title element.
		$offset = strpos($html, 'auth-title');
		$offset = strpos($html, '>', $offset) + 1;
		// Inject some text in between them.
		$html = substr($html, 0, $offset) .
			(self::$itemPrefix ? Html::encode(self::$itemPrefix) : '') .
			substr($html, $offset);
		// Find end of open icon element.
		$svg = null;
		$svg_file = __DIR__ . '/icons/' . $client->getName() . '.svg';
		if (is_file($svg_file)) {
			$svg = @file_get_contents($svg_file);
		}
		$offset = strpos($html, 'auth-icon');
		$offset = strpos($html, '>', $offset) + 1;
		$html = substr($html, 0, $offset) .
			($svg ? $svg : '') .
			substr($html, $offset);
		// Insert the tab index if specified.
		$offset = strpos($html, '>');
		$html = substr($html, 0, $offset) .
			(isset(self::$tabIndex) ?
				' tabindex="' . self::$tabIndex . '"' : ''
			) .
			substr($html, $offset);
		echo $html;
	}

	public static function widget($config = []) {
		// Extract the extra option.
		if (isset($config['itemPrefix'])) {
			self::$itemPrefix = $config['itemPrefix'];
			unset($config['itemPrefix']);
		}
		self::$tabIndex = null;
		if (isset($config['tabIndex'])) {
			self::$tabIndex = $config['tabIndex'];
			unset($config['tabIndex']);
		}
		return parent::widget($config);
	}
}
