<?php

$config = require(__DIR__ . '/../config/web.php');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

(new app\models\ApplicationWeb($config))->run();
