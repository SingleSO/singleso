<?php

/**
 * @var $this yiiwebView
 * @var $form yii\widgets\ActiveForm
 */

use dektrium\user\widgets\Connect;
use yii\helpers\Html;

$this->title = Yii::t('user', 'Networks');
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row">
	<div class="col-sm-3 col-sm-offset-1 col-md-3 col-md-offset-2">
		<?= $this->render('_menu') ?>
	</div>
	<div class="col-sm-7 col-md-5">
		<div class="panel panel-default">
			<div class="panel-heading"><?= Html::encode($this->title) ?></div>
			<div class="panel-body">
				<div class="alert alert-info">
					<p><?= Yii::t('user', 'You can connect multiple accounts to be able to log in using them') ?>.</p>
				</div>
				<?php $auth = Connect::begin([
					'baseAuthUrl' => ['/user/security/auth'],
					'accounts'    => $user->accounts,
					'autoRender'  => false,
					'popupMode'   => false,
				]) ?>
				<table class="table">
					<?php foreach ($auth->getClients() as $client): ?>
						<tr>
							<td style="width: 32px; vertical-align: middle">
								<?= Html::tag('span', '', ['class' => 'auth-icon ' . $client->getName()]) ?>
							</td>
							<td style="vertical-align: middle">
								<strong><?= $client->getTitle() ?></strong>
							</td>
							<td style="width: 120px">
								<?= $auth->isConnected($client) ?
									Html::a(Yii::t('user', 'Disconnect'), $auth->createClientUrl($client), [
										'class' => 'btn btn-danger btn-block',
										'data-method' => 'post',
									]) :
									Html::a(Yii::t('user', 'Connect'), $auth->createClientUrl($client), [
										'class' => 'btn btn-success btn-block',
									])
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
				<?php Connect::end() ?>
			</div>
		</div>
	</div>
</div>
