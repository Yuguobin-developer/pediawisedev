<?php

class MM_WPFS_Members_Database {
	public static function setup_db() {
		//require for dbDelta()
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $table = $wpdb->prefix . 'fullstripe_members';

		$sql = "CREATE TABLE " . $table . " (
        memberID INT NOT NULL AUTO_INCREMENT,
        role VARCHAR(100) NOT NULL,
        plan VARCHAR(100) NOT NULL,
        email VARCHAR(500) NOT NULL,
        wpUserID INT NOT NULL,
        stripeCustomerID VARCHAR(100),
        stripeSubscriptionID VARCHAR(100),
        stripeSubscriptionStatus VARCHAR(100),
        livemode TINYINT(1) DEFAULT 1,
        created DATETIME NOT NULL,
        UNIQUE KEY memberID (memberID)
        ) $charset_collate;";

		//database write/update
		dbDelta( $sql );
	}

	public function insert_member( $member ) {
		global $wpdb;
		$wpdb->insert( $wpdb->prefix . 'fullstripe_members', $member );

		return $wpdb->insert_id;
	}

	function update_member( $id, $member ) {
		global $wpdb;
		$wpdb->update( $wpdb->prefix . 'fullstripe_members', $member, array( 'memberID' => $id ) );
	}

	public function get_member( $id ) {
		global $wpdb;

		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}fullstripe_members WHERE memberID=%d;", array( $id ) ) );
	}

	public function get_member_by_wpid( $wpUserID, $liveMode ) {
		global $wpdb;

		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}fullstripe_members WHERE livemode=%d AND wpUserID=%s;", array(
			$liveMode,
			$wpUserID
		) ) );
	}

	public function get_member_by_email( $email, $liveMode ) {
		global $wpdb;

		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}fullstripe_members WHERE livemode=%d AND email=%s;", array(
			$liveMode,
			$email
		) ) );
	}

	public function get_members( $liveMode ) {
		global $wpdb;

		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}fullstripe_members WHERE livemode=%d;", array( $liveMode ) ) );
	}

	public function delete_member( $id ) {
		global $wpdb;

		return $wpdb->delete( "{$wpdb->prefix}fullstripe_members", array( 'memberID' => $id ) );
	}

	public function get_members_by_stripe_customer_id( $stripeCustomerID, $liveMode ) {
		global $wpdb;

		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}fullstripe_members WHERE livemode=%d AND stripeCustomerID=%s", array(
			$liveMode,
			$stripeCustomerID
		) ) );
	}

    /**
     * @return array|null|object|void
     */
    public static function get_site_ids() {
        global $wpdb;

        return $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs} WHERE site_id = {$wpdb->siteid};" );
    }
}
