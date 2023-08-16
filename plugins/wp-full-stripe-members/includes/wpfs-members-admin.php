<?php

/**
 * Class MM_WPFS_Members_Admin
 *
 * Deal with all member admin like adding new members, checking subscriptions changing member status and so on.
 *
 */
class MM_WPFS_Members_Admin {

	const SESSION_KEY_IMPORTED_SUBSCRIBERS = 'wpfsm_imported_subscribers';
	const IMPORT_STATUS_ABORT_DUE_TO_TEST_MODE = 'ABORT_DUE_TO_TEST_MODE';
	const IMPORT_STATUS_ABORT_DUE_TO_NO_SUBSCRIPTIONS = 'ABORT_DUE_TO_NO_SUBSCRIPTIONS';
	const IMPORT_STATUS_ABORT_DUE_TO_NO_PLANS_MATCHING_SUBSCRIPTIONS = 'ABORT_DUE_TO_NO_PLANS_MATCHING_SUBSCRIPTIONS';
	const IMPORT_STATUS_ABORT_DUE_TO_AMBIGUOUS_PLANS = 'ABORT_DUE_TO_AMBIGUOUS_PLANS';
	const IMPORT_STATUS_SUCCESSFULLY_CREATED = 'SUCCESSFULLY_CREATED';
	const IMPORT_STATUS_SUCCESSFULLY_UPDATED = 'SUCCESSFULLY_UPDATED';
	const TEMPLATE_TYPE_MEMBERS_REGISTRATION_SUCCESSFUL = 'MembersRegistrationSuccessful';

	/** @var MM_WPFS_Members_Database */
	private $db;
	/** @var MM_WPFS_Members_Stripe_Service */
	private $stripe_service;

	public function __construct() {

		include_once 'wpfs-members-database.php';
		include_once 'wpfs-members-stripe-service.php';

		$this->db             = new MM_WPFS_Members_Database();
		$this->stripe_service = new MM_WPFS_Members_Stripe_Service();

		// tnagy prepare session for use during import
		add_action( 'admin_init', array( $this, 'start_session' ), 1 );
		add_action( 'wp_logout', array( $this, 'end_session' ) );
		add_action( 'wp_login', array( $this, 'end_session' ) );

		//attach to the action after a successful subscription setup by wp full stripe
		add_action( 'fullstripe_after_subscription_charge', array( $this, 'add_member' ), 10, 2 );
		add_action( 'fullstripe_after_checkout_subscription_charge', array( $this, 'add_member' ), 10, 2 );
		//make sure we check when a user logs in that they are still subscribed, if not, change role
		add_action( 'wp_login', array( $this, 'check_member_status_login' ), 10, 2 );

		global $wp_version;
		if ( version_compare( $wp_version, '4.5.0', '<' ) ) {
			error_log( 'WPFS Members WARN MM_WPFS_Members_Admin->__construct(): Adding custom implementation of email/password authentication.' );
			add_filter( 'authenticate', array( $this, 'wpfsm_authenticate_email_password' ), 20, 3 );
		}

		// members or admin changing  membership plan & role
		add_action( 'wp_ajax_nopriv_wpfs_members_change_level', array( $this, 'change_level' ) );
		add_action( 'wp_ajax_wpfs_members_change_level', array( $this, 'change_level' ) );
		// members or admin cancelling membership
		add_action( 'wp_ajax_nopriv_wpfs_members_cancel', array( $this, 'cancel_membership' ) );
		add_action( 'wp_ajax_wpfs_members_cancel', array( $this, 'cancel_membership' ) );
		// members updating card
		add_action( 'wp_ajax_nopriv_wpfs_members_update_card', array( $this, 'update_card' ) );
		add_action( 'wp_ajax_wpfs_members_update_card', array( $this, 'update_card' ) );
		//admin changing just the role on edit member page
		add_action( 'wp_ajax_nopriv_wpfs_members_change_role', array( $this, 'change_role' ) );
		add_action( 'wp_ajax_wpfs_members_change_role', array( $this, 'change_role' ) );
		//admin manually creating a new member
		add_action( 'wp_ajax_wpfs_members_manual_create_member', array( $this, 'manual_create_member' ) );
		// tnagy admin manually deleting an existing member
		add_action( 'wp_ajax_wpfs_members_delete_member', array( $this, 'manual_delete_member' ) );

		// tnagy admin importing subscribers from stripe
		add_action( 'wp_ajax_wpfs_members_import_subscribers_from_stripe_step1', array(
			$this,
			'import_subscribers_from_stripe_step1'
		) );
		add_action( 'wp_ajax_wpfs_members_import_subscribers_from_stripe_step3', array(
			$this,
			'import_subscribers_from_stripe_step3'
		) );
		add_action( 'wp_ajax_wpfs_members_import_subscribers_from_stripe_step5', array(
			$this,
			'import_subscribers_from_stripe_step5'
		) );
	}

	/**
	 * Starts a session if not started already.
	 *
	 * @author tnagy
	 */
	function start_session() {
		if ( ! session_id() ) {
			session_start();
		}
	}

	/**
	 * Ends a previously started session.
	 *
	 * @author tnagy
	 */
	function end_session() {
		if ( session_id() ) {
			session_destroy();
		}
	}

	/**
	 * Here we must add (or update) a wordpress user and assign them the role associated with this subscription plan,
	 * if any. WPFS provides additional data according to this subscription like placeholders with values.
	 *
	 * @param $stripeCustomer
	 * @param $additionalData
	 */
	function add_member( $stripeCustomer, $additionalData = array() ) {

		try {
			// Get stripe and role data
			$email        = $stripeCustomer->email;
			$subscription = $stripeCustomer->subscriptions->data[0];
			$planID       = $subscription->plan->id;
			// Get the role name for this plan, if any
			$planRole = MM_WPFS_Members::getInstance()->get_role_for_plan( $planID );
			if ( ! $planRole ) {
				return;
			} // No role assigned to this plan means it's not a membership subscription

			// First check if the user exists
			$wpUserID = username_exists( $email );
			if ( ! $wpUserID ) {
				// We also want to check that the email isn't already registered to another user
				$wpUserID = email_exists( $email );
				if ( ! $wpUserID ) {
					$additionalMacros      = null;
					$additionalMacroValues = null;
					if ( array_key_exists( MM_WPFS_Utils::ADDITIONAL_DATA_KEY_MACROS, $additionalData ) ) {
						$additionalMacros = $additionalData[ MM_WPFS_Utils::ADDITIONAL_DATA_KEY_MACROS ];
					}
					if ( array_key_exists( MM_WPFS_Utils::ADDITIONAL_DATA_KEY_MACRO_VALUES, $additionalData ) ) {
						$additionalMacroValues = $additionalData[ MM_WPFS_Utils::ADDITIONAL_DATA_KEY_MACRO_VALUES ];
					}
					if ( ! is_array( $additionalMacros ) || is_null( $additionalMacros ) ) {
						$additionalMacros = array();
					}
					if ( ! is_array( $additionalMacroValues ) || is_null( $additionalMacroValues ) ) {
						$additionalMacroValues = array();
					}
					$this->create_new_user( $stripeCustomer, $planRole, $additionalMacros, $additionalMacroValues );
				} else // add the role to this user
				{
					$this->update_existing_user( $wpUserID, $stripeCustomer, $planRole );
				}
			} else // update the role of this user
			{
				$this->update_existing_user( $wpUserID, $stripeCustomer, $planRole );
			}
		} catch ( Exception $ex ) {
			MM_WPFS_Utils::log( __FUNCTION__ . "(): " . $ex );
		}
	}

	private function create_new_user( $stripeCustomer, $planRole, $additionalMacros, $additionalMacroValues ) {
		$password = wp_generate_password( $length = 12, $include_standard_special_chars = false );

		// Create a user with the email as username
		$user_id      = wp_create_user( $stripeCustomer->email, $password, $stripeCustomer->email );
		$subscription = $stripeCustomer->subscriptions->data[0];
		// Add new role to the new users
		$user = get_user_by( 'id', $user_id );
		$user->add_role( $planRole ); //this updates the user metadata in wpdb
		// Save in the DB
		$this->db->insert_member( array(
			'role'                     => $planRole,
			'plan'                     => $subscription->plan->id,
			'email'                    => $stripeCustomer->email,
			'wpUserID'                 => $user_id,
			'stripeCustomerID'         => $stripeCustomer->id,
			'stripeSubscriptionID'     => $subscription->id,
			'stripeSubscriptionStatus' => $subscription->status,
			'livemode'                 => $stripeCustomer->livemode,
			'created'                  => date( 'Y-m-d H:i:s', $stripeCustomer->created )
		) );
		//Send registration email
		$this->email_new_user( $stripeCustomer->email, $password, $additionalMacros, $additionalMacroValues );
	}

	private function email_new_user( $recipientEmail, $password, $additionalMacros, $additionalMacroValues ) {
		$options = get_option( 'fullstripe_options' );
		$name    = html_entity_decode( get_bloginfo( 'name' ) );

		$emailReceipts = json_decode( $options['email_receipts'] );
		$subject       = $emailReceipts->registrationSuccessful->subject;
		$message       = stripslashes( $emailReceipts->registrationSuccessful->html );

		$searchArray  = array_merge( array(
			"%NAME%",
			"%USERNAME%",
			"%PASSWORD%"
		), $additionalMacros );
		$replaceArray = array_merge( array(
			$name,
			$recipientEmail,
			$password
		), $additionalMacroValues );
		$message      = str_replace(
			$searchArray,
			$replaceArray,
			$message );

		$this->sendEmail( $recipientEmail, $subject, $message );
	}


	private function sendEmail( $recipientEmail, $subject, $message ) {
		$options = get_option( 'fullstripe_options' );

		$name = html_entity_decode( get_bloginfo( 'name' ) );

		$admin_email  = get_bloginfo( 'admin_email' );
		$sender_email = $admin_email;
		if ( isset( $options['email_receipt_sender_address'] ) && ! empty( $options['email_receipt_sender_address'] ) ) {
			$sender_email = $options['email_receipt_sender_address'];
		}
		$headers[] = "From: $name <$sender_email>";

		$headers[] = "Content-type: text/html";

		wp_mail( $recipientEmail,
			apply_filters( MM_WPFS::FILTER_NAME_MODIFY_EMAIL_SUBJECT, $subject, self::TEMPLATE_TYPE_MEMBERS_REGISTRATION_SUCCESSFUL, '' ),
			apply_filters( MM_WPFS::FILTER_NAME_MODIFY_EMAIL_MESSAGE, $message, self::TEMPLATE_TYPE_MEMBERS_REGISTRATION_SUCCESSFUL, '' ),
			apply_filters( 'fullstripe_email_headers_filter', $headers ) );

		if ( $options['admin_payment_receipt'] == 'website_admin' || $options['admin_payment_receipt'] == 'sender_address' ) {
			$receipt_to = $admin_email;
			if ( $options['admin_payment_receipt'] == 'sender_address' && isset( $options['email_receipt_sender_address'] ) && ! empty( $options['email_receipt_sender_address'] ) ) {
				$receipt_to = $options['email_receipt_sender_address'];
			}
			wp_mail( $receipt_to,
				"COPY: " . apply_filters( MM_WPFS::FILTER_NAME_MODIFY_EMAIL_SUBJECT, $subject, self::TEMPLATE_TYPE_MEMBERS_REGISTRATION_SUCCESSFUL, '' ),
				apply_filters( MM_WPFS::FILTER_NAME_MODIFY_EMAIL_MESSAGE, $message, self::TEMPLATE_TYPE_MEMBERS_REGISTRATION_SUCCESSFUL, '' ),
				apply_filters( 'fullstripe_email_headers_filter', $headers ) );
		}
	}

	private function update_existing_user( $wpUserID, $stripeCustomer, $planRole ) {
		$user = get_user_by( 'id', $wpUserID );
		$user->add_role( $planRole );

		// Check if they have been a member before
		$member       = $this->db->get_member_by_wpid( $wpUserID, MM_WPFS_Members::get_api_mode() );
		$subscription = $stripeCustomer->subscriptions->data[0];
		if ( $member ) {
			$this->db->update_member( $member->memberID, array(
				'role'                     => $planRole,
				'plan'                     => $subscription->plan->id,
				'stripeSubscriptionID'     => $subscription->id,
				'stripeSubscriptionStatus' => $subscription->status,
			) );
		} else {
			// Save in the DB
			$this->db->insert_member( array(
				'role'                     => $planRole,
				'plan'                     => $subscription->plan->id,
				'email'                    => $stripeCustomer->email,
				'wpUserID'                 => $wpUserID,
				'stripeCustomerID'         => $stripeCustomer->id,
				'stripeSubscriptionID'     => $subscription->id,
				'stripeSubscriptionStatus' => $subscription->status,
				'livemode'                 => $stripeCustomer->livemode,
				'created'                  => date( 'Y-m-d H:i:s', $stripeCustomer->created )
			) );
		}
	}

	function check_member_status_login( $user_login, $user ) {
		try {
			$member = $this->db->get_member_by_wpid( $user->ID, MM_WPFS_Members::get_api_mode() );
			$this->check_member_status( $member );

		} catch ( Exception $e ) {
			error_log( 'WPFS Members ERROR check_member_status_login(): ' . sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
		}
	}

	/**
	 * Get the wpfs member from the WP_User $user, check Stripe for subscription status and update role if not
	 * subscribed any more.
	 *
	 * TODO: We could also use webhooks instead of this login/cron approach
	 *
	 * @param $member
	 */
	function check_member_status( $member ) {

		if ( ! isset( $member ) ) {
			return;
		}

		if ( isset( $member->stripeCustomerID ) && ! empty( $member->stripeCustomerID ) ) {
			// Check subscription status. Note Stripe is loaded because WP Full Stripe.
			$stripe_customer = $this->stripe_service->retrieve_active_customer( $member->stripeCustomerID );
		} else {
			error_log( 'WPFS Members WARN check_member_status(): Cannot check member status because \'stripeCustomerID\' property is not set or it is empty for member=' . print_r( $member, true ) );
		}

		if ( ! isset( $stripe_customer ) ) {
			return;
		}

		if ( isset( $stripe_customer->subscriptions ) && count( $stripe_customer->subscriptions->data ) > 0 ) {

			$invalid_statuses = array( 'unpaid', 'canceled' );
			$options          = get_option( 'fullstripe_options' );
			if ( $options['wpfs_members_block_past_due'] == 1 ) {
				$invalid_statuses[] = 'past_due';
			}

			// Find the subscription related to this membership signup.
			$found = false;
			foreach ( $stripe_customer->subscriptions->data as $sub ) {
				if ( $sub->id == $member->stripeSubscriptionID ) {
					if ( in_array( $sub->status, $invalid_statuses ) ) {
						$this->remove_membership_role( $member, $sub->status );
					}
					$found = true;
					break;
				}
			}

			// The subscription this member signed up with is no longer on the Stripe
			// customer details, meaning it's been deleted/canceled.
			if ( ! $found ) {
				$this->remove_membership_role( $member, 'deleted' );
			}
		} else {
			// not subscribed to anything...shouldn't happen.
			$this->remove_membership_role( $member, 'unknown' );
		}
	}

	function remove_membership_role( $member, $newStatus ) {
		// Remove the role and update the member record
		$user = get_user_by( 'id', $member->wpUserID );
		$user->remove_role( $member->role );
		$user->add_role( 'wpfs_no_access' );
		$this->db->update_member( $member->memberID, array(
			'role'                     => 'wpfs_no_access',
			'stripeSubscriptionStatus' => $newStatus
		) );
	}

	/**
	 * This allows users to put their email address in the username field on login.
	 * WordPress offers this functionality after 4.5.0.
	 *
	 * This function is a slightly modified version of wp_authenticate_email_password() from WordPress 4.5.0+ (wp-includes/user.php).
	 *
	 * @param $user
	 * @param $email
	 * @param $password
	 *
	 * @return WP_Error|WP_User
	 */
	function wpfsm_authenticate_email_password( $user, $email, $password ) {
		if ( $user instanceof WP_User ) {
			return $user;
		}
		if ( ! empty( $email ) && ! empty( $password ) ) {

			if ( ! is_email( $email ) ) {
				return $user;
			}

			$user = get_user_by( 'email', $email );

			if ( ! $user ) {
				$lost_password_html = ' <a href="' . wp_lostpassword_url() . '">' . __( 'Lost your password?' ) . '</a>';
				$result             = new WP_Error( 'invalid_username', __( '<strong>ERROR</strong>: Invalid username.' ) . $lost_password_html );

				return $result;
			}

			$user = apply_filters( 'wp_authenticate_user', $user, $password );
			if ( is_wp_error( $user ) ) {
				return $user;
			}

			if ( ! wp_check_password( $password, $user->user_pass, $user->ID ) ) {
				$message            = sprintf(
				/* translators: %s: user name */
					__( '<strong>ERROR</strong>: The password you entered for the username %s is incorrect.' ),
					'<strong>' . $email . '</strong>'
				);
				$lost_password_html = ' <a href="' . wp_lostpassword_url() . '">' . __( 'Lost your password?' ) . '</a>';

				$result = new WP_Error( 'incorrect_password', $message . $lost_password_html );

				return $result;
			}

			return $user;
		} else {
			if ( is_wp_error( $user ) ) {
				return $user;
			}

			$result = new WP_Error();
			if ( empty( $email ) ) {
				$result->add( 'empty_username', __( '<strong>ERROR</strong>: The username field is empty.' ) );
			}

			if ( empty( $password ) ) {
				$result->add( 'empty_password', __( '<strong>ERROR</strong>: The password field is empty.' ) );
			}

			return $result;
		}

	}

	// Here we just change the role with no regard to the associated subscription plan.
	// Should only be used with care - currently a button only for admins on edit member page.

	/**
	 * A member has selected a new subscription plan
	 */
	public function change_level() {
		$memberID = $_POST['memberID'];
		$newPlan  = $_POST['wpfs_members_level'];
		$newRole  = $_POST['role'];
		$return   = array( 'success' => true );

		$member = $this->db->get_member( $memberID );
		if ( $member ) {
			$subscriptionUpdated = false;
			try {
				$this->member_change_level( $member, $newPlan );
				$subscriptionUpdated = true;
			} catch ( Exception $e ) {
				$return = array(
					'success' => false,
					'msg'     => MM_WPFS_Members::translate_label( $e->getMessage() ),
					'ex_msg'  => $e->getMessage()
				);
			}

			// if Stripe was updated successfully, update our own records
			if ( $subscriptionUpdated ) {
				// change WP user role
				$user = get_user_by( 'id', $member->wpUserID );
				$user->remove_role( $member->role );
				$user->add_role( $newRole );

				// update DB
				$this->db->update_member( $memberID, array(
					'role' => $newRole,
					'plan' => $newPlan
				) );
			}
		} else {
			$return = array(
				'success' => false,
				'msg'     => __( 'No member found. Please re-log and try again.', 'wp-full-stripe-members' )
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	/**
	 * Updates the subscription plan of the member in Stripe, the WPFSM database, and the WPFS database.
	 *
	 * @param $member mixed Member database record
	 * @param $new_plan string Id of the new subscription plan
	 *
	 * @throws Exception
	 */
	function member_change_level( $member, $new_plan ) {
		$this->changeLevelInWPFSDatabase( $member, $new_plan );
		$this->changeLevelInStripe( $member, $new_plan );
	}

	/**
	 * Updates the subscription plan id of the member in the WPFS database.
	 *
	 * @param $member mixed Member database record
	 * @param $new_plan_id string Id of the new subscription plan
	 *
	 * @throws Exception
	 */
	function changeLevelInWPFSDatabase( $member, $new_plan_id ) {
		$newPlan        = $this->stripe_service->retrieve_plan( $new_plan_id );
		$maxChargeCount = $this->getMaxChargeCountFromPlan( $newPlan );

		$this->updateSubscriptionPlanAndCounters( $member->stripeSubscriptionID, $new_plan_id, $maxChargeCount );
	}

	/**
	 * Returns the value of the 'cancellation_count' metadata of the plan.
	 *
	 * @param $plan \StripeWPFS\Plan Subscription plan
	 *
	 * @return integer
	 */
	function getMaxChargeCountFromPlan( $plan ) {
		$maxChargeCount = 0;

		if ( isset( $plan->metadata ) && ! empty( $plan->metadata ) ) {
			$metadata = $plan->metadata;

			if ( isset( $metadata['cancellation_count'] ) ) {
				$maxChargeCount = $metadata['cancellation_count'];
			}
		}

		return $maxChargeCount;
	}

	/**
	 * Updates the subscription plan id of the subscriber in the WPFS database.
	 *
	 * @param $subscriptionId string Id of the subscription to be updated.
	 * @param $planId string Id of the new subscription plan
	 * @param $chargeMaxCount integer The cancellation count of the new plan.
	 *
	 * @throws Exception
	 */
	function updateSubscriptionPlanAndCounters( $subscriptionId, $planId, $chargeMaxCount ) {
		$wpfsClazz = MM_WPFS::getInstance();

		if ( method_exists( $wpfsClazz, 'updateSubscriptionPlanAndCounters' ) ) {
			$wpfsClazz->updateSubscriptionPlanAndCounters( $subscriptionId, $planId, $chargeMaxCount );
		}
	}

	/**
	 * Updates the subscription plan in Stripe.
	 *
	 * @param $member mixed Member database record
	 * @param $new_plan string Id of the new subscription plan
	 *
	 * @throws Exception
	 */
	function changeLevelInStripe( $member, $new_plan ) {
		$stripe_customer = $this->stripe_service->retrieve_active_customer( $member->stripeCustomerID );
		if ( isset( $stripe_customer ) ) {
			$subscription = $this->stripe_service->retrieve_customer_subscription( $stripe_customer, $member->stripeSubscriptionID );
			if ( isset( $subscription ) ) {
				$subscription->plan = $new_plan;
				$subscription->save();
			}
		}
	}

	/**
	 * A member chose to cancel their membership
	 */
	function cancel_membership() {
		$memberID = $_POST['memberID'];
		$return   = array( 'success' => true );

		$member = $this->db->get_member( $memberID );
		if ( $member ) {
			$subscriptionUpdated = false;
			try {
				$this->member_cancel_membership( $member );
				$subscriptionUpdated = true;
			} catch ( Exception $e ) {
				$return = array(
					'success' => false,
					'msg'     => MM_WPFS_Members::translate_label( $e->getMessage() ),
					'ex_msg'  => $e->getMessage()
				);
			}

			// if Stripe was updated successfully, update our own records
			if ( $subscriptionUpdated ) {
				// Might as well use our own check_status function that will update DB and WP Role as needed,
				// though it is slightly less efficient because it loops through all subscriptions...
				$this->check_member_status( $member );
			}
		} else {
			$return = array(
				'success' => false,
				'msg'     => __( 'No member found. Please re-log and try again.', 'wp-full-stripe-members' )
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function member_cancel_membership( $member ) {
		if ( isset( $member->stripeSubscriptionID ) ) {
			$this->stripe_service->cancelSubscriptionAtPeriodEnd( $member->stripeSubscriptionID );
		}
	}

	function update_card() {

		$memberID = $_POST['memberID'];
		$card     = $_POST['stripeToken'];
		$return   = array( 'success' => true );

		$member = $this->db->get_member( $memberID );
		if ( $member ) {
			try {
				$this->member_update_card( $member, $card );
			} catch ( \StripeWPFS\Error\Card $e ) {
				$message = MM_WPFS_Members::resolve_error_message_by_code( $e->getCode() );
				error_log( 'resolved message=' . $message );
				if ( is_null( $message ) ) {
					$message = MM_WPFS_Members::translate_label( $e->getMessage() );
				}
				$return = array(
					'success' => false,
					'msg'     => $message,
					'ex_msg'  => $e->getMessage()
				);
			} catch ( Exception $e ) {
				error_log( 'exception class=' . get_class( $e ) );
				$message = $e->getMessage();
				$return  = array(
					'success' => false,
					'msg'     => MM_WPFS_Members::translate_label( $message ),
					'ex_msg'  => $message
				);
			}
		} else {
			$return = array(
				'success' => false,
				'msg'     => __( 'No member found. Please re-log and try again.', 'wp-full-stripe-members' )
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function member_update_card( $member, $card ) {
		$stripe_customer = $this->stripe_service->retrieve_active_customer( $member->stripeCustomerID );
		if ( isset( $stripe_customer ) ) {
			// tnagy save card as source, Stripe will delete the previous default source and it'll set the new source
			// as default
			$stripe_customer->source = $card;
			$stripe_customer->save();
			// tnagy retrieve the modified Customer object
			$stripe_customer = $this->stripe_service->retrieve_active_customer( $member->stripeCustomerID );
			// tnagy save the new default source as default payment method
			$stripe_customer->invoice_settings->default_payment_method = $stripe_customer->default_source;
			$stripe_customer->save();
		}
	}

	function change_role() {
		$memberID = $_POST['memberID'];
		$newRole  = $_POST['wpfs_member_role'];
		$return   = array( 'success' => true );

		$member = $this->db->get_member( $memberID );
		if ( $member ) {
			$user = get_user_by( 'id', $member->wpUserID );
			$user->remove_role( $member->role );
			$user->add_role( $newRole );
			$this->db->update_member( $member->memberID, array(
				'role' => $newRole
			) );
		} else {
			$return = array(
				'success' => false,
				'msg'     => __( 'No member found. Please re-log and try again.', 'wp-full-stripe-members' )
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function manual_create_member() {
		$wpUserID             = $_POST['wpfs_members_wp_user_id'];
		$email                = $_POST['wpfs_members_email'];
		$plan                 = $_POST['wpfs_members_plan'];
		$role                 = $_POST['wpfs_members_role'];
		$stripeCustomerID     = $_POST['wpfs_members_customer_id'];
		$stripeSubscriptionID = $_POST['wpfs_members_subscription_id'];
		$liveMode             = $_POST['wpfs_members_live_mode'];
		$return               = array( 'success' => true );

		// Add new role to the new users
		$user = get_user_by( 'id', $wpUserID );
		$user->add_role( $role ); //this updates the user metadata in wpdb

		// Save in the DB
		$this->db->insert_member( array(
			'role'                     => $role,
			'plan'                     => $plan,
			'email'                    => $email,
			'wpUserID'                 => $wpUserID,
			'stripeCustomerID'         => $stripeCustomerID,
			'stripeSubscriptionID'     => $stripeSubscriptionID,
			'stripeSubscriptionStatus' => 'active',
			'livemode'                 => $liveMode,
			'created'                  => date( 'Y-m-d H:i:s' )
		) );

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function manual_delete_member() {
		$id      = sanitize_text_field( $_POST['id'] );
		$member  = $this->db->get_member( $id );
		$success = false;
		if ( ! is_null( $member ) ) {
			// tnagy remove role when other membership don't need it
			$user = get_user_by( 'id', $member->wpUserID );
			if ( ! is_null( $user ) && $user ) {
				$removeRole = true;
				$members    = $this->db->get_members_by_stripe_customer_id( $member->stripeCustomerID, MM_WPFS_Members::get_api_mode() );
				foreach ( $members as $aMember ) {
					if ( $aMember->memberID != $member->memberID ) {
						if ( $aMember->role == $member->role ) {
							$removeRole = false;
						}
					}
				}
				if ( $removeRole ) {
					$user->remove_role( $member->role );
				}
			}
			// tnagy delete member
			$this->db->delete_member( $id );

			$success = true;
		}

		$return = array(
			'success'     => $success,
			'redirectURL' => add_query_arg( array(
				'page' => 'fullstripe-members',
				'tab'  => 'members'
			), admin_url( 'admin.php' ) )
		);

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	/**
	 * This is the first step of the process to import Stripe customers as WPFS Members
	 *
	 * @author tnagy
	 */
	function import_subscribers_from_stripe_step1() {

		$response = array();

		$this->reset_in_session();

		$total_count                    = - 1;
		$total_count_with_subscriptions = 0;
		$last_customer                  = null;
		$has_more                       = false;

		try {

			do {
				$params = array( "limit" => 100, "include[]" => "total_count" );
				if ( ! is_null( $last_customer ) ) {
					$params["starting_after"] = $last_customer;
				}

				$list_all_response = \StripeWPFS\Customer::all( $params );
				$has_more          = $list_all_response->has_more;

				if ( $total_count == - 1 ) {
					$total_count = $list_all_response->total_count;
				}

				foreach ( $list_all_response->data as $customer ) {
					if ( isset( $customer->subscriptions ) ) {
						if ( ! empty( $customer->subscriptions->data ) ) {
							$total_count_with_subscriptions += 1;
						}
					}
				}

				if ( $has_more ) {
					$last_customer = $list_all_response->data[ count( $list_all_response->data ) - 1 ]["id"];
				}


			} while ( $has_more );

			$response['status'] = 'OK';

		} catch ( Exception $e ) {
			error_log( $e );
			$response['status'] = 'ERROR';
		}

		$response['total_count']                    = $total_count;
		$response['total_count_with_subscriptions'] = $total_count_with_subscriptions;

		header( "Content-Type: application/json" );
		echo json_encode( $response );
		exit;

	}

	//////////////////////////
	///  Private functions
	//////////////////////////

	/**
	 * Initialize an empty array in session for Stripe customers
	 *
	 * @author tnagy
	 */
	private function reset_in_session() {
		$session_key              = self::SESSION_KEY_IMPORTED_SUBSCRIBERS;
		$_SESSION[ $session_key ] = array();
	}

	/**
	 * This is the third step of the process to import Stripe customers as WPFS Members
	 *
	 * @author tnagy
	 */
	function import_subscribers_from_stripe_step3() {
		$response = $this->init_response();

		$this->reset_in_session();
		$from_session = null;

		$total_count   = - 1;
		$page          = 0;
		$last_customer = null;
		$has_more      = 0;

		try {

			// tnagy load customers
			do {

				$page += 1;
				$params = array( "limit" => 100, "include[]" => "total_count" );
				if ( ! is_null( $last_customer ) ) {
					$params["starting_after"] = $last_customer;
				}

				$list_all_response = \StripeWPFS\Customer::all( $params );
				$has_more          = $list_all_response->has_more;

				$from_session          = $this->get_from_session();
				$from_session[ $page ] = $list_all_response;
				$this->store_to_session( $from_session );

				if ( $total_count == - 1 ) {
					$total_count = $list_all_response->total_count;
				}

				if ( $has_more ) {
					$last_customer = $list_all_response->data[ count( $list_all_response->data ) - 1 ]["id"];
				}

			} while ( $has_more );

			if ( $total_count != - 1 ) {
				$response['total_count'] = $total_count;
			}

			// tnagy import customers
			$from_session = $this->get_from_session();

			if ( isset( $from_session ) && is_array( $from_session ) ) {

				foreach ( $from_session as $page => $customer_list ) {

					foreach ( $customer_list->data as $customer ) {

						$result = $this->import_member( $customer );

						$this->handle_result( $result, $customer, $response );

					}

				}
			}

			if ( $total_count != - 1 ) {
				$response['total_count_with_subscriptions'] = $total_count - $response['no_subscriptions'];
			}

			$response['status'] = 'OK';

		} catch ( Exception $e ) {
			error_log( $e );
			$this->remove_from_session();
			$response = array(
				'status' => 'ERROR'
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $response );
		exit;

	}

	/**
	 * @return mixed
	 */
	private function init_response() {
		$response                          = array();
		$response['imported_successfully'] = 0;
		$response['no_subscriptions']      = 0;
		$response['test_mode']             = array();
		$response['cannot_import']         = array();
		$response['can_import_manually']   = array();

		return $response;
	}

	/**
	 * Gets the Stripe customers' array from session
	 *
	 * @return array
	 *
	 * @author tnagy
	 */
	private function get_from_session() {
		$session_key = self::SESSION_KEY_IMPORTED_SUBSCRIBERS;
		if ( isset( $_SESSION[ $session_key ] ) ) {
			return $_SESSION[ $session_key ];
		} else {
			return array();
		}
	}

	/**
	 * Stores Stripe customers to session
	 *
	 * @param $value
	 *
	 * @author tnagy
	 */
	private function store_to_session( $value ) {
		$session_key              = self::SESSION_KEY_IMPORTED_SUBSCRIBERS;
		$_SESSION[ $session_key ] = $value;
	}

	/**
	 * @param $stripe_customer
	 * @param null $preferred_plan_id
	 *
	 * @return array
	 * @author tnagy
	 */
	function import_member( $stripe_customer, $preferred_plan_id = null ) {

		$result = array();

		$result['live_mode'] = $stripe_customer->livemode;

		// Get stripe and role data
		$email = $stripe_customer->email;

		$role_for_preferred_plan = null;
		if ( isset( $preferred_plan_id ) ) {
			$role_for_preferred_plan = MM_WPFS_Members::getInstance()->get_role_for_plan( $preferred_plan_id );
		}

		$plans = array();
		if ( isset( $stripe_customer->subscriptions ) ) {
			if ( ! empty( $stripe_customer->subscriptions->data ) ) {
				foreach ( $stripe_customer->subscriptions->data as $subscription ) {
					$role_for_plan = MM_WPFS_Members::getInstance()->get_role_for_plan( $subscription->plan->id );
					if ( ! is_null( $role_for_plan ) ) {

						$add_plan = true;
						if ( isset( $role_for_preferred_plan ) ) {
							if ( $role_for_plan !== $role_for_preferred_plan ) {
								$add_plan = false;
							}
						}
						if ( $add_plan ) {
							$plans[] = array(
								'id'                  => $subscription->plan->id,
								'role'                => $role_for_plan,
								'subscription_id'     => $subscription->id,
								'subscription_status' => $subscription->status
							);
						}
					}
				}
			} else {
				$result['status'] = self::IMPORT_STATUS_ABORT_DUE_TO_NO_SUBSCRIPTIONS;

				return $result;
			}
		}

		$result['plans'] = $plans;

		if ( empty( $plans ) ) {
			$result['status'] = self::IMPORT_STATUS_ABORT_DUE_TO_NO_PLANS_MATCHING_SUBSCRIPTIONS;

			return $result;
		}

		if ( count( $plans ) > 1 ) {
			$result['status'] = self::IMPORT_STATUS_ABORT_DUE_TO_AMBIGUOUS_PLANS;

			return $result;
		}

		// First check if the user exists
		$wp_user_id = username_exists( $email );
		if ( ! $wp_user_id ) {
			// We also want to check that the email isn't already registered to another user
			$wp_user_id = email_exists( $email );
			if ( ! $wp_user_id ) {
				$this->import__create_new_user( $stripe_customer, $plans[0] );
				$wp_user_id       = username_exists( $email );
				$result['status'] = self::IMPORT_STATUS_SUCCESSFULLY_CREATED;
			} else {
				// add the role to this user
				$this->import__update_existing_user( $wp_user_id, $stripe_customer, $plans[0] );
				$result['status'] = self::IMPORT_STATUS_SUCCESSFULLY_UPDATED;
			}
		} else {
			// update the role of this user
			$this->import__update_existing_user( $wp_user_id, $stripe_customer, $plans[0] );
			$result['status'] = self::IMPORT_STATUS_SUCCESSFULLY_UPDATED;
		}

		$result['wp_user_id'] = $wp_user_id;

		return $result;
	}

	/**
	 * Create new user by Import process
	 *
	 * @param $stripe_customer
	 * @param $plan
	 */
	private function import__create_new_user( $stripe_customer, $plan ) {
		$password = wp_generate_password( $length = 20, $include_standard_special_chars = false );

		// Create a user with the email as username
		$user_id = wp_create_user( $stripe_customer->email, $password, $stripe_customer->email );
		// Add new role to the new users
		$user = get_user_by( 'id', $user_id );
		// tnagy remove existing roles
		foreach ( $user->roles as $role ) {
			$user->remove_role( $role );
		}
		// tnagy add role by plan
		$user->add_role( $plan['role'] ); //this updates the user metadata in wpdb

		// tnagy look for existing wpfs member by email
		$member = $this->db->get_member_by_email( $stripe_customer->email, MM_WPFS_Members::get_api_mode() );
		if ( $member ) {
			$this->db->update_member( $member->memberID, array(
				'role'                     => $plan['role'],
				'plan'                     => $plan['id'],
				'wpUserID'                 => $user_id,
				'stripeCustomerID'         => $stripe_customer->id,
				'stripeSubscriptionID'     => $plan['subscription_id'],
				'stripeSubscriptionStatus' => $plan['subscription_status'],
				'livemode'                 => $stripe_customer->livemode,
				'created'                  => date( 'Y-m-d H:i:s', $stripe_customer->created )
			) );
		} else {
			// Save in the DB
			$this->db->insert_member( array(
				'role'                     => $plan['role'],
				'plan'                     => $plan['id'],
				'email'                    => $stripe_customer->email,
				'wpUserID'                 => $user_id,
				'stripeCustomerID'         => $stripe_customer->id,
				'stripeSubscriptionID'     => $plan['subscription_id'],
				'stripeSubscriptionStatus' => $plan['subscription_status'],
				'livemode'                 => $stripe_customer->livemode,
				'created'                  => date( 'Y-m-d H:i:s', $stripe_customer->created )
			) );
		}
	}

	/**
	 * Update existing user by Import process
	 *
	 * @param $wp_user_id
	 * @param $stripe_customer
	 * @param $plan
	 *
	 * @author tnagy
	 */
	private function import__update_existing_user( $wp_user_id, $stripe_customer, $plan ) {
		$user = get_user_by( 'id', $wp_user_id );
		$user->add_role( $plan['role'] );

		// Check if they have been a member before
		$member = $this->db->get_member_by_wpid( $wp_user_id, MM_WPFS_Members::get_api_mode() );
		if ( $member ) {
			$this->db->update_member( $member->memberID, array(
				'role'                     => $plan['role'],
				'plan'                     => $plan['id'],
				'stripeSubscriptionID'     => $plan['subscription_id'],
				'stripeSubscriptionStatus' => $plan['subscription_status'],
			) );
		} else {
			// Save in the DB
			$this->db->insert_member( array(
				'role'                     => $plan['role'],
				'plan'                     => $plan['id'],
				'email'                    => $stripe_customer->email,
				'wpUserID'                 => $wp_user_id,
				'stripeCustomerID'         => $stripe_customer->id,
				'stripeSubscriptionID'     => $plan['subscription_id'],
				'stripeSubscriptionStatus' => $plan['subscription_status'],
				'livemode'                 => $stripe_customer->livemode,
				'created'                  => date( 'Y-m-d H:i:s', $stripe_customer->created )
			) );
		}
	}

	/**
	 * @param $result
	 * @param $customer
	 * @param $response
	 */
	private function handle_result( $result, $customer, &$response ) {
		$response['live_mode'] = $result['live_mode'];
		switch ( $result['status'] ) {
			case self::IMPORT_STATUS_ABORT_DUE_TO_NO_SUBSCRIPTIONS:
				$response['no_subscriptions'] += 1;
				break;
			case self::IMPORT_STATUS_ABORT_DUE_TO_NO_PLANS_MATCHING_SUBSCRIPTIONS:
				$response['cannot_import'][] = $this->createCustomerArray( $result['status'], $customer );
				break;
			case self::IMPORT_STATUS_ABORT_DUE_TO_AMBIGUOUS_PLANS:
				$availablePlansForCustomer = array();
				$availablePlans            = $this->get_available_plans();
				foreach ( $availablePlans as $plan ) {
					foreach ( $result['plans'] as $subscriptionPlan ) {
						if ( $plan['plan'] === $subscriptionPlan['id'] ) {
							$availablePlansForCustomer[] = $plan;
						}
					}
				}
				$response['can_import_manually'][] = $this->createCustomerArray( $result['status'], $customer, $availablePlansForCustomer );
				break;
			case self::IMPORT_STATUS_SUCCESSFULLY_CREATED:
			case self::IMPORT_STATUS_SUCCESSFULLY_UPDATED:
				$response['imported_successfully'] += 1;
				break;
		}
	}

	/**
	 * @param $reason
	 * @param $customer
	 * @param null $availablePlans
	 *
	 * @return array
	 */
	private function createCustomerArray( $reason, $customer, $availablePlans = null ) {
		$customerArray = array();
		if ( isset( $customer ) ) {
			if ( isset( $customer->metadata ) && isset( $customer->metadata->customer_name ) ) {
				$customerArray['name'] = $customer->metadata->customer_name;
			}
			if ( isset( $customer->email ) ) {
				$customerArray['email'] = $customer->email;
			} else {
				$customerArray['email'] = null;
			}
			if ( isset( $customer->id ) ) {
				$customerArray['customer_id'] = $customer->id;
			} else {
				$customerArray['customer_id'] = null;
			}
		}
		if ( isset( $availablePlans ) ) {
			$customerArray['available_plans'] = $availablePlans;
		}
		if ( isset( $reason ) ) {
			$customerArray['reason'] = $reason;
		}

		return $customerArray;
	}

	/**
	 * @return array
	 */
	private function get_available_plans() {
		$available_plans = array();
		$role_plans      = MM_WPFS_Members::getInstance()->get_role_plans();
		$role_names      = MM_WPFS_Members::getInstance()->get_wp_role_names();
		foreach ( $role_plans as $role => $plan ) {
			$available_plans[] = array(
				'role'         => $role,
				'plan'         => $plan,
				'display_name' => $role_names[ $role ]
			);
		}

		return $available_plans;
	}

	/**
	 * Removes Stripe customers from session
	 *
	 * @author tnagy
	 */
	private function remove_from_session() {
		$session_key = self::SESSION_KEY_IMPORTED_SUBSCRIBERS;
		unset( $_SESSION[ $session_key ] );
	}

	/**
	 * This is the fifth step of the process to import Stripe customers as WPFS Members
	 *
	 * @author tnagy
	 */
	function import_subscribers_from_stripe_step5() {

		$response = $this->init_response();

		try {

			$plans = $_POST['plans'];

			if ( isset( $plans ) && is_array( $plans ) ) {

				$from_session = $this->get_from_session();

				if ( isset( $from_session ) && is_array( $from_session ) ) {
					foreach ( $from_session as $page => $customer_list ) {
						foreach ( $customer_list->data as $customer ) {

							$preferred_plan_id = $this->find_preferred_plan( $customer->id, $plans );

							if ( ! is_null( $preferred_plan_id ) ) {
								$result = $this->import_member( $customer, $preferred_plan_id );

								$this->handle_result( $result, $customer, $response );

							}
						}
					}
				}

			}

			$response['status'] = 'OK';

			$this->remove_from_session();

		} catch ( Exception $e ) {
			error_log( $e );
			$response = array(
				'status' => 'ERROR'
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $response );
		exit;

	}

	private function find_preferred_plan( $customer_id, $plans ) {
		foreach ( $plans as $plan ) {
			if ( $plan['name'] === $customer_id ) {
				return $plan['value'];
			}
		}

		return null;
	}

}