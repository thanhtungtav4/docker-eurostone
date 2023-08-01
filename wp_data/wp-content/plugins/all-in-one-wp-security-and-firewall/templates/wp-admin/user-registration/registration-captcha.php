<?php if (!defined('ABSPATH')) die('No direct access.'); ?>
<div class="aio_blue_box">
	<?php
		echo '<p>'.__('This feature allows you to add a CAPTCHA form on the WordPress registration page.', 'all-in-one-wp-security-and-firewall').
		'<br>'.__('Users who attempt to register will also need to enter the answer to a simple mathematical question - if they enter the wrong answer, the plugin will not allow them to register.', 'all-in-one-wp-security-and-firewall').
		'<br>'.__('Therefore, adding a CAPTCHA form on the registration page is another effective yet simple spam registration prevention technique.', 'all-in-one-wp-security-and-firewall').'</p>';
	?>
</div>
<div class="postbox">
	<h3 class="hndle"><label for="title"><?php _e('Registration page CAPTCHA settings', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
	<div class="inside">
		<?php
		// Display security info badge
		$aiowps_feature_mgr->output_feature_details_badge("user-registration-captcha");
		?>
		<form action="" method="POST">
			<?php wp_nonce_field('aiowpsec-registration-captcha-settings-nonce'); ?>
			<?php AIOWPSecurity_Captcha::warning_captcha_settings_notset(); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Enable CAPTCHA on registration page', 'all-in-one-wp-security-and-firewall'); ?>:</th>
					<td>
					<input id="aiowps_enable_registration_page_captcha" name="aiowps_enable_registration_page_captcha" type="checkbox"<?php if ('1' == $aio_wp_security->configs->get_value('aiowps_enable_registration_page_captcha')) echo ' checked="checked"'; ?> value="1"/>
					<label for="aiowps_enable_registration_page_captcha" class="description"><?php _e('Check this if you want to insert a CAPTCHA form on the WordPress user registration page (if you allow user registration).', 'all-in-one-wp-security-and-firewall'); ?></label>
					</td>
				</tr>
			</table>
			<input type="submit" name="aiowpsec_save_registration_captcha_settings" value="<?php _e('Save settings', 'all-in-one-wp-security-and-firewall'); ?>" class="button-primary">
		</form>
	</div>
</div>