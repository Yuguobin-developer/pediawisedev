<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2016.04.04.
 * Time: 9:52
 */

$supportUrl = esc_url(
	add_query_arg(
		array(
			'utm_source'   => 'plugin-wpfs',
			'utm_medium'   => 'about-page_help-and-support-tab',
			'utm_campaign' => 'v' . MM_WPFS::VERSION,
			'utm_content'  => 'support-url'
		),
		'https://paymentsplugin.com/support'
	)
);
?>
<div class="help-and-support">
	<p><?php printf( __( 'Check out our <a href="%s" target="_blank">Help section</a> or visit the <a href="%s" target="_blank">Support page</a> if you have questions.', MM_WPFS::L10N_DOMAIN_ADMIN ), admin_url( "admin.php?page=fullstripe-help" ), $supportUrl ); ?></p>
	<p><?php printf( __( 'You can subscribe for premium support for FREE by <a href="%s" target="_blank">adding your email address to our mailing list</a>.', MM_WPFS::L10N_DOMAIN_ADMIN ), 'http://eepurl.com/5zJG1' ); ?></p>
	<a href="http://eepurl.com/5zJG1" target="_blank" class="button button-primary"><?php _e( 'Subscribe for premium support', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
</div>
