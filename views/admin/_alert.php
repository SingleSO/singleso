<div class="row">
	<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
		<?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
			<?php if (in_array($type, ['success', 'danger', 'warning', 'info'])): ?>
				<div class="alert alert-<?= $type ?>">
					<?= $message ?>
				</div>
			<?php endif ?>
		<?php endforeach ?>
	</div>
</div>
