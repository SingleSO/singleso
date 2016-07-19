<?php

namespace app\widgets;

use Yii;
use yii\bootstrap\Nav as NavBase;

class Nav extends NavBase {
	/**
	 * @inheritdoc
	 */
	protected function isItemActive($item) {
		// Let the parent check if active.
		$ret = parent::isItemActive($item);
		// If not already active, check some other things.
		if (!$ret && isset($item['url'][0])) {
			$route = $item['url'][0];
			if ($route[0] !== '/' && Yii::$app->controller) {
				$route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
			}
			$routeTrimmed = ltrim($route, '/');
			// Also check if the index route.
			$ret = $routeTrimmed . '/index' === $this->route;
			// Also check if the user profile.
			if (!$ret) {
				// XXX: Workaround for user profile not following the convention.
				$ret = $this->route === $routeTrimmed ||
					strpos($this->route, $routeTrimmed . '/') !== false;
			}
		}
		return $ret;
	}
}
