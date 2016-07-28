<?php

/**
 * @var yiiwebView $this
 * @var yii\bootstrap\ActiveForm $form
 * @var app\models\admin\SettingsForm $model
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Settings';
?>

<?= $this->render('_alert') ?>

<div class="row">
	<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
		<h1><?= Html::encode($this->title) ?></h1>
		<?php $form = ActiveForm::begin(['id' => 'admin-settings']); ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Application Settings</h2>
				</div>
				<div class="panel-body">
					<?= $form->field($model, 'application_admin_email') ?>
					<?= $form->field($model, 'application_home_url') ?>
					<?= $form->field($model, 'application_name') ?>
					<?= $form->field($model, 'application_copyright') ?>
					<p>Use <code>{{year}}</code> for the current year.</p>
					<?= $form->field($model, 'application_theme')
						->radioList($model->themes()) ?>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">User Settings</h2>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label class="control-label">Registration Settings</label>
						<?= $form->field($model, 'user_registration_enabled')
							->checkbox() ?>
						<?= $form->field($model, 'user_registration_confirmation')
							->checkbox() ?>
						<?= $form->field($model, 'user_registration_unconfirmed_login')
							->checkbox() ?>
						<?= $form->field($model, 'user_registration_password_recovery')
							->checkbox() ?>
					</div>
					<?= $form->field($model, 'user_registration_confirm_time')
						->label('User Registration Confirm Time (Default: <code>' .
							$model->defaultValue('user_registration_confirm_time') .
							'</code>)') ?>
					<p>Time is in seconds.</p>
					<?= $form->field($model, 'user_registration_recover_time')
						->label('User Registration Recover Time (Default: <code>' .
							$model->defaultValue('user_registration_recover_time') .
							'</code>)') ?>
					<p>Time is in seconds.</p>
					<?= $form->field($model, 'user_login_remember_time')
						->label('User Login Remember Time (Default: <code>' .
							$model->defaultValue('user_login_remember_time') .
							'</code>)') ?>
					<p>Time is in seconds.</p>
					<?= $form->field($model, 'user_name_length_max')
						->label('User Name Max Length (Default: <code>' .
							$model->defaultValue('user_name_length_max') .
							'</code>)') ?>
					<?= $form->field($model, 'user_name_length_min')
						->label('User Name Min Length (Default: <code>' .
							$model->defaultValue('user_name_length_min') .
							'</code>)') ?>
					<?= $form->field($model, 'user_email_length_max')
						->label('Email Max Length (Default: <code>' .
							$model->defaultValue('user_email_length_max') .
							'</code>)') ?>
					<?= $form->field($model, 'user_name_blacklist', ['enableAjaxValidation' => true])
						->textarea(['value' => implode("\n", $model->user_name_blacklist), 'rows' => 10]) ?>
					<p>Prevents users from using a name. Admins are not restricted by the blacklist. One blacklist entry per-line. Supports string matching and regular expressions. String matching is case-insensitive, regex must include <code>i</code> flag for case-insensitive matching. Regular expressions must use <code>/</code> as delimiter, other lines are treated as plain strings.</p>
					<p>Regex Examples:</p>
					<ul>
						<li><code>/^exactname$/</code> (Exactly "exactname")</li>
						<li><code>/caseinsensitivename/i</code> (Contains "caseinsensitivename" of any letter casing.)</li>
					</ul>
					<?= $form->field($model, 'user_profile_fields')
						->checkboxList($model->profileFields()) ?>
					<?= $form->field($model, 'user_profile_fields_all')
						->checkbox() ?>
					<p>Check the all option to include all fields, or select individual fields.</p>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">OAuth 2 Settings</h2>
				</div>
				<div class="panel-body">
					<?= $form->field($model, 'oauth2_code_expire')
						->label('Code Expire Time (Default: <code>' .
							$model->defaultValue('oauth2_code_expire') .
							'</code>)') ?>
					<?= $form->field($model, 'oauth2_token_expire')
						->label('Token Expire Time (Default: <code>' .
							$model->defaultValue('oauth2_token_expire') .
							'</code>)') ?>
					<p>These settings set the time in seconds that OAuth 2 codes and tokens last before expiration. Use negative value to never expire.</p>
					<?= $form->field($model, 'oauth2_endpoint')
						->textinput(['readonly' => true])
						->label('OAuth 2 Endpoint') ?>
					<?= $form->field($model, 'oauth2_loginurl')
						->textinput(['readonly' => true])
						->label('OAuth 2 Login') ?>
					<?= $form->field($model, 'oauth2_registerurl')
						->textinput(['readonly' => true])
						->label('OAuth 2 Register') ?>
					<?= $form->field($model, 'oauth2_logouturl')
						->textinput(['readonly' => true])
						->label('OAuth 2 Logout') ?>
					<?= $form->field($model, 'oauth2_domain_global_cookie_name')
						->textinput(['readonly' => true])
						->label('Domain Global Cookie Name') ?>
					<p>Domain global cookie is the name of a cookie set global for the domain to check if logged in.</p>
					<p>Set by config files like other cookie names. If empty then cookie is disabled.</p>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Pages</h2>
				</div>
				<div class="panel-body">
					<p>Page contents support Markdown and HTML. Empty the page content to remove the page.</p>
					<?= $form->field($model, 'page_about_content')
						->textarea(['rows' => 10])
						->label('About Page Content') ?>
					<?= $form->field($model, 'page_contact_content')
						->textarea(['rows' => 10])
						->label('Contact Page Content') ?>
					<?= $form->field($model, 'page_contact_submitted')
						->label('Contact Page Form Submitted Message') ?>
					<?= $form->field($model, 'page_links')
						->textarea(['rows' => 10])
						->label('Links to Add to Nav Menu') ?>
					<p>A list of links with title and URL pairs. Title on one line, URL on another.</p>
				</div>
			</div>
			<div class="form-group">
				<?= Html::submitButton('Submit', [
					'class' => 'btn btn-primary',
					'name' => 'contact-button'
				]) ?>
			</div>
		<?php ActiveForm::end(); ?>
	</div>
</div>
