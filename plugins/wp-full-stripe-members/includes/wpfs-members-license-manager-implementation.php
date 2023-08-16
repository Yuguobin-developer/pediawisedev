<?php

// Create a helper function for easy SDK access.
function wpfsm_fs()
{
    global  $wpfsm_fs ;
    
    if ( !isset( $wpfsm_fs ) ) {
        // Include Freemius SDK.
        
        if ( file_exists( dirname( WPFS_MEMBERS_DIR ) . '/wp-full-stripe/includes/freemius/start.php' ) ) {
            // Try to load SDK from parent plugin folder.
            require_once dirname( WPFS_MEMBERS_DIR ) . '/wp-full-stripe/includes/freemius/start.php';
        } else {
            
            if ( file_exists( dirname( WPFS_MEMBERS_DIR ) . '/wp-full-stripe-premium/includes/freemius/start.php' ) ) {
                // Try to load SDK from premium parent plugin folder.
                require_once dirname( WPFS_MEMBERS_DIR ) . '/wp-full-stripe-premium/includes/freemius/start.php';
            } else {
                require_once WPFS_MEMBERS_DIR . '/includes/freemius/start.php';
            }
        
        }
        
        $wpfsm_fs = fs_dynamic_init( array(
            'id'               => '2756',
            'slug'             => 'wp-full-stripe-members',
            'type'             => 'plugin',
            'public_key'       => 'pk_3bb1f7e07e228db48ea7b6aea40a9',
            'is_premium'       => true,
            'is_premium_only'  => true,
            'has_paid_plans'   => true,
            'is_org_compliant' => false,
            'parent'           => array(
            'id'         => '2752',
            'slug'       => 'wp-full-stripe',
            'public_key' => 'pk_7c2ed6cc45348be58a5c4ed3b0a84',
            'name'       => 'WP Full Stripe',
        ),
            'menu'             => array(
            'slug'           => 'fullstripe-members',
            'override_exact' => true,
            'contact'        => false,
            'support'        => false,
            'parent'         => array(
            'slug' => 'fullstripe-settings',
        ),
        ),
            'is_live'          => true,
        ) );
    }
    
    return $wpfsm_fs;
}

function wpfsm_fs_settings_url()
{
    return admin_url( 'admin.php?page=fullstripe-members&tab=settings' );
}

function wpfsm_fs_is_parent_active_and_loaded()
{
    // Check if the parent's init SDK method exists.
    return function_exists( 'wpfs_fs' );
}

function wpfsm_fs_is_parent_active()
{
    $active_plugins = get_option( 'active_plugins', array() );
    
    if ( is_multisite() ) {
        $network_active_plugins = get_site_option( 'active_sitewide_plugins', array() );
        $active_plugins = array_merge( $active_plugins, array_keys( $network_active_plugins ) );
    }
    
    foreach ( $active_plugins as $basename ) {
        if ( 0 === strpos( $basename, 'wp-full-stripe/' ) || 0 === strpos( $basename, 'wp-full-stripe-premium/' ) ) {
            return true;
        }
    }
    return false;
}

function wpfsm_fs_init()
{
    
    if ( wpfsm_fs_is_parent_active_and_loaded() ) {
        // Init Freemius.
        wpfsm_fs();
        // Parent is active, add your init code here.
    } else {
        // Parent is inactive, add your error handling here.
    }

}


if ( wpfsm_fs_is_parent_active_and_loaded() ) {
    // If parent already included, init add-on.
    wpfsm_fs_init();
    wpfsm_fs()->add_filter( 'connect_url', 'wpfsm_fs_settings_url' );
    wpfsm_fs()->add_filter( 'after_skip_url', 'wpfsm_fs_settings_url' );
    wpfsm_fs()->add_filter( 'after_connect_url', 'wpfsm_fs_settings_url' );
    wpfsm_fs()->add_filter( 'after_pending_connect_url', 'wpfsm_fs_settings_url' );
} else {
    
    if ( wpfsm_fs_is_parent_active() ) {
        // Init add-on only after the parent is loaded.
        add_action( 'wpfs_fs_loaded', 'wpfsm_fs_init' );
    } else {
        // Even though the parent is not activated, execute add-on for activation / uninstall hooks.
        wpfsm_fs_init();
    }

}

function show_admin_notices()
{
    show_wpfs_inactive_error();
    show_wpfs_incompatible_version_error();
}

add_action( 'admin_notices', 'show_admin_notices' );
function plugin_admin_init()
{
    $deactivate_plugin = false;
    $deactivate_plugin = $deactivate_plugin || !is_wpfs_active();
    $deactivate_plugin = $deactivate_plugin || !has_wpfs_min_version();
    if ( $deactivate_plugin ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
    }
}

add_action( 'admin_init', 'plugin_admin_init' );
// HELPER FUNCTIONS
function is_wpfs_active()
{
    if ( !is_admin() ) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    return is_plugin_active( 'wp-full-stripe/wp-full-stripe.php' );
}

function has_wpfs_min_version()
{
    if ( class_exists( 'MM_WPFS' ) ) {
        return version_compare( MM_WPFS::VERSION, REQUIRED_WPFS_VERSION, '>=' );
    }
    return false;
}

function show_error( $message )
{
    echo  "<div class=\"error\"><p>{$message}</p></div>" ;
}

function show_wpfs_inactive_error()
{
    
    if ( !is_wpfs_active() ) {
        $message = __( 'WP Full Stripe Members cannot be activated when WP Full Stripe is not active. Please install/activate WP Full Stripe.', 'wp-full-stripe-members' );
        show_error( $message );
        return true;
    }
    
    return false;
}

function show_wpfs_incompatible_version_error()
{
    if ( is_wpfs_active() ) {
        
        if ( !has_wpfs_min_version() ) {
            $message = sprintf( __( 'Please update your WP Full Stripe plugin. Required version: %s.', 'wp-full-stripe-members' ), REQUIRED_WPFS_VERSION );
            show_error( $message );
            return true;
        }
    
    }
    return false;
}

class MM_WPFSM_LicenseManager extends MM_WPFSM_LicenseManager_Root
{
    public static  $instance ;
    private function __construct()
    {
    }
    
    public static function getInstance()
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = new MM_WPFSM_LicenseManager();
        }
        return self::$instance;
    }
    
    public function initPluginUpdater()
    {
    }
    
    public function getLicenseOptionDefaults()
    {
        //-- No options used by Freemius
        return array();
    }
    
    public function setLicenseOptionDefaultsIfEmpty( &$options )
    {
        //-- No options used by Freemius
        return;
    }
    
    public function activateLicenseIfNeeded()
    {
        //-- No need to activate license in Freemius
    }

}