<?php

/*
Plugin Name: WP Full Stripe Members
Plugin URI: https://paymentsplugin.com
Description: Fully featured membership add-on for WP Full Stripe.  Create premium content for subscribers only.
Version: 1.6.4
Author: Mammothology
Author URI: https://paymentsplugin.com
*/

if ( ! defined( 'REQUIRED_WPFS_VERSION' ) ) {
	define( 'REQUIRED_WPFS_VERSION', '5.4.0' );
}

if ( ! defined( 'WPFS_VERSION_3_16_0' ) ) {
	define( 'WPFS_VERSION_3_16_0', '3.16.0' );
}

if ( ! defined( 'WPFS_MEMBERS_NAME' ) ) {
	define( 'WPFS_MEMBERS_NAME', trim( dirname( plugin_basename( __FILE__ ) ), '/' ) );
}

if ( ! defined( 'WPFS_MEMBERS_BASENAME' ) ) {
	define( 'WPFS_MEMBERS_BASENAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'WPFS_MEMBERS_DIR' ) ) {
	define( 'WPFS_MEMBERS_DIR', WP_PLUGIN_DIR . '/' . WPFS_MEMBERS_NAME );
}

$wpfs_domain      = 'wp-full-stripe';
$locale           = apply_filters( 'plugin_locale', get_locale(), $wpfs_domain );
$mofile           = trailingslashit( WP_PLUGIN_DIR ) . $wpfs_domain . '/languages/' . $wpfs_domain . '-' . $locale . '.mo';
$plugin_rel_path2 = basename( dirname( __FILE__ ) ) . '/languages/';

$wpfs_loaded  = load_textdomain( $wpfs_domain, $mofile );
$wpfsm_loaded = load_plugin_textdomain( 'wp-full-stripe-members', false, $plugin_rel_path2 );

if ( ! class_exists( 'MM_WPFSM_LicenseManager' ) ) {
    include( dirname( __FILE__ ) . '/includes/wpfs-members-license-manager.php' );
}

require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'wpfs-members-main.php';
register_activation_hook( __FILE__, array( 'MM_WPFS_Members', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'MM_WPFS_Members', 'deactivate' ) );

MM_WPFSM_LicenseManager::getInstance()->initPluginUpdater();
