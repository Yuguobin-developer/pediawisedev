<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2016.04.13.
 * Time: 15:28
 */

$options                = get_option( 'fullstripe_options' );
$googleReCAPTCHASiteKey = '';
if ( array_key_exists( MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SITE_KEY, $options ) ) {
	$googleReCAPTCHASiteKey = $options[ MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SITE_KEY ];
}
$googleReCAPTCHASecretKey = '';
if ( array_key_exists( MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SECRET_KEY, $options ) ) {
	$googleReCAPTCHASecretKey = $options[ MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SECRET_KEY ];
}
if ( MM_WPFS_Utils::isDemoMode() ) {
	$googleReCAPTCHASiteKey   = __( 'Google reCaptcha site key here', MM_WPFS::L10N_DOMAIN_ADMIN );
	$googleReCAPTCHASecretKey = __( 'Google reCaptcha secret key here', MM_WPFS::L10N_DOMAIN_ADMIN );
}

?>
<div id="users-tab">
	<form class="form-horizontal" action="#" method="post" id="settings-users-form">
		<p class="tips"></p>
		<input type="hidden" name="action" value="wp_full_stripe_update_settings"/>
		<input type="hidden" name="tab" value="security">
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label class="control-label"><?php esc_html_e( 'Fill in email address for logged in users?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
				</th>
				<td>
					<label class="radio">
						<input type="radio" name="lock_email_field_for_logged_in_users" id="lock_email_field_for_logged_in_users_no" value="0" <?php echo ( $options['lock_email_field_for_logged_in_users'] == '0' ) ? 'checked' : '' ?>>
						<?php esc_html_e( 'No', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</label>
					<label class="radio">
						<input type="radio" name="lock_email_field_for_logged_in_users" id="lock_email_field_for_logged_in_users_yes" value="1" <?php echo ( $options['lock_email_field_for_logged_in_users'] == '1' ) ? 'checked' : '' ?> >
						<?php esc_html_e( 'Yes', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label class="control-label"><?php esc_html_e( 'Secure inline forms with Google reCAPTCHA?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
				</th>
				<td>
					<label class="checkbox">
						<input type="radio" name="secure_inline_forms_with_google_recaptcha" id="secure_inline_forms_with_google_recaptcha_no" value="0" <?php echo ( $options['secure_inline_forms_with_google_recaptcha'] == '0' ) ? 'checked' : '' ?>>
						<?php esc_html_e( 'No', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</label>
					<label class="checkbox">
						<input type="radio" name="secure_inline_forms_with_google_recaptcha" id="secure_inline_forms_with_google_recaptcha_yes" value="1" <?php echo ( $options['secure_inline_forms_with_google_recaptcha'] == '1' ) ? 'checked' : '' ?>>
						<?php esc_html_e( 'Yes', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label class="control-label"><?php esc_html_e( 'Secure checkout forms with Google reCAPTCHA?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
				</th>
				<td>
					<label class="checkbox">
						<input type="radio" name="secure_checkout_forms_with_google_recaptcha" id="secure_checkout_forms_with_google_recaptcha_no" value="0" <?php echo ( $options['secure_checkout_forms_with_google_recaptcha'] == '0' ) ? 'checked' : '' ?>>
						<?php esc_html_e( 'No', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</label>
					<label class="checkbox">
						<input type="radio" name="secure_checkout_forms_with_google_recaptcha" id="secure_checkout_forms_with_google_recaptcha_yes" value="1" <?php echo ( $options['secure_checkout_forms_with_google_recaptcha'] == '1' ) ? 'checked' : '' ?>>
						<?php esc_html_e( 'Yes', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label class="control-label"><?php esc_html_e( 'Secure subscription update with Google reCAPTCHA?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
				</th>
				<td>
					<label class="checkbox">
						<input type="radio" name="secure_subscription_update_with_google_recaptcha" id="secure_subscription_update_with_google_recaptcha_no" value="0" <?php echo ( $options['secure_subscription_update_with_google_recaptcha'] == '0' ) ? 'checked' : '' ?>>
						<?php esc_html_e( 'No', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</label>
					<label class="checkbox">
						<input type="radio" name="secure_subscription_update_with_google_recaptcha" id="secure_subscription_update_with_google_recaptcha_yes" value="1" <?php echo ( $options['secure_subscription_update_with_google_recaptcha'] == '1' ) ? 'checked' : '' ?>>
						<?php esc_html_e( 'Yes', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</label>
				</td>
			</tr>
			<tr valign="top" id="google_recaptcha_site_key_row" <?php echo $options['secure_subscription_update_with_google_recaptcha'] == '0' ? 'style="display: none;"' : '' ?>>
				<th scope="row">
					<label class="control-label"><?php esc_html_e( 'Google reCAPTCHA site key: ', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
				</th>
				<td>
					<input type="text" id="google_recaptcha_site_key" name="google_recaptcha_site_key" class="regular-text code" value="<?php echo esc_attr( $googleReCAPTCHASiteKey ); ?>">
				</td>
			</tr>
			<tr valign="top" id="google_recaptcha_secret_key_row" <?php echo $options['secure_subscription_update_with_google_recaptcha'] == '0' ? 'style="display: none;"' : '' ?>>
				<th scope="row">
					<label class="control-label"><?php esc_html_e( 'Google reCAPTCHA secret key: ', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
				</th>
				<td>
					<input type="text" id="google_recaptcha_secret_key" name="google_recaptcha_secret_key" class="regular-text code" value="<?php echo esc_attr( $googleReCAPTCHASecretKey ); ?>">
				</td>
			</tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label"><?php esc_html_e( 'Show invoices to subscribers?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
                </th>
                <td>
                    <label class="checkbox">
                        <input type="radio" name="<?php echo MM_WPFS::OPTION_MY_ACCOUNT_SHOW_INVOICES_SECTION ?>" id="show_subscriptions_to_subscribers_no" value="0" <?php echo ( $options[MM_WPFS::OPTION_MY_ACCOUNT_SHOW_INVOICES_SECTION] == '0' ) ? 'checked' : '' ?>>
                        <?php esc_html_e( 'No', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                    </label>
                    <label class="checkbox">
                        <input type="radio" name="<?php echo MM_WPFS::OPTION_MY_ACCOUNT_SHOW_INVOICES_SECTION ?>" id="show_subscriptions_to_subscribers_yes" value="1" <?php echo ( $options[MM_WPFS::OPTION_MY_ACCOUNT_SHOW_INVOICES_SECTION] == '1' ) ? 'checked' : '' ?>>
                        <?php esc_html_e( 'Yes', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label"><?php esc_html_e( 'Let subscribers cancel subscriptions?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
                </th>
                <td>
                    <label class="checkbox">
                        <input type="radio" name="<?php echo MM_WPFS::OPTION_MY_ACCOUNT_LET_SUBSCRIBERS_CANCEL_SUBSCRIPTIONS ?>" id="let_subscribers_cancel_subscriptions_no" value="0" <?php echo ( $options[MM_WPFS::OPTION_MY_ACCOUNT_LET_SUBSCRIBERS_CANCEL_SUBSCRIPTIONS] == '0' ) ? 'checked' : '' ?>>
                        <?php esc_html_e( 'No', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                    </label>
                    <label class="checkbox">
                        <input type="radio" name="<?php echo MM_WPFS::OPTION_MY_ACCOUNT_LET_SUBSCRIBERS_CANCEL_SUBSCRIPTIONS ?>" id="let_subscribers_cancel_subscriptions_yes" value="1" <?php echo ( $options[MM_WPFS::OPTION_MY_ACCOUNT_LET_SUBSCRIBERS_CANCEL_SUBSCRIPTIONS] == '1' ) ? 'checked' : '' ?>>
                        <?php esc_html_e( 'Yes', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                    </label>
                </td>
            </tr>
		</table>
		<p class="submit">
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes' ) ?></button>
			<img src="<?php echo MM_WPFS_Assets::images( 'loader.gif' ); ?>" alt="<?php esc_attr_e( 'Loading...', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" class="showLoading"/>
		</p>
	</form>
</div>
