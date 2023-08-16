<?php

class MM_WPFS_Members_Admin_Menu {
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		add_filter( 'fullstripe_settings_nav_tab_items', array( $this, 'get_settings_nav_tab_items' ) );
		add_filter( 'fullstripe_settings_email_receipt_templates', array(
			$this,
			'get_settings_email_receipt_templates'
		) );
		add_action( 'fullstripe_admin_menus', array( $this, 'admin_menu' ), 11 );
		// This ensures members/admin.js is loaded on wp full stripe admin pages
		add_action( 'fullstripe_admin_scripts', array( $this, 'members_admin_scripts' ), 11 );
	}

	public function admin_menu( $menu_slug ) {
		// Add onto the Full Stripe menu
		$capability = 'manage_options';
		if ( defined( 'WPFS_MEMBERS_DEMO' ) ) {
			$capability = 'read';
		}
		$submenu_page_title = 'Members';
		$submenu_title      = 'Members';
		$submenu_slug       = 'fullstripe-members';
		$submenu_function   = array( $this, 'display_members_page' );
		$menu_hook          = add_submenu_page( $menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function );
		add_action( 'admin_print_scripts-' . $menu_hook, array( $this, 'admin_scripts' ) );
		add_action( 'admin_print_scripts-' . $menu_hook, array( $this, 'admin_styles' ) );
		add_action( 'admin_print_scripts-' . $menu_hook, array( $this, 'admin_import_scripts_and_styles' ) );

		//edit  page - don't show on submenu
		$submenu_page_title = 'Edit Member';
		$submenu_title      = 'Edit Member';
		$submenu_slug       = 'wpfs-members-edit';
		$submenu_function   = array( $this, 'display_edit_page' );
		$menu_hook          = add_submenu_page( null, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function );
		add_action( 'admin_print_scripts-' . $menu_hook, array( $this, 'admin_scripts' ) );

		//new member  page - don't show on submenu
		$submenu_page_title = 'Create Member';
		$submenu_title      = 'Create Member';
		$submenu_slug       = 'wpfs-members-create';
		$submenu_function   = array( $this, 'display_create_page' );
		$menu_hook          = add_submenu_page( null, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function );
		add_action( 'admin_print_scripts-' . $menu_hook, array( $this, 'admin_scripts' ) );
	}

	public function admin_scripts() {
		$options = get_option( 'fullstripe_options' );
		wp_enqueue_script( 'stripe-js', 'https://js.stripe.com/v2/', array( 'jquery' ) );
		wp_enqueue_script( 'wpfs-members-admin-js', plugins_url( '/assets/js/admin.js', dirname( __FILE__ ) ), array( 'jquery' ) );
		if ( $options['apiMode'] === 'test' ) {
			wp_localize_script( 'wpfs-members-admin-js', 'stripekey', $options['publishKey_test'] );
		} else {
			wp_localize_script( 'wpfs-members-admin-js', 'stripekey', $options['publishKey_live'] );
		}
		wp_localize_script( 'wpfs-members-admin-js', 'admin_ajaxurl', admin_url( 'admin-ajax.php' ) );
	}

	public function admin_styles() {
		wp_register_style( 'fullstripe-members-admin-css', plugins_url( '/assets/css/fullstripe-members-admin.css', dirname( __FILE__ ) ), array(
			'fullstripe-ui-css',
			'fullstripe-admin-css'
		), MM_WPFS_Members::VERSION );

		wp_enqueue_style( 'fullstripe-members-admin-css' );
	}

	public function admin_import_scripts_and_styles() {
		// tnagy register scripts and styles
		wp_register_script( 'bootstrap-js-3.3.5', plugins_url( '/assets/js/bootstrap.min.js', dirname( __FILE__ ) ), array( 'jquery' ), '3.3.5' );
		wp_register_style( 'bootstrap-css-3.3.5', plugins_url( '/assets/css/bootstrap.min.css', dirname( __FILE__ ) ), null, '3.3.5' );
		wp_register_style( 'bootstrap-theme-css-3.3.5', plugins_url( '/assets/css/bootstrap-theme.min.css', dirname( __FILE__ ) ), null, '3.3.5' );
		wp_register_style( 'bootstrap-fonts-3.3.5', plugins_url( '/assets/fonts/glyphicons-halflings-regular.svg', dirname( __FILE__ ) ), null, '3.3.5' );

		// tnagy load scripts and styles
		wp_enqueue_style( 'bootstrap-css-3.3.5' );
		wp_enqueue_style( 'bootstrap-theme-css-3.3.5' );
		wp_enqueue_script( 'jquery-bootstrap-modal-js', plugins_url( '/assets/js/jquery-bootstrap-modal-steps.min.js', dirname( __FILE__ ) ), array(
			'jquery',
			'bootstrap-js-3.3.5'
		) );
		wp_enqueue_script( 'spin-js', plugins_url( '/assets/js/spin.min.js', dirname( __FILE__ ) ) );

	}

	public function members_admin_scripts() {
		wp_enqueue_script( 'wpfs-members-admin-js', plugins_url( '/assets/js/admin.js', dirname( __FILE__ ) ), array( 'jquery' ) );
	}

	public function display_members_page() {
		if ( ! class_exists( 'WP_List_Table' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}
		if ( ! class_exists( 'WPFS_Base_Table' ) ) {
			/** @noinspection PhpIncludeInspection */
			require_once( MM_WPFS_Members::get_wpfs_include_path( 'wp-full-stripe-tables.php' ) );
		}
		if ( ! class_exists( 'WPFS_Members_Table' ) ) {
			require_once( WPFS_MEMBERS_DIR . '/includes/wpfs-members-tables.php' );
		}

		$membersTable = new WPFS_Members_Table();

		include WPFS_MEMBERS_DIR . '/templates/members_page.php';
	}

	public function display_edit_page() {
		include WPFS_MEMBERS_DIR . '/templates/edit_member_page.php';
	}

	public function display_create_page() {
		include WPFS_MEMBERS_DIR . '/templates/create_member_page.php';
	}

	public function get_settings_nav_tab_items( array $nav_tab_items ) {
		$item_members = array(
			'tab'     => 'members',
			'caption' => __( 'Member Settings', 'wp-full-stripe-members' ),
			'content' => WPFS_MEMBERS_DIR . '/templates/partials/settings_tab_members.php'
		);
		$item_roles   = array(
			'tab'     => 'roles',
			'caption' => __( 'Member Roles', 'wp-full-stripe-members' ),
			'content' => WPFS_MEMBERS_DIR . '/templates/partials/settings_tab_roles.php'
		);

		array_push( $nav_tab_items, $item_members );
		array_push( $nav_tab_items, $item_roles );

		return $nav_tab_items;
	}

	public function get_settings_email_receipt_templates( array $email_receipt_templates ) {
		$registration_successful          = new stdClass();
		$registration_successful->id      = 'registrationSuccessful';
		$registration_successful->caption = __( 'Registration successful', 'wp-full-stripe-members' );

		array_push( $email_receipt_templates, $registration_successful );

		return $email_receipt_templates;
	}

}