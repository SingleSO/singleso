<?php

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var UserSearch $searchModel
 */

use dektrium\user\models\UserSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\web\View;
use yii\widgets\Pjax;

$this->title = Yii::t('user', 'Manage users');
?>

<?= $this->render('/_alert', [
	'module' => Yii::$app->getModule('user'),
]) ?>

<?= $this->render('/admin/_menu') ?>

<div class="row">
	<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">

<?php Pjax::begin() ?>

<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'layout' => "{items}\n{pager}",
	'columns' => [
		'username',
		'email:email',
		[
			'attribute' => 'registration_ip',
			'value' => function ($model) {
				return $model->registration_ip == null
					? '<span class="not-set">' . Yii::t('user', '(not set)') . '</span>'
					: $model->registration_ip;
			},
			'format' => 'html',
		],
		[
			'attribute' => 'created_at',
			'value' => function ($model) {
				if (extension_loaded('intl')) {
					return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [$model->created_at]);
				} else {
					return date('Y-m-d G:i:s', $model->created_at);
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
		// [
		// 	'header' => Yii::t('user', 'Confirm'),
		// 	'value' => function ($model) {
		// 		if ($model->isConfirmed) {
		// 			return '<div class="text-center"><span class="text-success">' . Yii::t('user', 'Confirmed') . '</span></div>';
		// 		} else {
		// 			return Html::a(Yii::t('user', 'Confirm'), ['confirm', 'id' => $model->id], [
		// 				'class' => 'btn btn-xs btn-success btn-block',
		// 				'data-method' => 'post',
		// 				'data-confirm' => Yii::t('user', 'Are you sure you want to confirm this user?'),
		// 			]);
		// 		}
		// 	},
		// 	'format' => 'raw',
		// 	'visible' => Yii::$app->getModule('user')->enableConfirmation,
		// ],
		// [
		// 	'header' => Yii::t('user', 'Block'),
		// 	'value' => function ($model) {
		// 		if ($model->isBlocked) {
		// 			return Html::a(Yii::t('user', 'Unblock'), ['block', 'id' => $model->id], [
		// 				'class' => 'btn btn-xs btn-success btn-block',
		// 				'data-method' => 'post',
		// 				'data-confirm' => Yii::t('user', 'Are you sure you want to unblock this user?'),
		// 			]);
		// 		} else {
		// 			return Html::a(Yii::t('user', 'Block'), ['block', 'id' => $model->id], [
		// 				'class' => 'btn btn-xs btn-danger btn-block',
		// 				'data-method' => 'post',
		// 				'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?'),
		// 			]);
		// 		}
		// 	},
		// 	'format' => 'raw',
		// ],
		'is_admin',
		[
			'class' => 'yii\grid\ActionColumn',
			'template' => '{update} {delete}',
		],
	],
]); ?>

<?php Pjax::end() ?>

	</div>
</div>
