<?php
$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'news';

$paymentsPluginUrl = esc_url(
	add_query_arg(
		array(
			'utm_source'   => 'plugin-wpfs',
			'utm_medium'   => 'about-page',
			'utm_campaign' => 'v' . MM_WPFS::VERSION,
			'utm_content'  => 'mammothology-url'
		),
		'https://paymentsplugin.com/'
	)
);

?>
<div class="wrap about-wrap">

	<h2><?php printf( __( 'Welcome to WP Full Stripe', MM_WPFS::L10N_DOMAIN_ADMIN ) . ' (v%s)', MM_WPFS::VERSION ); ?></h2>

	<div class="about-text">
		<p><?php printf( __( 'Accept payments and subscriptions from your WordPress website. Created by <a target="_blank" href="%s">Mammothology</a><div class=""></div>', MM_WPFS::L10N_DOMAIN_ADMIN ), $paymentsPluginUrl ); ?></p>
	</div>

	<h2 class="nav-tab-wrapper">
		<a href="<?php echo admin_url( 'admin.php?page=fullstripe-about&tab=news' ); ?>" class="nav-tab <?php echo $active_tab == 'news' ? 'nav-tab-active' : ''; ?>"><?php _e( 'News', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
		<a href="<?php echo admin_url( 'admin.php?page=fullstripe-about&tab=help_and_support' ); ?>" class="nav-tab <?php echo $active_tab == 'help_and_support' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Help & Support', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
		<a href="<?php echo admin_url( 'admin.php?page=fullstripe-about&tab=changelog' ); ?>" class="nav-tab <?php echo $active_tab == 'changelog' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Changelog', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
	</h2>

	<div class="wpfs-tab-content">
		<?php
		if ( $active_tab == 'news' ) {
			include( MM_WPFS_Assets::templates( 'admin/partials/about_news.php' ));
		} elseif ( $active_tab == 'help_and_support' ) {
			include( MM_WPFS_Assets::templates( 'admin/partials/about_help_and_support.php' ));
		} elseif ( $active_tab == 'changelog' ) {
			include( MM_WPFS_Assets::templates( 'admin/partials/about_changelog.php' ));
		}
		?>
	</div>

</div>