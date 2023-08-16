<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2019.02.19.
 * Time: 09:53
 */
?>
<form id="wpfs-enter-security-code-form" class="wpfs-form wpfs-w-60" action="" method="post">
	<div class="wpfs-form-title"><?php esc_html_e( 'Manage your subscriptions', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></div>
	<div class="wpfs-form-lead">
		<div class="wpfs-form-description">
			<?php if ( MM_WPFS_Utils::isDemoMode() ) { ?>
				<?php /* translators: Login form field placeholder for security code */ esc_html_e( 'Enter any non-empty security code (DEMO MODE).', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?>
			<?php } else { ?>
				<?php /* translators: Login form field placeholder for security code */ esc_html_e( 'Enter the security code sent to your email address.', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?>
			<?php } ?>
		</div>
	</div>
	<div class="wpfs-form-group wpfs-w-30">
		<input type="text" class="wpfs-form-control" id="wpfs-security-code" name="wpfs-security-code" autocomplete="off" placeholder="<?php esc_attr_e( 'Enter received security code', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?>">
	</div>
	<div class="wpfs-form-actions">
		<button class="wpfs-btn wpfs-btn-primary wpfs-mr-2" type="submit"><?php esc_html_e( 'Sign in', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></button>
		<a class="wpfs-btn wpfs-btn-link wpfs-nav-back-to-email-address"><?php /* translators: Link text to restart the login process  */ esc_html_e( 'Back to e-mail address', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></a>
	</div>
</form>
