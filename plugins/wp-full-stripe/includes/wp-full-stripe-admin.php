<?php

/**
 * Class MM_WPFS_Admin deals with admin back-end input i.e. create plans, transfers
 */
class MM_WPFS_Admin {

	/** @var boolean */
	private $debugLog = false;

	/**@var MM_WPFS_LoggerService */
	private $loggerService = null;

	/** @var MM_WPFS_Stripe */
	private $stripe = null;

	/** @var MM_WPFS_Database */
	private $db = null;

	/** @var MM_WPFS_Mailer */
	private $mailer = null;

	/** @var $eventHandler MM_WPFS_EventHandler */
	private $eventHandler = null;

	public function __construct() {
		$this->loggerService = new MM_WPFS_LoggerService();
		$this->stripe        = new MM_WPFS_Stripe();
		$this->db            = new MM_WPFS_Database();
		$this->mailer        = new MM_WPFS_Mailer();
		$this->eventHandler  = new MM_WPFS_EventHandler(
			$this->db,
			$this->stripe,
			$this->mailer,
			$this->loggerService
		);
		$this->hooks();
	}

	private function hooks() {

		// tnagy actions for settings
		add_action( 'wp_ajax_wp_full_stripe_update_settings', array( $this, 'fullstripe_update_settings_post' ) );

		// tnagy actions for subscription plans
		add_action( 'wp_ajax_wp_full_stripe_create_plan', array( $this, 'fullstripe_create_plan_post' ) );
		add_action( 'wp_ajax_wp_full_stripe_edit_subscription_plan', array(
			$this,
			'fullstripe_edit_subscription_plan_post'
		) );
		add_action( 'wp_ajax_wp_full_stripe_delete_subscription_plan', array(
			$this,
			'fullstripe_delete_subscription_plan'
		) );

		// tnagy actions for subscription forms
		add_action( 'wp_ajax_wp_full_stripe_create_subscripton_form', array(
			$this,
			'fullstripe_create_subscription_form_post'
		) );
		add_action( 'wp_ajax_wp_full_stripe_edit_subscription_form', array(
			$this,
			'fullstripe_edit_subscription_form_post'
		) );
		add_action( 'wp_ajax_wp_full_stripe_delete_subscription_form', array(
			$this,
			'fullstripe_delete_subscription_form'
		) );

		// tnagy actions for checkout subscriptions
		add_action( 'wp_ajax_wp_full_stripe_create_checkout_subscription_form', array(
			$this,
			'fullstripe_create_checkout_subscription_form_post'
		) );
		add_action( 'wp_ajax_wp_full_stripe_edit_checkout_subscription_form', array(
			$this,
			'fullstripe_edit_checkout_subscription_form_post'
		) );
		add_action( 'wp_ajax_wp_full_stripe_delete_checkout_subscription_form', array(
			$this,
			'fullstripe_delete_checkout_subscription_form'
		) );

		// tnagy actions for subscriptions
		add_action( 'wp_ajax_wp_full_stripe_cancel_subscription', array( $this, 'fullstripe_cancel_subscription' ) );
		add_action( 'wp_ajax_wp_full_stripe_delete_subscription_record', array(
			$this,
			'fullstripe_delete_subscription_record'
		) );

		// tnagy actions for payment forms
		add_action( 'wp_ajax_wp_full_stripe_create_payment_form', array(
			$this,
			'fullstripe_create_payment_form_post'
		) );
		add_action( 'wp_ajax_wp_full_stripe_edit_payment_form', array( $this, 'fullstripe_edit_payment_form_post' ) );
		add_action( 'wp_ajax_wp_full_stripe_delete_payment_form', array( $this, 'fullstripe_delete_payment_form' ) );

		// tnagy actions for checkouts
		add_action( 'wp_ajax_wp_full_stripe_create_checkout_form', array(
			$this,
			'fullstripe_create_checkout_form_post'
		) );
		add_action( 'wp_ajax_wp_full_stripe_edit_checkout_form', array( $this, 'fullstripe_edit_checkout_form_post' ) );
		add_action( 'wp_ajax_wp_full_stripe_delete_checkout_form', array( $this, 'fullstripe_delete_checkout_form' ) );

		// tnagy actions for payments
		add_action( 'wp_ajax_wp_full_stripe_delete_payment', array( $this, 'fullstripe_delete_payment_local' ) );
		add_action( 'wp_ajax_wp_full_stripe_capture_payment', array( $this, 'fullstripe_capture_payment' ) );
		add_action( 'wp_ajax_wp_full_stripe_refund_payment', array( $this, 'fullstripe_refund_payment' ) );

		// actions for donations
        add_action( 'wp_ajax_wp_full_stripe_refund_donation', array( $this, 'fullstripe_refund_donation' ) );
        add_action( 'wp_ajax_wp_full_stripe_cancel_donation', array( $this, 'fullstripe_cancel_donation' ) );
        add_action( 'wp_ajax_wp_full_stripe_delete_donation', array( $this, 'fullstripe_delete_donation_local' ) );


        // tnagy actions for saved cards
		add_action( 'wp_ajax_wp_full_stripe_create_inline_card_capture_form', array(
			$this,
			'fullstripe_create_inline_card_capture_form_post'
		) );
		add_action( 'wp_ajax_wp_full_stripe_create_popup_card_capture_form', array(
			$this,
			'fullstripe_create_popup_card_capture_form_post'
		) );
		add_action( 'wp_ajax_wp_full_stripe_edit_inline_card_capture_form', array(
			$this,
			'fullstripe_edit_inline_card_capture_form_post'
		) );
		add_action( 'wp_ajax_wp_full_stripe_edit_popup_card_capture_form', array(
			$this,
			'fullstripe_edit_popup_card_capture_form_post'
		) );
		add_action( 'wp_ajax_wp_full_stripe_delete_inline_card_capture_form', array(
			$this,
			'fullstripe_delete_inline_card_capture_form'
		) );
		add_action( 'wp_ajax_wp_full_stripe_delete_popup_card_capture_form', array(
			$this,
			'fullstripe_delete_popup_card_capture_form'
		) );
		add_action( 'wp_ajax_wp_full_stripe_delete_card_capture', array(
			$this,
			'fullstripe_delete_card_capture_local'
		) );

		// actions for donations
        add_action( 'wp_ajax_wp_full_stripe_create_inline_donation_form', array(
            $this,
            'fullstripe_create_inline_donation_form_post'
        ) );
        add_action( 'wp_ajax_wp_full_stripe_edit_inline_donation_form', array(
            $this,
            'fullstripe_edit_inline_donation_form_post'
        ) );
        add_action( 'wp_ajax_wp_full_stripe_create_checkout_donation_form', array(
            $this,
            'fullstripe_create_checkout_donation_form_post'
        ) );
        add_action( 'wp_ajax_wp_full_stripe_edit_checkout_donation_form', array(
            $this,
            'fullstripe_edit_checkout_donation_form_post'
        ) );
        add_action( 'wp_ajax_wp_full_stripe_delete_inline_donation_form', array(
            $this,
            'fullstripe_delete_inline_donation_form'
        ) );
        add_action( 'wp_ajax_wp_full_stripe_delete_checkout_donation_form', array(
            $this,
            'fullstripe_delete_checkout_donation_form'
        ) );


        // tnagy actions for recipients
		add_action( 'wp_ajax_wp_full_stripe_create_recipient', array( $this, 'fullstripe_create_recipient' ) );
		add_action( 'wp_ajax_wp_full_stripe_create_recipient_card', array(
			$this,
			'fullstripe_create_recipient_card'
		) );

		// tnagy actions for transfers
		add_action( 'wp_ajax_wp_full_stripe_create_transfer', array( $this, 'fullstripe_create_transfer' ) );

		// tnagy handle stripe webhook events
		add_action( 'admin_post_nopriv_handle_wpfs_event', array(
			$this,
			'fullstripe_handle_wpfs_event'
		) );
	}

	public static function getPaymentStatusLabel( $payment_status ) {

		if ( MM_WPFS::PAYMENT_STATUS_AUTHORIZED === $payment_status ) {
			$label = __( 'Authorized', MM_WPFS::L10N_DOMAIN_ADMIN );
		} elseif ( MM_WPFS::PAYMENT_STATUS_PAID === $payment_status ) {
			$label = __( 'Paid', MM_WPFS::L10N_DOMAIN_ADMIN );
		} elseif ( MM_WPFS::PAYMENT_STATUS_EXPIRED === $payment_status ) {
			$label = __( 'Expired', MM_WPFS::L10N_DOMAIN_ADMIN );
		} elseif ( MM_WPFS::PAYMENT_STATUS_RELEASED === $payment_status ) {
			$label = __( 'Released', MM_WPFS::L10N_DOMAIN_ADMIN );
		} elseif ( MM_WPFS::PAYMENT_STATUS_REFUNDED === $payment_status ) {
			$label = __( 'Refunded', MM_WPFS::L10N_DOMAIN_ADMIN );
		} elseif ( MM_WPFS::PAYMENT_STATUS_FAILED === $payment_status ) {
			$label = __( 'Failed', MM_WPFS::L10N_DOMAIN_ADMIN );
		} elseif ( MM_WPFS::PAYMENT_STATUS_PENDING === $payment_status ) {
			$label = __( 'Pending', MM_WPFS::L10N_DOMAIN_ADMIN );
		} else {
			$label = __( 'Unknown', MM_WPFS::L10N_DOMAIN_ADMIN );
		}

		return $label;
	}

	public static function formatIntervalLabelAdmin( $interval, $intervalCount ) {
		$intervalLabel = __( 'No interval', MM_WPFS::L10N_DOMAIN_ADMIN );

		if ( $interval === "year" ) {
			$intervalLabel = sprintf( _n( 'year', '%d years', $intervalCount, MM_WPFS::L10N_DOMAIN_ADMIN ), number_format_i18n( $intervalCount ) );
		} elseif ( $interval === "month" ) {
			$intervalLabel = sprintf( _n( 'month', '%d months', $intervalCount, MM_WPFS::L10N_DOMAIN_ADMIN ), number_format_i18n( $intervalCount ) );
		} elseif ( $interval === "week" ) {
			$intervalLabel = sprintf( _n( 'week', '%d weeks', $intervalCount, MM_WPFS::L10N_DOMAIN_ADMIN ), number_format_i18n( $intervalCount ) );
		} elseif ( $interval === "day" ) {
			$intervalLabel = sprintf( _n( 'day', '%d days', $intervalCount, MM_WPFS::L10N_DOMAIN_ADMIN ), number_format_i18n( $intervalCount ) );
		}

		return $intervalLabel;
	}

	public static function translateLabelAdmin( $label ) {
		return MM_WPFS_Localization::translateLabel( $label, MM_WPFS::L10N_DOMAIN_ADMIN );
	}

	public static function get_vat_rate_type_values() {
		return array(
			MM_WPFS::VAT_RATE_TYPE_NO_VAT     => __( 'No VAT', MM_WPFS::L10N_DOMAIN_ADMIN ),
			MM_WPFS::VAT_RATE_TYPE_FIXED_VAT  => __( 'Fixed rate', MM_WPFS::L10N_DOMAIN_ADMIN ),
			MM_WPFS::VAT_RATE_TYPE_CUSTOM_VAT => __( 'Custom rate', MM_WPFS::L10N_DOMAIN_ADMIN )
		);
	}

	/**
	 * Create a list of email receipt template objects to render on the Settings page.
	 * @return mixed|void
	 */
	public function get_email_receipt_templates() {

		$emailReceiptTemplates = array();

		$paymentMade                              = new stdClass();
		$paymentMade->id                          = 'paymentMade';
		$paymentMade->caption                     = __( 'Payment receipt', MM_WPFS::L10N_DOMAIN_ADMIN );
		$cardCaptured                             = new stdClass();
		$cardCaptured->id                         = 'cardCaptured';
		$cardCaptured->caption                    = __( 'Card saved', MM_WPFS::L10N_DOMAIN_ADMIN );
		$subscriptionStarted                      = new stdClass();
		$subscriptionStarted->id                  = 'subscriptionStarted';
		$subscriptionStarted->caption             = __( 'Subscription receipt', MM_WPFS::L10N_DOMAIN_ADMIN );
		$subscriptionFinished                     = new stdClass();
		$subscriptionFinished->id                 = 'subscriptionFinished';
		$subscriptionFinished->caption            = __( 'Subscription ended', MM_WPFS::L10N_DOMAIN_ADMIN );
        $donationMade                              = new stdClass();
        $donationMade->id                          = 'donationMade';
        $donationMade->caption                     = __( 'Donation receipt', MM_WPFS::L10N_DOMAIN_ADMIN );
		$cardUpdateConfirmationRequested          = new stdClass();
		$cardUpdateConfirmationRequested->id      = 'cardUpdateConfirmationRequest';
		$cardUpdateConfirmationRequested->caption = __( 'Subscription update security code', MM_WPFS::L10N_DOMAIN_ADMIN );

		array_push( $emailReceiptTemplates, $paymentMade );
		array_push( $emailReceiptTemplates, $cardCaptured );
		array_push( $emailReceiptTemplates, $subscriptionStarted );
		array_push( $emailReceiptTemplates, $subscriptionFinished );
        array_push( $emailReceiptTemplates, $donationMade );
		array_push( $emailReceiptTemplates, $cardUpdateConfirmationRequested );

		return apply_filters( 'fullstripe_settings_email_receipt_templates', $emailReceiptTemplates );
	}

	function fullstripe_create_plan_post() {
		$validation_result = array();
		if ( ! $this->is_valid_plan( $validation_result ) ) {
			header( "Content-Type: application/json" );
			echo json_encode( array( 'success' => false, 'validation_result' => $validation_result ) );
			exit;
		}

		$id                = stripslashes( $_POST['sub_id'] );
		$name              = $_POST['sub_name'];
		$currency          = $_POST['sub_currency'];
		$amount            = $_POST['sub_amount'];
		$setup_fee         = $_POST['sub_setup_fee'];
		$interval          = $_POST['sub_interval'];
		$intervalCount     = $_POST['sub_interval_count'];
		$cancellationCount = $_POST['sub_cancellation_count'];
		$trial             = $_POST['sub_trial'];

		$result = [ ];
		try {
			$this->stripe->create_plan( $id, $name, $currency, $amount, $setup_fee, $interval, $trial, $intervalCount, $cancellationCount );
			$result = array(
				'success' => true,
				'msg'     => __( 'Subscription plan created ', MM_WPFS::L10N_DOMAIN_ADMIN )
			);
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );

			$result = array(
				'success' => false,
				'msg'     => __( 'There was an error creating the plan: ', MM_WPFS::L10N_DOMAIN_ADMIN ) . $e->getMessage()
			);
		}
		$result['redirectURL'] = admin_url( 'admin.php?page=fullstripe-subscriptions&tab=plans' );

		do_action( 'fullstripe_admin_create_plan_action', $result );

		header( "Content-Type: application/json" );
		echo json_encode( $result );
		exit;
	}

	private function is_valid_plan( &$validation_result ) {
		if ( isset( $_POST['sub_cancellation_count'] ) ) {
			if ( ! is_numeric( $_POST['sub_cancellation_count'] ) ) {
				$validation_result['sub_cancellation_count'] = __( 'Field value is invalid.', MM_WPFS::L10N_DOMAIN_ADMIN );
			} else if ( intval( $_POST['sub_cancellation_count'] ) < 0 ) {
				$validation_result['sub_cancellation_count'] = __( 'Field value is invalid.', MM_WPFS::L10N_DOMAIN_ADMIN );
			}
		} else {
			$validation_result['sub_cancellation_count'] = __( 'Required field.', MM_WPFS::L10N_DOMAIN_ADMIN );
		}

		return empty( $validation_result );
	}

	function fullstripe_create_subscription_form_post() {

		$redirectURL = add_query_arg(
			array(
				'page' => 'fullstripe-subscriptions',
				'tab'  => 'forms'
			),
			admin_url( 'admin.php' )
		);

		try {

			$subscriptionFormModel = new MM_WPFS_Admin_InlineSubscriptionFormModel();
			$subscriptionFormModel->bind();
			$data = $subscriptionFormModel->getData();

			$this->db->insert_subscription_form( $data );

			$return = array(
				'success'     => true,
				'redirectURL' => $redirectURL
			);

		} catch ( Exception $e ) {
			error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
			$return = array(
				'success'     => false,
				'redirectURL' => $redirectURL,
				'ex_msg'      => $e->getMessage(),
				'ex_stack'    => $e->getTraceAsString()
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function fullstripe_edit_subscription_form_post() {
		$redirectURL = add_query_arg(
			array(
				'page' => 'fullstripe-subscriptions',
				'tab'  => 'forms'
			),
			admin_url( 'admin.php' )
		);

		if ( ! MM_WPFS_Utils::isDemoMode() ) {
			try {

				$inlineSubscriptionFormModel = new MM_WPFS_Admin_InlineSubscriptionFormModel();
				$inlineSubscriptionFormModel->bind();
				$data = $inlineSubscriptionFormModel->getData();

				$this->db->update_subscription_form( $inlineSubscriptionFormModel->getId(), $data );

				$return = array(
					'success'     => true,
					'redirectURL' => $redirectURL
				);

			} catch ( Exception $e ) {
				error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
				$return = array(
					'success'     => false,
					'redirectURL' => $redirectURL,
					'ex_msg'      => $e->getMessage(),
					'ex_stack'    => $e->getTraceAsString()
				);
			}
		} else {
			$return = array(
				'success'     => true,
				'redirectURL' => $redirectURL
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function fullstripe_create_payment_form_post() {

		$redirectURL = add_query_arg(
			array(
				'page' => 'fullstripe-payments',
				'tab'  => 'forms'
			),
			admin_url( 'admin.php' )
		);

		try {

			$inlinePaymentFormModel = new MM_WPFS_Admin_InlinePaymentFormModel();
			$inlinePaymentFormModel->bind();
			$data = $inlinePaymentFormModel->getData();

			$this->db->insert_payment_form( $data );

			$return = array(
				'success'     => true,
				'redirectURL' => $redirectURL
			);

		} catch ( Exception $e ) {
			error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
			$return = array(
				'success'     => false,
				'redirectURL' => $redirectURL,
				'ex_msg'      => $e->getMessage(),
				'ex_stack'    => $e->getTraceAsString()
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function fullstripe_create_inline_card_capture_form_post() {

		$redirectURL = add_query_arg(
			array(
				'page' => 'fullstripe-saved-cards',
				'tab'  => 'forms'
			),
			admin_url( 'admin.php' )
		);

		try {

			$inlinePaymentFormModel = new MM_WPFS_Admin_InlinePaymentFormModel();
			$inlinePaymentFormModel->bind();
			$inlinePaymentFormModel->convertToCardCaptureForm();
			$data = $inlinePaymentFormModel->getData();

			$this->db->insert_payment_form( $data );

			$return = array(
				'success'     => true,
				'redirectURL' => $redirectURL
			);

		} catch ( Exception $e ) {
			error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
			$return = array(
				'success'     => false,
				'redirectURL' => $redirectURL,
				'ex_msg'      => $e->getMessage(),
				'ex_stack'    => $e->getTraceAsString()
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function fullstripe_edit_payment_form_post() {

		$redirectURL = add_query_arg(
			array(
				'page' => 'fullstripe-payments',
				'tab'  => 'forms'
			),
			admin_url( 'admin.php' )
		);

		if ( ! MM_WPFS_Utils::isDemoMode() ) {
			try {

				$inlinePaymentFormModel = new MM_WPFS_Admin_InlinePaymentFormModel();
				$inlinePaymentFormModel->bind();
				$data = $inlinePaymentFormModel->getData();

				$this->db->update_payment_form( $inlinePaymentFormModel->getId(), $data );

				$return = array(
					'success'     => true,
					'redirectURL' => $redirectURL
				);

			} catch ( Exception $e ) {
				error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
				$return = array(
					'success'     => false,
					'redirectURL' => $redirectURL,
					'ex_msg'      => $e->getMessage(),
					'ex_stack'    => $e->getTraceAsString()
				);
			}
		} else {
			$return = array(
				'success'     => true,
				'redirectURL' => $redirectURL
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function fullstripe_edit_inline_card_capture_form_post() {
		$redirectURL = add_query_arg(
			array(
				'page' => 'fullstripe-saved-cards',
				'tab'  => 'forms'
			),
			admin_url( 'admin.php' )
		);

		if ( ! MM_WPFS_Utils::isDemoMode() ) {
			try {

				$inlinePaymentFormModel = new MM_WPFS_Admin_InlinePaymentFormModel();
				$inlinePaymentFormModel->bind();
				$inlinePaymentFormModel->convertToCardCaptureForm();
				$data = $inlinePaymentFormModel->getData();

				$this->db->update_payment_form( $inlinePaymentFormModel->getId(), $data );

				$return = array(
					'success'     => true,
					'redirectURL' => $redirectURL
				);

			} catch ( Exception $e ) {
				error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
				$return = array(
					'success'     => false,
					'redirectURL' => $redirectURL,
					'ex_msg'      => $e->getMessage(),
					'ex_stack'    => $e->getTraceAsString()
				);
			}
		} else {
			$return = array(
				'success'     => true,
				'redirectURL' => $redirectURL
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function fullstripe_create_popup_card_capture_form_post() {

		$redirectURL = add_query_arg(
			array(
				'page' => 'fullstripe-saved-cards',
				'tab'  => 'forms'
			),
			admin_url( 'admin.php' )
		);

		try {

			$popupPaymentFormModel = new MM_WPFS_Admin_PopupPaymentFormModel();
			$popupPaymentFormModel->bind();
			$popupPaymentFormModel->convertToCardCaptureForm();
			$data = $popupPaymentFormModel->getData();

			$this->db->insert_checkout_form( $data );

			$return = array(
				'success'     => true,
				'redirectURL' => $redirectURL
			);

		} catch ( Exception $e ) {
			error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
			$return = array(
				'success'     => false,
				'redirectURL' => $redirectURL,
				'ex_msg'      => $e->getMessage(),
				'ex_stack'    => $e->getTraceAsString()
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function fullstripe_create_checkout_form_post() {

		$redirectURL = add_query_arg(
			array(
				'page' => 'fullstripe-payments',
				'tab'  => 'forms'
			),
			admin_url( 'admin.php' )
		);

		try {

			$popupPaymentFormModel = new MM_WPFS_Admin_PopupPaymentFormModel();
			$popupPaymentFormModel->bind();
			$data = $popupPaymentFormModel->getData();

			$this->db->insert_checkout_form( $data );

			$return = array(
				'success'     => true,
				'redirectURL' => $redirectURL
			);

		} catch ( Exception $e ) {
			error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
			$return = array(
				'success'     => false,
				'redirectURL' => $redirectURL,
				'ex_msg'      => $e->getMessage(),
				'ex_stack'    => $e->getTraceAsString()
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function fullstripe_edit_checkout_form_post() {
		$redirectURL = add_query_arg(
			array(
				'page' => 'fullstripe-payments',
				'tab'  => 'forms'
			),
			admin_url( 'admin.php' )
		);

		if ( ! MM_WPFS_Utils::isDemoMode() ) {
			try {

				$popupPaymentFormModel = new MM_WPFS_Admin_PopupPaymentFormModel();
				$popupPaymentFormModel->bind();
				$data = $popupPaymentFormModel->getData();

				$this->db->update_checkout_form( $popupPaymentFormModel->getId(), $data );

				$return = array(
					'success'     => true,
					'redirectURL' => $redirectURL
				);

			} catch ( Exception $e ) {
				error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
				$return = array(
					'success'     => false,
					'redirectURL' => $redirectURL,
					'ex_msg'      => $e->getMessage(),
					'ex_stack'    => $e->getTraceAsString()
				);
			}
		} else {
			$return = array(
				'success'     => true,
				'redirectURL' => $redirectURL
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function fullstripe_edit_popup_card_capture_form_post() {

		$redirectURL = add_query_arg(
			array(
				'page' => 'fullstripe-saved-cards',
				'tab'  => 'forms'
			),
			admin_url( 'admin.php' )
		);

		if ( ! MM_WPFS_Utils::isDemoMode() ) {
			try {
				$popupPaymentFormModel = new MM_WPFS_Admin_PopupPaymentFormModel();
				$popupPaymentFormModel->bind();
				$popupPaymentFormModel->convertToCardCaptureForm();
				$data = $popupPaymentFormModel->getData();

				$this->db->update_checkout_form( $popupPaymentFormModel->getId(), $data );

				$return = array(
					'success'     => true,
					'redirectURL' => $redirectURL
				);

			} catch ( Exception $e ) {
				error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
				$return = array(
					'success'     => false,
					'redirectURL' => $redirectURL,
					'ex_msg'      => $e->getMessage(),
					'ex_stack'    => $e->getTraceAsString()
				);
			}
		} else {
			$return = array(
				'success'     => true,
				'redirectURL' => $redirectURL
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function fullstripe_create_checkout_subscription_form_post() {

		$redirectURL = add_query_arg(
			array(
				'page' => 'fullstripe-subscriptions',
				'tab'  => 'forms'
			),
			admin_url( 'admin.php' )
		);

		try {

			$popupSubscriptionFormModel = new MM_WPFS_Admin_PopupSubscriptionFormModel();
			$popupSubscriptionFormModel->bind();
			$data = $popupSubscriptionFormModel->getData();

			$this->db->insert_checkout_subscription_form( $data );

			$return = array(
				'success'     => true,
				'redirectURL' => $redirectURL
			);

		} catch ( Exception $e ) {
			error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
			$return = array(
				'success'     => false,
				'redirectURL' => $redirectURL,
				'ex_msg'      => $e->getMessage(),
				'ex_stack'    => $e->getTraceAsString()
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function fullstripe_edit_checkout_subscription_form_post() {
		$redirectURL = add_query_arg(
			array(
				'page' => 'fullstripe-subscriptions',
				'tab'  => 'forms'
			),
			admin_url( 'admin.php' )
		);

		if ( ! MM_WPFS_Utils::isDemoMode() ) {
			try {

				$popupSubscriptionFormModel = new MM_WPFS_Admin_PopupSubscriptionFormModel();
				$popupSubscriptionFormModel->bind();
				$data = $popupSubscriptionFormModel->getData();

				$this->db->update_checkout_subscription_form( $popupSubscriptionFormModel->getId(), $data );

				$return = array(
					'success'     => true,
					'redirectURL' => $redirectURL
				);

			} catch ( Exception $e ) {
				error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
				$return = array(
					'success'     => false,
					'redirectURL' => $redirectURL,
					'ex_msg'      => $e->getMessage(),
					'ex_stack'    => $e->getTraceAsString()
				);
			}
		} else {
			$return = array(
				'success'     => true,
				'redirectURL' => $redirectURL
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}


    function fullstripe_create_inline_donation_form_post() {

        $redirectURL = add_query_arg(
            array(
                'page' => 'fullstripe-donations',
                'tab'  => 'forms'
            ),
            admin_url( 'admin.php' )
        );

        if ( ! MM_WPFS_Utils::isDemoMode() ) {
            try {

                $inlineDonationFormModel = new MM_WPFS_Admin_InlineDonationFormModel();
                $inlineDonationFormModel->bind();
                $data = $inlineDonationFormModel->getData();

                $this->db->insertDonationForm( $data );

                $return = array(
                    'success'     => true,
                    'redirectURL' => $redirectURL
                );

            } catch ( Exception $e ) {
                error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
                $return = array(
                    'success'     => false,
                    'redirectURL' => $redirectURL,
                    'ex_msg'      => $e->getMessage(),
                    'ex_stack'    => $e->getTraceAsString()
                );
            }
        } else {
            $return = array(
                'success'     => true,
                'redirectURL' => $redirectURL
            );
        }

        header( "Content-Type: application/json" );
        echo json_encode( $return );
        exit;
    }

    function fullstripe_create_checkout_donation_form_post() {

        $redirectURL = add_query_arg(
            array(
                'page' => 'fullstripe-donations',
                'tab'  => 'forms'
            ),
            admin_url( 'admin.php' )
        );

        if ( ! MM_WPFS_Utils::isDemoMode() ) {
            try {

                $checkoutDonationFormModel = new MM_WPFS_Admin_PopupDonationFormModel();
                $checkoutDonationFormModel->bind();
                $data = $checkoutDonationFormModel->getData();

                $this->db->insertCheckoutDonationForm( $data );

                $return = array(
                    'success'     => true,
                    'redirectURL' => $redirectURL
                );

            } catch ( Exception $e ) {
                error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
                $return = array(
                    'success'     => false,
                    'redirectURL' => $redirectURL,
                    'ex_msg'      => $e->getMessage(),
                    'ex_stack'    => $e->getTraceAsString()
                );
            }
        } else {
            $return = array(
                'success'     => true,
                'redirectURL' => $redirectURL
            );
        }

        header( "Content-Type: application/json" );
        echo json_encode( $return );
        exit;
    }

    function fullstripe_edit_inline_donation_form_post() {

        $redirectURL = add_query_arg(
            array(
                'page' => 'fullstripe-donations',
                'tab'  => 'forms'
            ),
            admin_url( 'admin.php' )
        );

        if ( ! MM_WPFS_Utils::isDemoMode() ) {
            try {

                $inlineDonationFormModel = new MM_WPFS_Admin_InlineDonationFormModel();
                $inlineDonationFormModel->bind();
                $data = $inlineDonationFormModel->getData();

                $this->db->updateDonationForm( $inlineDonationFormModel->getId(), $data );

                $return = array(
                    'success'     => true,
                    'redirectURL' => $redirectURL
                );

            } catch ( Exception $e ) {
                error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
                $return = array(
                    'success'     => false,
                    'redirectURL' => $redirectURL,
                    'ex_msg'      => $e->getMessage(),
                    'ex_stack'    => $e->getTraceAsString()
                );
            }
        } else {
            $return = array(
                'success'     => true,
                'redirectURL' => $redirectURL
            );
        }

        header( "Content-Type: application/json" );
        echo json_encode( $return );
        exit;
    }

    function fullstripe_edit_checkout_donation_form_post() {

        $redirectURL = add_query_arg(
            array(
                'page' => 'fullstripe-donations',
                'tab'  => 'forms'
            ),
            admin_url( 'admin.php' )
        );

        if ( ! MM_WPFS_Utils::isDemoMode() ) {
            try {

                $checkoutDonationFormModel = new MM_WPFS_Admin_PopupDonationFormModel();
                $checkoutDonationFormModel->bind();
                $data = $checkoutDonationFormModel->getData();

                $this->db->updateCheckoutDonationForm( $checkoutDonationFormModel->getId(), $data );

                $return = array(
                    'success'     => true,
                    'redirectURL' => $redirectURL
                );

            } catch ( Exception $e ) {
                error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
                $return = array(
                    'success'     => false,
                    'redirectURL' => $redirectURL,
                    'ex_msg'      => $e->getMessage(),
                    'ex_stack'    => $e->getTraceAsString()
                );
            }
        } else {
            $return = array(
                'success'     => true,
                'redirectURL' => $redirectURL
            );
        }

        header( "Content-Type: application/json" );
        echo json_encode( $return );
        exit;
    }


    function fullstripe_update_settings_post() {
		if ( MM_WPFS_Utils::isDemoMode() ) {
			header( "Content-Type: application/json" );
			echo json_encode( array(
				'success'     => true,
				'redirectURL' => admin_url( 'admin.php?page=fullstripe-settings' )
			) );
			exit;
		}

		$validation_result = array();
		if ( ! $this->is_valid_options( $validation_result ) ) {
			header( "Content-Type: application/json" );
			echo json_encode( array( 'success' => false, 'validation_result' => $validation_result ) );
			exit;
		}

		// Save the posted value in the database
		$options = get_option( 'fullstripe_options' );

		$tab = null;
		if ( isset( $_POST['tab'] ) ) {
			$tab = sanitize_text_field( $_POST['tab'] );
		}
		if ( isset( $_POST['publishKey_test'] ) ) {
			$options['publishKey_test'] = sanitize_text_field( $_POST['publishKey_test'] );
		}
		if ( isset( $_POST['secretKey_test'] ) ) {
			$options['secretKey_test'] = sanitize_text_field( $_POST['secretKey_test'] );
		}
		if ( isset( $_POST['publishKey_live'] ) ) {
			$options['publishKey_live'] = sanitize_text_field( $_POST['publishKey_live'] );
		}
		if ( isset( $_POST['secretKey_live'] ) ) {
			$options['secretKey_live'] = sanitize_text_field( $_POST['secretKey_live'] );
		}
		if ( isset( $_POST['apiMode'] ) ) {
			$options['apiMode'] = sanitize_text_field( $_POST['apiMode'] );
		}
		if ( isset( $_POST['form_css'] ) ) {
			$options['form_css'] = stripslashes( $_POST['form_css'] );
		}
		if ( isset( $_POST['includeStyles'] ) ) {
			$options['includeStyles'] = sanitize_text_field( $_POST['includeStyles'] );
		}
		if ( isset( $_POST['receiptEmailType'] ) ) {
			$options['receiptEmailType'] = sanitize_text_field( $_POST['receiptEmailType'] );
		}
		if ( isset( $_POST['email_receipts'] ) ) {
			$options['email_receipts'] = json_encode( json_decode( rawurldecode( stripslashes( $_POST['email_receipts'] ) ) ) );
		}
		if ( isset( $_POST['email_receipt_sender_address'] ) ) {
			$options['email_receipt_sender_address'] = sanitize_email( $_POST['email_receipt_sender_address'] );
		}
		if ( isset( $_POST['admin_payment_receipt'] ) ) {
			$options['admin_payment_receipt'] = sanitize_text_field( $_POST['admin_payment_receipt'] );
		}
		if ( isset( $_POST['lock_email_field_for_logged_in_users'] ) ) {
			$options['lock_email_field_for_logged_in_users'] = sanitize_text_field( $_POST['lock_email_field_for_logged_in_users'] );
		}
		$secureInlineFormsWithGoogleReCAPTCHA = '0';
		if ( isset( $_POST[ MM_WPFS::OPTION_SECURE_INLINE_FORMS_WITH_GOOGLE_RE_CAPTCHA ] ) ) {
			$secureInlineFormsWithGoogleReCAPTCHA                                  = sanitize_text_field( $_POST[ MM_WPFS::OPTION_SECURE_INLINE_FORMS_WITH_GOOGLE_RE_CAPTCHA ] );
			$options[ MM_WPFS::OPTION_SECURE_INLINE_FORMS_WITH_GOOGLE_RE_CAPTCHA ] = $secureInlineFormsWithGoogleReCAPTCHA;
		}
		$secureCheckoutFormsWithGoogleReCAPTCHA = '0';
		if ( isset( $_POST[ MM_WPFS::OPTION_SECURE_CHECKOUT_FORMS_WITH_GOOGLE_RE_CAPTCHA ] ) ) {
			$secureCheckoutFormsWithGoogleReCAPTCHA                                  = sanitize_text_field( $_POST[ MM_WPFS::OPTION_SECURE_CHECKOUT_FORMS_WITH_GOOGLE_RE_CAPTCHA ] );
			$options[ MM_WPFS::OPTION_SECURE_CHECKOUT_FORMS_WITH_GOOGLE_RE_CAPTCHA ] = $secureCheckoutFormsWithGoogleReCAPTCHA;
		}
		$secureSubscriptionUpdateWithGoogleReCAPTCHA = '0';
		if ( isset( $_POST[ MM_WPFS::OPTION_SECURE_SUBSCRIPTION_UPDATE_WITH_GOOGLE_RE_CAPTCHA ] ) ) {
			$secureSubscriptionUpdateWithGoogleReCAPTCHA                                  = sanitize_text_field( $_POST[ MM_WPFS::OPTION_SECURE_SUBSCRIPTION_UPDATE_WITH_GOOGLE_RE_CAPTCHA ] );
			$options[ MM_WPFS::OPTION_SECURE_SUBSCRIPTION_UPDATE_WITH_GOOGLE_RE_CAPTCHA ] = $secureSubscriptionUpdateWithGoogleReCAPTCHA;
		}
		if (
			'1' == $secureInlineFormsWithGoogleReCAPTCHA
			|| '1' == $secureCheckoutFormsWithGoogleReCAPTCHA
			|| '1' == $secureSubscriptionUpdateWithGoogleReCAPTCHA
		) {
			if ( isset( $_POST[ MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SITE_KEY ] ) ) {
				$options[ MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SITE_KEY ] = sanitize_text_field( $_POST[ MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SITE_KEY ] );
			}
			if ( isset( $_POST[ MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SECRET_KEY ] ) ) {
				$options[ MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SECRET_KEY ] = sanitize_text_field( $_POST[ MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SECRET_KEY ] );
			}
		}
		$showInvoicesSection = '0';
		if ( isset( $_POST[ MM_WPFS::OPTION_MY_ACCOUNT_SHOW_INVOICES_SECTION ] ) ) {
			$showInvoicesSection                                         = sanitize_text_field( $_POST[ MM_WPFS::OPTION_MY_ACCOUNT_SHOW_INVOICES_SECTION ] );
			$options[ MM_WPFS::OPTION_MY_ACCOUNT_SHOW_INVOICES_SECTION ] = $showInvoicesSection;
		}
		$subscribersCanCancelSubscriptions = '1';
		if ( isset( $_POST[ MM_WPFS::OPTION_MY_ACCOUNT_LET_SUBSCRIBERS_CANCEL_SUBSCRIPTIONS ] ) ) {
			$subscribersCanCancelSubscriptions                                          = sanitize_text_field( $_POST[ MM_WPFS::OPTION_MY_ACCOUNT_LET_SUBSCRIBERS_CANCEL_SUBSCRIPTIONS ] );
			$options[ MM_WPFS::OPTION_MY_ACCOUNT_LET_SUBSCRIBERS_CANCEL_SUBSCRIPTIONS ] = $subscribersCanCancelSubscriptions;
		}
		// tnagy currency format options
		if ( isset( $_POST[ MM_WPFS::OPTION_DECIMAL_SEPARATOR_SYMBOL ] ) ) {
			$decimalSeparatorSymbol                              = sanitize_text_field( $_POST[ MM_WPFS::OPTION_DECIMAL_SEPARATOR_SYMBOL ] );
			if (MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT == $decimalSeparatorSymbol) {
				$options[ MM_WPFS::OPTION_DECIMAL_SEPARATOR_SYMBOL ] = MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT;
			} elseif (MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_COMMA == $decimalSeparatorSymbol) {
				$options[ MM_WPFS::OPTION_DECIMAL_SEPARATOR_SYMBOL ] = MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_COMMA;
			} else {
				$options[ MM_WPFS::OPTION_DECIMAL_SEPARATOR_SYMBOL ] = MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT;
			}
		}
		if ( isset( $_POST[ MM_WPFS::OPTION_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE ] ) ) {
			$showCurrencySymbolInsteadOfCode                                 = sanitize_text_field( $_POST[ MM_WPFS::OPTION_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE ] );
			$options[ MM_WPFS::OPTION_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE ] = $showCurrencySymbolInsteadOfCode;
		}
		if ( isset( $_POST[ MM_WPFS::OPTION_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION ] ) ) {
			$showCurrencySignAtFirstPosition                                 = sanitize_text_field( $_POST[ MM_WPFS::OPTION_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION ] );
			$options[ MM_WPFS::OPTION_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION ] = $showCurrencySignAtFirstPosition;
		}
		if ( isset( $_POST[ MM_WPFS::OPTION_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT ] ) ) {
			$putWhitespaceBetweenCurrencyAndAmount                                 = sanitize_text_field( $_POST[ MM_WPFS::OPTION_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT ] );
			$options[ MM_WPFS::OPTION_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT ] = $putWhitespaceBetweenCurrencyAndAmount;
		}


		$activeTab = null;
		if ( $tab === 'stripe' ) {
			$activeTab = $tab;
		} else if ( $tab === 'appearance' ) {
			$activeTab = $tab;
		} else if ( $tab === 'email-receipts' ) {
			$activeTab = $tab;
		} else if ( $tab === 'security' ) {
			$activeTab = $tab;
		}
		update_option( 'fullstripe_options', $options );
		do_action( 'fullstripe_admin_update_options_action', $options );

		header( "Content-Type: application/json" );
		echo json_encode( array(
			'success'     => true,
			'redirectURL' => admin_url( 'admin.php?page=fullstripe-settings' . ( isset( $activeTab ) ? "&tab=$activeTab" : "" ) )
		) );
		exit;
	}

	private function is_valid_options( &$validation_result ) {
		if ( ! $this->is_not_set_or_empty( 'email_receipt_sender_address' ) ) {
			if ( ! filter_var( sanitize_email( $_POST['email_receipt_sender_address'] ), FILTER_VALIDATE_EMAIL ) ) {
				$validation_result['email_receipt_sender_address'] = __( 'Please enter a valid email address or leave the field empty', MM_WPFS::L10N_DOMAIN_ADMIN );
			}
		}

		return empty( $validation_result );
	}

	private function is_not_set_or_empty( $key ) {
		return ! isset( $_POST[ $key ] ) || empty( $_POST[ $key ] );
	}

	function fullstripe_delete_payment_form() {
		if ( ! MM_WPFS_Utils::isDemoMode() ) {
			$id = $_POST['id'];
			do_action( 'fullstripe_admin_delete_payment_form_action', $id );

			try {
				$this->db->delete_payment_form( $id );

				$return = array( 'success' => true );
			} catch ( Exception $e ) {
				error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
				$return = array(
					'success'  => false,
					'ex_msg'   => $e->getMessage(),
					'ex_stack' => $e->getTraceAsString()
				);

			}
		} else {
			$return = array( 'success' => true );
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function fullstripe_delete_inline_card_capture_form() {
		if ( ! MM_WPFS_Utils::isDemoMode() ) {
			$id = $_POST['id'];
			do_action( 'fullstripe_admin_delete_inline_card_capture_form_action', $id );

			try {
				$this->db->delete_payment_form( $id );

				$return = array( 'success' => true );
			} catch ( Exception $e ) {
				error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
				$return = array(
					'success'  => false,
					'ex_msg'   => $e->getMessage(),
					'ex_stack' => $e->getTraceAsString()
				);

			}
		} else {
			$return = array( 'success' => true );
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

    function fullstripe_delete_inline_donation_form() {
        if ( ! MM_WPFS_Utils::isDemoMode() ) {
            $id = $_POST['id'];
            do_action( 'fullstripe_admin_delete_inline_donation_form_action', $id );

            try {
                $this->db->deleteInlineDonationForm( $id );

                $return = array( 'success' => true );
            } catch ( Exception $e ) {
                error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
                $return = array(
                    'success'  => false,
                    'ex_msg'   => $e->getMessage(),
                    'ex_stack' => $e->getTraceAsString()
                );

            }
        } else {
            $return = array( 'success' => true );
        }

        header( "Content-Type: application/json" );
        echo json_encode( $return );
        exit;
    }

    function fullstripe_delete_checkout_donation_form() {
        if ( ! MM_WPFS_Utils::isDemoMode() ) {
            $id = $_POST['id'];
            do_action( 'fullstripe_admin_delete_checkout_donation_form_action', $id );

            try {
                $this->db->deleteCheckoutDonationForm( $id );

                $return = array( 'success' => true );
            } catch ( Exception $e ) {
                error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
                $return = array(
                    'success'  => false,
                    'ex_msg'   => $e->getMessage(),
                    'ex_stack' => $e->getTraceAsString()
                );

            }
        } else {
            $return = array( 'success' => true );
        }

        header( "Content-Type: application/json" );
        echo json_encode( $return );
        exit;
    }

    function fullstripe_delete_subscription_form() {
		if ( ! MM_WPFS_Utils::isDemoMode() ) {
			$id = $_POST['id'];
			do_action( 'fullstripe_admin_delete_subscription_form_action', $id );

			try {
				$this->db->delete_subscription_form( $id );

				$return = array( 'success' => true );
			} catch ( Exception $e ) {
				error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
				$return = array(
					'success'  => false,
					'ex_msg'   => $e->getMessage(),
					'ex_stack' => $e->getTraceAsString()
				);
			}

		} else {
			$return = array( 'success' => true );
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function fullstripe_delete_checkout_form() {
		if ( ! MM_WPFS_Utils::isDemoMode() ) {
			$id = $_POST['id'];
			do_action( 'fullstripe_admin_delete_checkout_form_action', $id );

			try {

				$this->db->delete_checkout_form( $id );

				$return = array( 'success' => true );
			} catch ( Exception $e ) {
				error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
				$return = array(
					'success'  => true,
					'ex_msg'   => $e->getMessage(),
					'ex_stack' => $e->getTraceAsString()
				);
			}
		} else {
			$return = array( 'success' => true );
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function fullstripe_delete_popup_card_capture_form() {
		if ( ! MM_WPFS_Utils::isDemoMode() ) {
			$id = $_POST['id'];
			do_action( 'fullstripe_admin_delete_popup_card_capture_form_action', $id );

			try {

				$this->db->delete_checkout_form( $id );

				$return = array( 'success' => true );
			} catch ( Exception $e ) {
				error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
				$return = array(
					'success'  => true,
					'ex_msg'   => $e->getMessage(),
					'ex_stack' => $e->getTraceAsString()
				);
			}
		} else {
			$return = array( 'success' => true );
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function fullstripe_delete_checkout_subscription_form() {
		if ( ! MM_WPFS_Utils::isDemoMode() ) {
			$id = $_POST['id'];
			do_action( 'fullstripe_admin_delete_checkout_subscription_form_action', $id );

			try {
				$this->db->delete_checkout_subscription_form( $id );

				$return = array( 'success' => true );
			} catch ( Exception $e ) {
				error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
				$return = array(
					'success'  => false,
					'ex_msg'   => $e->getMessage(),
					'ex_stack' => $e->getTraceAsString()
				);
			}

		} else {
			$return = array( 'success' => true );
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function fullstripe_cancel_subscription() {
		if ( ! MM_WPFS_Utils::isDemoMode() ) {
			$id = $_POST['id'];

			do_action( 'fullstripe_admin_delete_subscriber_action', $id );

			try {

				$subscriber = $this->db->find_subscriber_by_id( $id );

				if ( $subscriber ) {
					$success = $this->stripe->cancel_subscription( $subscriber->stripeCustomerID, $subscriber->stripeSubscriptionID );
					if ( $success ) {
						$this->db->cancel_subscription( $id );
						$return = array(
							'success'     => true,
							'remove'      => false,
							'redirectURL' => admin_url( 'admin.php?page=fullstripe-subscriptions' )
						);
					} else {
						$return = array( 'success' => false );
					}
				} else {
					$return = array( 'success' => false );
				}
			} catch ( \StripeWPFS\Exception\InvalidRequestException $e ) {
				error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
				// tnagy log exception but return success to continue on client side
				$return = array(
					'success'     => true,
					'with_errors' => true,
					'remove'      => false,
					'redirectURL' => admin_url( 'admin.php?page=fullstripe-subscriptions' ),
					'ex_msg'      => $e->getMessage(),
					'ex_stack'    => $e->getTraceAsString()
				);
			} catch ( Exception $e ) {
				error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
				$return = array(
					'success'  => false,
					'ex_msg'   => $e->getMessage(),
					'ex_stack' => $e->getTraceAsString()
				);
			}

		} else {
			$return = array( 'success' => true );
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

    function fullstripe_cancel_donation() {
        if ( ! MM_WPFS_Utils::isDemoMode() ) {
            $id = $_POST['id'];

            do_action( 'fullstripe_admin_cancel_donation_action', $id );

            try {
                $donation = $this->db->getDonation( $id );

                if ( $donation ) {
                    $success = $this->stripe->cancel_subscription( $donation->stripeCustomerID, $donation->stripeSubscriptionID );
                    if ( $success ) {
                        $this->db->cancelDonationByDonationId( $id );
                        $return = array(
                            'success'     => true,
                            'remove'      => false,
                            'redirectURL' => admin_url( 'admin.php?page=fullstripe-donations' )
                        );
                    } else {
                        $return = array( 'success' => false );
                    }
                } else {
                    $return = array( 'success' => false );
                }
            } catch ( \StripeWPFS\Exception\InvalidRequestException $e ) {
                error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
                // tnagy log exception but return success to continue on client side
                $return = array(
                    'success'     => true,
                    'with_errors' => true,
                    'remove'      => false,
                    'redirectURL' => admin_url( 'admin.php?page=fullstripe-donations' ),
                    'ex_msg'      => $e->getMessage(),
                    'ex_stack'    => $e->getTraceAsString()
                );
            } catch ( Exception $e ) {
                error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
                $return = array(
                    'success'  => false,
                    'ex_msg'   => $e->getMessage(),
                    'ex_stack' => $e->getTraceAsString()
                );
            }

        } else {
            $return = array( 'success' => true );
        }

        header( "Content-Type: application/json" );
        echo json_encode( $return );
        exit;
    }

    public function fullstripe_delete_subscription_record() {
		if ( ! MM_WPFS_Utils::isDemoMode() ) {
			$id = $_POST['id'];

			do_action( 'fullstripe_admin_delete_subscription_record_action', $id );

			try {

				$this->db->delete_subscription_by_id( $id );

				$return = array(
					'success'     => true,
					'remove'      => false,
					'redirectURL' => admin_url( 'admin.php?page=fullstripe-subscriptions' )
				);
			} catch ( Exception $e ) {
				error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
				$return = array(
					'success'  => false,
					'ex_msg'   => $e->getMessage(),
					'ex_stack' => $e->getTraceAsString()
				);
			}

		} else {
			$return = array( 'success' => true );
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	/**
	 * @deprecated
	 */
	function fullstripe_delete_subscriber_local() {
		if ( ! MM_WPFS_Utils::isDemoMode() ) {
			$id = $_POST['id'];
			do_action( 'fullstripe_admin_delete_subscriber_action', $id );

			try {
				$this->db->delete_subscriber( $id );

				$return = array( 'success' => true );
			} catch ( Exception $e ) {
				error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
				$return = array(
					'success'  => false,
					'ex_msg'   => $e->getMessage(),
					'ex_stack' => $e->getTraceAsString()
				);
			}

		} else {
			$return = array( 'success' => true );
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function fullstripe_delete_payment_local() {
		if ( ! MM_WPFS_Utils::isDemoMode() ) {
			$id = $_POST['id'];
			do_action( 'fullstripe_admin_delete_payment_action', $id );

			try {
				$this->db->delete_payment( $id );

				$return = array( 'success' => true );
			} catch ( Exception $e ) {
				error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
				$return = array(
					'success'  => false,
					'ex_msg'   => $e->getMessage(),
					'ex_stack' => $e->getTraceAsString()
				);
			}
		} else {
			$return = array( 'success' => true );
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

    function fullstripe_delete_donation_local() {
        if ( ! MM_WPFS_Utils::isDemoMode() ) {
            $id = $_POST['id'];
            do_action( 'fullstripe_admin_delete_donation_action', $id );

            try {
                $this->db->deleteDonation( $id );

                $return = array( 'success' => true );
            } catch ( Exception $e ) {
                error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
                $return = array(
                    'success'  => false,
                    'ex_msg'   => $e->getMessage(),
                    'ex_stack' => $e->getTraceAsString()
                );
            }
        } else {
            $return = array( 'success' => true );
        }

        header( "Content-Type: application/json" );
        echo json_encode( $return );
        exit;
    }

    function fullstripe_refund_payment() {
		if ( ! MM_WPFS_Utils::isDemoMode() ) {
			$id = $_POST['id'];
			do_action( 'fullstripe_admin_refund_payment_action', $id );

			try {

				$success = $this->refund( $id );

				$return = array(
					'success'     => $success,
					'redirectURL' => add_query_arg(
						array( 'page' => 'fullstripe-payments', 'tab' => 'payments' ),
						admin_url( 'admin.php' )
					)
				);
			} catch ( Exception $e ) {
				error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
				$return = array(
					'success'  => false,
					'ex_msg'   => $e->getMessage(),
					'ex_stack' => $e->getTraceAsString()
				);
			}
		} else {
			$return = array( 'success' => true );
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

    function fullstripe_refund_donation() {
        if ( ! MM_WPFS_Utils::isDemoMode() ) {
            $id = $_POST['id'];
            do_action( 'fullstripe_admin_refund_donation_action', $id );

            try {
                $success = $this->refundDonation( $id );

                $return = array(
                    'success'     => $success,
                    'redirectURL' => add_query_arg(
                        array( 'page' => 'fullstripe-donations', 'tab' => 'donations' ),
                        admin_url( 'admin.php' )
                    )
                );
            } catch ( Exception $e ) {
                error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
                $return = array(
                    'success'  => false,
                    'ex_msg'   => $e->getMessage(),
                    'ex_stack' => $e->getTraceAsString()
                );
            }
        } else {
            $return = array( 'success' => true );
        }

        header( "Content-Type: application/json" );
        echo json_encode( $return );
        exit;
    }

    /**
     * @param $id
     *
     * @return mixed|null|\StripeWPFS\ApiResource
     */
    private function refundDonation( $id ) {
        if ( $this->debugLog ) {
            MM_WPFS_Utils::log( __CLASS__ . "." . __FUNCTION__ . '(): CALLED, id=' . print_r( $id, true ) );
        }

        $donation = null;
        if ( ! is_null( $id ) ) {
            $donation = $this->db->getDonation( $id );
        }

        $refundedSuccessfully = false;
        if ( isset( $donation ) ) {
            $donationStatus = MM_WPFS_Utils::getDonationStatus( $donation );

            if ( MM_WPFS::PAYMENT_STATUS_AUTHORIZED === $donationStatus ||
                 MM_WPFS::PAYMENT_STATUS_PAID === $donationStatus ) {
                $refund = $this->stripe->cancelOrRefundPaymentIntent( $donation->stripePaymentIntentID );
                if ( $this->debugLog ) {
                    MM_WPFS_Utils::log( 'refund(): Refund result object=' . print_r( $refund, true ) );
                }
                if ( $refund instanceof \StripeWPFS\PaymentIntent ) {
                    $paymentIntent = $refund;
                    if ( \StripeWPFS\PaymentIntent::STATUS_CANCELED === $paymentIntent->status ) {
                        $refundedSuccessfully = true;
                    }
                } elseif ( $refund instanceof \StripeWPFS\Refund ) {
                    if ( MM_WPFS::REFUND_STATUS_SUCCEEDED === $refund->status ) {
                        $refundedSuccessfully = true;
                    }
                }

                if ( $refundedSuccessfully ) {
                    $this->db->updateDonationByPaymentIntentId(
                        $donation->stripePaymentIntentID,
                        array(
                            'refunded' => true
                        )
                    );
                }

                return $refundedSuccessfully;
            }
        }

        return false;
    }


    /**
	 * @param $id
	 *
	 * @return mixed|null|\StripeWPFS\ApiResource
	 */
	private function refund( $id ) {
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'refund(): CALLED, id=' . print_r( $id, true ) );
		}
		$payment = null;

		if ( ! is_null( $id ) ) {
			$payment = $this->db->get_payment( $id );
		}

		$refundedSuccessfully = false;
		if ( isset( $payment ) ) {
			$payment_status      = MM_WPFS_Utils::get_payment_status( $payment );
			$payment_object_type = MM_WPFS_Utils::get_payment_object_type( $payment );
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log(
					"refund(): payment_status=$payment_status, payment_object_type=$payment_object_type"
				);
			}
			if (
				MM_WPFS::PAYMENT_STATUS_AUTHORIZED === $payment_status
				|| MM_WPFS::PAYMENT_STATUS_PAID === $payment_status
			) {
				if ( MM_WPFS::PAYMENT_OBJECT_TYPE_STRIPE_CHARGE === $payment_object_type ) {
					$refund = $this->stripe->refund_charge( $payment->eventID );

					if ( $refund instanceof \StripeWPFS\Refund && MM_WPFS::REFUND_STATUS_SUCCEEDED === $refund->status ) {
						$refundedSuccessfully = true;
					}
				} elseif ( MM_WPFS::PAYMENT_OBJECT_TYPE_STRIPE_PAYMENT_INTENT === $payment_object_type ) {
					$refund = $this->stripe->cancelOrRefundPaymentIntent( $payment->eventID );
					if ( $this->debugLog ) {
						MM_WPFS_Utils::log( 'refund(): Refund result object=' . print_r( $refund, true ) );
					}
					if ( $refund instanceof \StripeWPFS\PaymentIntent ) {
						$paymentIntent = $refund;
						if ( \StripeWPFS\PaymentIntent::STATUS_CANCELED === $paymentIntent->status ) {
							$refundedSuccessfully = true;
						}
					} elseif ( $refund instanceof \StripeWPFS\Refund ) {
						if ( MM_WPFS::REFUND_STATUS_SUCCEEDED === $refund->status ) {
							$refundedSuccessfully = true;
						}
					}
				}
				if ( $refundedSuccessfully ) {
					$this->db->updatePaymentByEventId(
						$payment->eventID,
						array(
							'refunded' => true
						)
					);
				}

				return $refundedSuccessfully;
			}
		}

		return false;
	}

	function fullstripe_capture_payment() {
		if ( ! MM_WPFS_Utils::isDemoMode() ) {
			$id = $_POST['id'];
			do_action( 'fullstripe_admin_capture_payment_action', $id );

			try {

				$success = $this->capture( $id );

				$return = array(
					'success'     => $success,
					'redirectURL' => add_query_arg(
						array( 'page' => 'fullstripe-payments', 'tab' => 'payments' ),
						admin_url( 'admin.php' )
					)
				);
			} catch ( Exception $e ) {
				error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
				$return = array(
					'success'  => false,
					'ex_msg'   => $e->getMessage(),
					'ex_stack' => $e->getTraceAsString()
				);
			}
		} else {
			$return = array( 'success' => true );
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	/**
	 * @param $payment_id
	 *
	 * @return bool
	 *
	 */
	private function capture( $payment_id ) {
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'capture(): CALLED, payment_id=' . print_r( $payment_id, true ) );
		}
		$payment = $this->db->get_payment( $payment_id );
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'capture(): payment=' . print_r( $payment, true ) );
		}
		if ( isset( $payment ) ) {
			$payment_status      = MM_WPFS_Utils::get_payment_status( $payment );
			$payment_object_type = MM_WPFS_Utils::get_payment_object_type( $payment );
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( "capture(): payment_status=$payment_status, payment_object_type=$payment_object_type" );
			}
			if ( MM_WPFS::PAYMENT_STATUS_AUTHORIZED === $payment_status ) {
				if ( MM_WPFS::PAYMENT_OBJECT_TYPE_STRIPE_CHARGE === $payment_object_type ) {
					$charge = $this->stripe->capture_charge( $payment->eventID );
					if ( $charge instanceof \StripeWPFS\Charge ) {
						if ( true === $charge->captured && MM_WPFS::STRIPE_CHARGE_STATUS_SUCCEEDED === $charge->status ) {
							$this->db->updatePaymentByEventId(
								$charge->id,
								array(
									'paid'               => $charge->paid,
									'captured'           => $charge->captured,
									'refunded'           => $charge->refunded,
									'last_charge_status' => $charge->status,
									'failure_code'       => $charge->failure_code,
									'failure_message'    => $charge->failure_message
								)
							);

							return true;
						}
					}
				} elseif ( MM_WPFS::PAYMENT_OBJECT_TYPE_STRIPE_PAYMENT_INTENT === $payment_object_type ) {
					$paymentIntent = $this->stripe->capturePaymentIntent( $payment->eventID );
					$lastCharge    = $paymentIntent->charges->data[0];
					if ( $lastCharge instanceof \StripeWPFS\Charge ) {
						if ( true === $lastCharge->captured && MM_WPFS::STRIPE_CHARGE_STATUS_SUCCEEDED === $lastCharge->status ) {
							$this->db->updatePaymentByEventId(
								$paymentIntent->id,
								array(
									'paid'               => $lastCharge->paid,
									'captured'           => $lastCharge->captured,
									'refunded'           => $lastCharge->refunded,
									'last_charge_status' => $lastCharge->status,
									'failure_code'       => $lastCharge->failure_code,
									'failure_message'    => $lastCharge->failure_message
								)
							);

							return true;
						}
					}
				}

			}
		}

		return false;
	}

	function fullstripe_delete_card_capture_local() {
		$redirectURL = add_query_arg(
			array(
				'page' => 'fullstripe-saved-cards',
				'tab'  => 'saved_cards'
			),
			admin_url( 'admin.php' )
		);
		if ( ! MM_WPFS_Utils::isDemoMode() ) {
			$id = $_POST['id'];
			do_action( 'fullstripe_admin_delete_card_capture_action', $id );

			try {
				$this->db->delete_card_capture( $id );

				$return = array(
					'success'     => true,
					'redirectURL' => $redirectURL
				);
			} catch ( Exception $e ) {
				error_log( sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
				$return = array(
					'success'  => false,
					'ex_msg'   => $e->getMessage(),
					'ex_stack' => $e->getTraceAsString()
				);
			}
		} else {
			$return = array(
				'success'     => true,
				'redirectURL' => $redirectURL
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	/**
	 * @deprecated
	 */
	function fullstripe_create_recipient() {
		$token = $_POST['stripeToken'];
		$name  = $_POST['recipient_name'];
		$type  = $_POST['recipient_type'];
		$taxID = isset( $_POST['recipient_tax_id'] ) ? $_POST['recipient_tax_id'] : '';
		$email = isset( $_POST['recipient_email'] ) ? $_POST['recipient_email'] : '';

		$data = array(
			'name'         => $name,
			'type'         => $type,
			'bank_account' => $token
		);
		//optional fields
		if ( $taxID !== '' ) {
			$data['tax_id'] = $taxID;
		}
		if ( $email !== '' ) {
			$data['email'] = $email;
		}

		try {
			$recipient = $this->stripe->create_recipient( $data );

			do_action( 'fullstripe_admin_create_recipient_action', $recipient );

			$return = array( 'success' => true, 'msg' => 'Recipient created' );

		} catch ( Exception $e ) {
			//show notification of error
			$return = array(
				'success' => false,
				'msg'     => sprintf( __( 'There was an error creating the recipient: %s', MM_WPFS::L10N_DOMAIN_ADMIN ), $e->getMessage() )
			);
		}

		//correct way to return JS results in wordpress
		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	/**
	 * @deprecated
	 */
	function fullstripe_create_recipient_card() {
		$token = $_POST['stripeToken'];
		$name  = $_POST['recipient_name_card'];
		$type  = $_POST['recipient_type_card'];
		$taxID = isset( $_POST['recipient_tax_id_card'] ) ? $_POST['recipient_tax_id_card'] : '';
		$email = isset( $_POST['recipient_email_card'] ) ? $_POST['recipient_email_card'] : '';

		$data = array(
			'name' => $name,
			'type' => $type,
			'card' => $token
		);
		//optional fields
		if ( $taxID !== '' ) {
			$data['tax_id'] = $taxID;
		}
		if ( $email !== '' ) {
			$data['email'] = $email;
		}

		try {
			$recipient = $this->stripe->create_recipient( $data );

			do_action( 'fullstripe_admin_create_recipient_action', $recipient );

			$return = array( 'success' => true, 'msg' => 'Recipient created' );

		} catch ( Exception $e ) {
			//show notification of error
			$return = array(
				'success' => false,
				'msg'     => __( 'There was an error creating the recipient: ', MM_WPFS::L10N_DOMAIN_ADMIN ) . $e->getMessage()
			);
		}

		//correct way to return JS results in wordpress
		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function fullstripe_create_transfer() {
		$amount    = $_POST['transfer_amount'];
		$desc      = $_POST['transfer_desc'];
		$recipient = $_POST['transfer_recipient'];

		try {
			$transfer = $this->stripe->create_transfer( array(
				"amount"                => $amount,
				"currency"              => MM_WPFS::CURRENCY_USD,
				"recipient"             => $recipient,
				"statement_description" => $desc
			) );

			do_action( 'fullstripe_admin_create_transfer_action', $transfer );

			$return = array( 'success' => true );
		} catch ( Exception $e ) {
			//show notification of error
			$return = array(
				'success' => false,
				'msg'     => __( 'There was an error creating the transfer: ', MM_WPFS::L10N_DOMAIN_ADMIN ) . $e->getMessage()
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function fullstripe_edit_subscription_plan_post() {
		if ( ! MM_WPFS_Utils::isDemoMode() ) {
			$plan_id              = stripslashes( $_POST['plan'] );
			$display_name         = $_POST['plan_display_name'];
			$statement_descriptor = isset( $_POST['plan_statement_descriptor'] ) ? $_POST['plan_statement_descriptor'] : null;
			$setup_fee            = isset( $_POST['plan_setup_fee'] ) ? $_POST['plan_setup_fee'] : null;

			try {
				$this->stripe->update_plan( $plan_id, array(
					'setup_fee'            => $setup_fee,
					'name'                 => $display_name,
					'statement_descriptor' => $statement_descriptor
				) );

				$return = array(
					'success'     => true,
					'msg'         => 'Subscription plan updated',
					'redirectURL' => admin_url( 'admin.php?page=fullstripe-subscriptions&tab=plans' )
				);
			} catch ( Exception $e ) {
				$return = array(
					'success' => false,
					'msg'     => __( 'There was an error updating the subscription plan: ', MM_WPFS::L10N_DOMAIN_ADMIN ) . $e->getMessage()
				);
			}
		} else {
			$return = array(
				'success'     => true,
				'msg'         => 'Subscription plan updated',
				'redirectURL' => admin_url( 'admin.php?page=fullstripe-subscriptions&tab=plans' )
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function fullstripe_delete_subscription_plan() {

		if ( ! MM_WPFS_Utils::isDemoMode() ) {
			$plan_id = stripslashes( $_POST['id'] );

			try {
				$this->stripe->delete_plan( $plan_id );

				$return = array(
					'success'     => true,
					'msg'         => 'Subscription plan deleted',
					'redirectURL' => admin_url( 'admin.php?page=fullstripe-subscriptions&tab=plans' )
				);
			} catch ( Exception $e ) {
				$return = array(
					'success' => false,
					'msg'     => __( 'There was an error deleting the subscription plan: ', MM_WPFS::L10N_DOMAIN_ADMIN ) . $e->getMessage()
				);
			}
		} else {
			$return = array(
				'success'     => true,
				'msg'         => 'Subscription plan deleted',
				'redirectURL' => admin_url( 'admin.php?page=fullstripe-subscriptions&tab=plans' )
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	/**
	 * Stripe Web hook handler
	 */
	function fullstripe_handle_wpfs_event() {

		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'fullstripe_handle_wpfs_event(): ' . 'CALLED' );
		}

		$auth_token     = empty( $_REQUEST['auth_token'] ) ? '' : $_REQUEST['auth_token'];
		$web_hook_token = self::get_webhook_token();

		if ( $web_hook_token !== $auth_token ) {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'fullstripe_handle_wpfs_event(): ' . 'Authentication failed, abort.' );
			}
			// return HTTP Unathorized
			status_header( 401 );
			header( 'Content-Type: application/json' );
			exit;
		}

		try {

			// Retrieve the request's body and parse it as JSON
			$input = @file_get_contents( "php://input" );
			// error_log( 'DEBUG: input=' . json_encode( $input ) );

			$event = json_decode( $input );

			// Do something with $event_json
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'fullstripe_handle_wpfs_event(): ' . 'event=' . json_encode( $event ) );
			}

			$event_processed = $this->eventHandler->handle( $event );

			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'fullstripe_handle_wpfs_event(): ' . 'event processed? ' . ( $event_processed === true ? 'true' : 'false' ) );
			}

			// return HTTP OK
			status_header( 200 );
		} catch ( Exception $e ) {
			error_log( 'ERROR: Message=' . $e->getMessage() . ', Trace=' . $e->getTraceAsString() );
			// return HTTP Internal Server Error
			status_header( 500 );
		}

		header( "Content-Type: application/json" );
		exit;
	}

	/**
	 * Generates the md5 hash by site_url and admin_email to create a unique ID for a WordPress installation.
	 * @return string
	 */
	public static function get_webhook_token() {
		$options = get_option( 'fullstripe_options' );

		return $options['webhook_token'];
	}

}