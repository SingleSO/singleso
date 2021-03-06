<?php

use yii\bootstrap\Nav;

?>
<div class="row">
	<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
		<?= Nav::widget([
			'options' => [
				'class' => 'nav-tabs',
				'style' => 'margin-bottom: 15px',
			],
			'items' => [
				[
					'label' => 'Clients',
					'url' => ['/oauth2-client/index'],
				],
				[
					'label' => Yii::t('user', 'Create'),
					'url' => ['/oauth2-client/create'],
				],
			],
		]) ?>
	</div>
</div>
