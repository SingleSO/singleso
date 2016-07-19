<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;
use yii\widgets\Pjax;

/**
 * @var $this yiiwebView
 * @var $searchModel app\models\oauth2\Oauth2ClientSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = 'Oauth2 Clients';
?>

<?= $this->render('_alert') ?>

<?= $this->render('_menu') ?>

<div class="row">
	<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">

<?php Pjax::begin() ?>

<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'layout' => "{items}\n{pager}",
	'columns' => [
		'client',
		[
			'attribute' => 'created_at',
			'value' => function ($model) {
				$val = $model->created_at;
				if (!$val) {
					return $val;
				}
				if (extension_loaded('intl')) {
					return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [$val]);
				} else {
					return date('Y-m-d G:i:s', $val);
				}
			},
			'filter' => DatePicker::widget([
				'model' => $searchModel,
				'attribute' => 'created_at',
				'dateFormat' => 'php:Y-m-d',
				'options' => [
					'class' => 'form-control',
				],
			]),
		],
		[
			'attribute' => 'updated_at',
			'value' => function ($model) {
				$val = $model->updated_at;
				if (!$val) {
					return $val;
				}
				if (extension_loaded('intl')) {
					return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [$val]);
				} else {
					return date('Y-m-d G:i:s', $val);
				}
			},
			'filter' => DatePicker::widget([
				'model' => $searchModel,
				'attribute' => 'updated_at',
				'dateFormat' => 'php:Y-m-d',
				'options' => [
					'class' => 'form-control',
				],
			]),
		],
		[
			'class' => 'yii\grid\ActionColumn',
			'template' => '{update} {delete}',
		],
	],
]); ?>

<?php Pjax::end() ?>

	</div>
</div>
