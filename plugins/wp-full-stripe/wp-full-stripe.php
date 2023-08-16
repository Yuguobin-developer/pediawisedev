<?php
/*
Plugin Name: WP Full Stripe (Premium)
Plugin URI: https://paymentsplugin.com
Description: Complete Stripe payments integration for Wordpress
Author: Mammothology
Version: 5.5.2
Author URI: https://paymentsplugin.com
Text Domain: wp-full-stripe
Domain Path: /languages
*/

//defines

// define( 'WP_FULL_STRIPE_DEMO_MODE', true );

define( 'WP_FULL_STRIPE_MIN_PHP_VERSION', '5.5.0' );
define( 'WP_FULL_STRIPE_MIN_WP_VERSION', '4.0.0' );
define( 'WP_FULL_STRIPE_STRIPE_API_VERSION', '7.24.0' );
define( 'WP_FULL_STRIPE_CRON_SCHEDULES_KEY_15_MIN', '15min' );


if ( ! defined( 'WP_FULL_STRIPE_NAME' ) ) {
	define( 'WP_FULL_STRIPE_NAME', trim( dirname( plugin_basename( __FILE__ ) ), '/' ) );
}

if ( ! defined( 'WP_FULL_STRIPE_BASENAME' ) ) {
	define( 'WP_FULL_STRIPE_BASENAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'WP_FULL_STRIPE_DIR' ) ) {
	define( 'WP_FULL_STRIPE_DIR', plugin_dir_path( __FILE__ ) );
}

// tnagy check minimum requirements
if ( version_compare( PHP_VERSION, WP_FULL_STRIPE_MIN_PHP_VERSION ) == - 1 ) {
	wp_die( plugin_basename( __FILE__ ) . ': ' . sprintf(
	    /* translators:
	     * p1: Required version of PHP
	     * p2: Current version of PHP */
	    __( 'The minimum PHP version for running WP Full Stripe is %1$s but %2$s is found.<br/><br/>Please press the "Back" browser button, upgrade PHP, and activate the plugin again.', MM_WPFS::L10N_DOMAIN_ADMIN ), WP_FULL_STRIPE_MIN_PHP_VERSION, PHP_VERSION ) );
}
if ( version_compare( get_bloginfo( 'version' ), WP_FULL_STRIPE_MIN_WP_VERSION ) == - 1 ) {
	wp_die( plugin_basename( __FILE__ ) . ': ' . sprintf(
        /* translators:
         * p1: Required version of Wordpress
         * p2: Current version of Wordpress */
	    __( 'The minimum WordPress version for running WP Full Stripe is %1$s but %2$s is found.<br/><br/>Please press the "Back" browser button, upgrade Wordpress, and activate the plugin again.', MM_WPFS::L10N_DOMAIN_ADMIN ), WP_FULL_STRIPE_MIN_WP_VERSION, get_bloginfo( 'version' ) ) );
}
if ( extension_loaded( 'curl' ) === false ) {
	wp_die( plugin_basename( __FILE__ ) . ': ' . sprintf(
        /* translators:
         * p1: Name of required extension
         * p2: Name of required extension */
	    __( 'WP Full Stripe cannot find a required PHP extension called "%1$s".<br/><br/>Please press the "Back" browser button, install/enable "%2$s" for PHP, and activate the plugin again.', MM_WPFS::L10N_DOMAIN_ADMIN ), 'cURL', 'cURL' ) );
}
if ( extension_loaded( 'mbstring' ) === false ) {
	wp_die( plugin_basename( __FILE__ ) . ': ' . sprintf(
        /* translators:
         * p1: Name of required extension
         * p2: Name of required extension */
	    __( 'WP Full Stripe cannot find a required PHP extension called "%1$s".<br/><br/>Please press the "Back" browser button, install/enable "%2$s" for PHP, and activate the plugin again.', MM_WPFS::L10N_DOMAIN_ADMIN ), 'MBString', 'MBString' ) );
}

// Stripe PHP library
if ( ! class_exists( '\StripeWPFS\StripeWPFS' ) ) {
	require_once( dirname( __FILE__ ) . '/includes/stripe/init.php' );
} else {
	if ( substr( \StripeWPFS\StripeWPFS::VERSION, 0, strpos( \StripeWPFS\StripeWPFS::VERSION, '.' ) ) != substr( WP_FULL_STRIPE_STRIPE_API_VERSION, 0, strpos( WP_FULL_STRIPE_STRIPE_API_VERSION, '.' ) ) ) {
		$reflector = new ReflectionClass( '\StripeWPFS\StripeWPFS' );
		wp_die( plugin_basename( __FILE__ ) . ': ' . __( 'Another plugin has loaded an incompatible Stripe API client. Deactivate all other Stripe plugins, and try to activate WP Full Stripe again.', MM_WPFS::L10N_DOMAIN_ADMIN ) . ' ' . \StripeWPFS\StripeWPFS::VERSION . ' != ' . WP_FULL_STRIPE_STRIPE_API_VERSION . ', ' . $reflector->getFileName() );
	}
}
// Sodium Compat library
if ( ! class_exists( 'ParagonIE_Sodium_Compat' ) ) {
	require_once( dirname( __FILE__ ) . '/vendor/paragonie/sodium_compat/autoload.php' );
}

if ( ! class_exists( 'MM_WPFS_LicenseManager' ) ) {
	include( dirname( __FILE__ ) . '/includes/wp-full-stripe-license-manager.php' );
}

require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'wp-full-stripe-main.php';
register_activation_hook( __FILE__, array( 'MM_WPFS', 'setup_db' ) );
register_activation_hook( __FILE__, array( 'MM_WPFS_CardUpdateService', 'onActivation' ) );
register_deactivation_hook( __FILE__, array( 'MM_WPFS_CardUpdateService', 'onDeactivation' ) );
register_activation_hook( __FILE__, array( 'MM_WPFS_CheckoutSubmissionService', 'onActivation' ) );
register_deactivation_hook( __FILE__, array( 'MM_WPFS_CheckoutSubmissionService', 'onDeactivation' ) );

\StripeWPFS\StripeWPFS::setAppInfo( 'WP Full Stripe', MM_WPFS::VERSION, 'https://paymentsplugin.com', 'pp_partner_FnULHViL0IqHp6' );


MM_WPFS_LicenseManager::getInstance()->initPluginUpdater();


function wp_full_stripe_load_plugin_textdomain() {
	load_plugin_textdomain( 'wp-full-stripe', false, basename( dirname( __FILE__ ) ) . '/languages/' );
    load_plugin_textdomain( 'wp-full-stripe-admin', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}

function wp_full_stripe_prepare_cron_schedules( $schedules ) {
	if ( ! isset( $schedules[ WP_FULL_STRIPE_CRON_SCHEDULES_KEY_15_MIN ] ) ) {
		$schedules[ WP_FULL_STRIPE_CRON_SCHEDULES_KEY_15_MIN ] = array(
			'interval' => 15 * 60,
			'display'  =>
                /* translators: Textual description of how often a periodic task of the plugin runs */
                __( 'Every 15 minutes', MM_WPFS::L10N_DOMAIN_PUBLIC )
		);
	}

	return $schedules;
}

add_action( 'plugins_loaded', 'wp_full_stripe_load_plugin_textdomain' );
add_filter( 'cron_schedules', 'wp_full_stripe_prepare_cron_schedules' );
