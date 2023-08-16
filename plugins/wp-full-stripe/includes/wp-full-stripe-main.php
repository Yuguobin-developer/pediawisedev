<?php

/*
WP Full Stripe
https://paymentsplugin.com
Complete Stripe payments integration for Wordpress
Mammothology
5.5.2
https://paymentsplugin.com
*/

class MM_WPFS {

	const VERSION = '5.5.2';
	const REQUEST_PARAM_NAME_WPFS_RENDERED_FORMS = 'wpfs_rendered_forms';

	const HANDLE_WP_FULL_STRIPE_JS = 'wp-full-stripe-js';

	const SHORTCODE_FULLSTRIPE_FORM = 'fullstripe_form';
	const SHORTCODE_FULLSTRIPE_THANKYOU = 'fullstripe_thankyou';
	const SHORTCODE_FULLSTRIPE_THANKYOU_SUCCESS = 'fullstripe_thankyou_success';
	const SHORTCODE_FULLSTRIPE_THANKYOU_DEFAULT = 'fullstripe_thankyou_default';
	/**
	 * @deprecated
	 */
	const SHORTCODE_FULLSTRIPE_SUBSCRIPTION_CHECKOUT = 'fullstripe_subscription_checkout';
	/**
	 * @deprecated
	 */
	const SHORTCODE_FULLSTRIPE_CHECKOUT = 'fullstripe_checkout';
	/**
	 * @deprecated
	 */
	const SHORTCODE_FULLSTRIPE_SUBSCRIPTION = 'fullstripe_subscription';
	/**
	 * @deprecated
	 */
	const SHORTCODE_FULLSTRIPE_PAYMENT = 'fullstripe_payment';
	/**
	 * @deprecated
	 */
	const HANDLE_WP_FULL_STRIPE_JS_V_3 = 'wp-full-stripe-v3-js';
	const HANDLE_WP_FULL_STRIPE_UTILS_JS = 'wp-full-stripe-utils-js';
	const HANDLE_SPRINTF_JS = 'sprintf-js';
	/**
	 * @deprecated
	 */
	const HANDLE_STRIPE_CHECKOUT_JS = 'checkout-js';
	/**
	 * @deprecated
	 */
	const HANDLE_STRIPE_JS_V_2 = 'stripe-js-v2';
	const HANDLE_STRIPE_JS_V_3 = 'stripe-js-v3';
	const HANDLE_STYLE_WPFS_VARIABLES = 'wpfs-variables-css';
	const HANDLE_STYLE_WPFS_FORMS = 'wpfs-forms-css';
	const HANDLE_GOOGLE_RECAPTCHA_V_2 = 'google-recaptcha-v2';
	const URL_RECAPTCHA_API_SITEVERIFY = 'https://www.google.com/recaptcha/api/siteverify';
	const SOURCE_GOOGLE_RECAPTCHA_V2_API_JS = 'https://www.google.com/recaptcha/api.js';

	const FORM_TYPE_PAYMENT = 'payment';
	const FORM_TYPE_CHECKOUT_SUBSCRIPTION = 'checkout-subscription';
	const FORM_TYPE_SUBSCRIPTION = 'subscription';
	const FORM_TYPE_CHECKOUT = 'checkout';

	// tnagy new form type denominations 
	const FORM_TYPE_INLINE_PAYMENT = 'inline_payment';
	const FORM_TYPE_POPUP_PAYMENT = 'popup_payment';
	const FORM_TYPE_INLINE_SUBSCRIPTION = 'inline_subscription';
	const FORM_TYPE_POPUP_SUBSCRIPTION = 'popup_subscription';
	const FORM_TYPE_INLINE_SAVE_CARD = 'inline_save_card';
	const FORM_TYPE_POPUP_SAVE_CARD = 'popup_save_card';
    const FORM_TYPE_INLINE_DONATION = 'inline_donation';
    const FORM_TYPE_POPUP_DONATION = 'popup_donation';

	const VAT_RATE_TYPE_NO_VAT = 'no_vat';
	const VAT_RATE_TYPE_FIXED_VAT = 'fixed_vat';
	const VAT_RATE_TYPE_CUSTOM_VAT = 'custom_vat';

	const NO_VAT_PERCENT = 0.0;

	const DEFAULT_BILLING_COUNTRY_INITIAL_VALUE = 'US';

	const PREFERRED_LANGUAGE_AUTO = 'auto';

	const DEFAULT_CUSTOM_INPUT_FIELD_MAX_COUNT = 10;

	const PAYMENT_TYPE_LIST_OF_AMOUNTS = 'list_of_amounts';
	const PAYMENT_TYPE_CUSTOM_AMOUNT = 'custom_amount';
	const PAYMENT_TYPE_SPECIFIED_AMOUNT = 'specified_amount';
	const PAYMENT_TYPE_CARD_CAPTURE = 'card_capture';

	const CURRENCY_USD = 'usd';

	const OPTION_SECURE_INLINE_FORMS_WITH_GOOGLE_RE_CAPTCHA = 'secure_inline_forms_with_google_recaptcha';
	const OPTION_SECURE_CHECKOUT_FORMS_WITH_GOOGLE_RE_CAPTCHA = 'secure_checkout_forms_with_google_recaptcha';
	const OPTION_SECURE_SUBSCRIPTION_UPDATE_WITH_GOOGLE_RE_CAPTCHA = 'secure_subscription_update_with_google_recaptcha';
	const OPTION_GOOGLE_RE_CAPTCHA_SITE_KEY = 'google_recaptcha_site_key';
	const OPTION_GOOGLE_RE_CAPTCHA_SECRET_KEY = 'google_recaptcha_secret_key';
	const OPTION_MY_ACCOUNT_SHOW_INVOICES_SECTION = 'my_account_show_invoices_section';
	const OPTION_MY_ACCOUNT_SHOW_ALL_INVOICES = 'my_account_show_all_invoices';
	const OPTION_MY_ACCOUNT_LET_SUBSCRIBERS_CANCEL_SUBSCRIPTIONS = 'my_account_subscribers_cancel_subscriptions';
	const OPTION_DECIMAL_SEPARATOR_SYMBOL = 'decimal_separator_symbol';
	const OPTION_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE = 'show_currency_symbol_instead_of_code';
	const OPTION_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION = 'show_currency_sign_first';
	const OPTION_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT = 'put_whitespace_between_currency_and_amount';

	const DECIMAL_SEPARATOR_SYMBOL_DOT = 'dot';
	const DECIMAL_SEPARATOR_SYMBOL_COMMA = 'comma';

	const CHARGE_TYPE_IMMEDIATE = 'immediate';
	const CHARGE_TYPE_AUTHORIZE_AND_CAPTURE = 'authorize_and_capture';

	const PAYMENT_METHOD_CARD = 'card';

	const STRIPE_CHARGE_STATUS_SUCCEEDED = 'succeeded';
	const STRIPE_CHARGE_STATUS_PENDING = 'pending';
	const STRIPE_CHARGE_STATUS_FAILED = 'failed';

	const STRIPE_SUBSCRIPTION_STATUS_TRIALING = 'trialing';
	const STRIPE_SUBSCRIPTION_STATUS_ACTIVE = 'active';
	const STRIPE_SUBSCRIPTION_STATUS_PAST_DUE = 'past_due';
	const STRIPE_SUBSCRIPTION_STATUS_CANCELED = 'canceled';
	const STRIPE_SUBSCRIPTION_STATUS_UNPAID = 'unpaid';

	const PAYMENT_STATUS_UNKNOWN = 'unknown';
	const PAYMENT_STATUS_FAILED = 'failed';
	const PAYMENT_STATUS_REFUNDED = 'refunded';
	const PAYMENT_STATUS_EXPIRED = 'expired';
	const PAYMENT_STATUS_PAID = 'paid';
	const PAYMENT_STATUS_AUTHORIZED = 'authorized';
	const PAYMENT_STATUS_PENDING = 'pending';
	const PAYMENT_STATUS_RELEASED = 'released';

	const REFUND_STATUS_SUCCEEDED = 'succeeded';
	const REFUND_STATUS_FAILED = 'failed';
	const REFUND_STATUS_PENDING = 'pending';
	const REFUND_STATUS_CANCELED = 'canceled';

	const SUBSCRIPTION_STATUS_ENDED = 'ended';
	const SUBSCRIPTION_STATUS_CANCELLED = 'cancelled';

	const AMOUNT_SELECTOR_STYLE_RADIO_BUTTONS = 'radio-buttons';
	const AMOUNT_SELECTOR_STYLE_DROPDOWN = 'dropdown';
	const AMOUNT_SELECTOR_STYLE_BUTTON_GROUP = 'button-group';

	const PLAN_SELECTOR_STYLE_DROPDOWN = 'dropdown';
	const PLAN_SELECTOR_STYLE_LIST = 'list';

	const JS_VARIABLE_AJAX_URL = 'wpfsAjaxURL';
	const JS_VARIABLE_STRIPE_KEY = 'wpfsStripeKey';
	const JS_VARIABLE_GOOGLE_RECAPTCHA_SITE_KEY = 'wpfsGoogleReCAPTCHASiteKey';
	const JS_VARIABLE_L10N = 'wpfsL10n';
	const JS_VARIABLE_FORM_FIELDS = 'wpfsFormFields';

	const ACTION_NAME_BEFORE_CARD_CAPTURE = 'fullstripe_before_card_capture';
	const ACTION_NAME_AFTER_CARD_CAPTURE = 'fullstripe_after_card_capture';
	const ACTION_NAME_BEFORE_CHECKOUT_CARD_CAPTURE = 'fullstripe_before_checkout_card_capture';
	const ACTION_NAME_AFTER_CHECKOUT_CARD_CAPTURE = 'fullstripe_after_checkout_card_capture';

	const ACTION_NAME_BEFORE_PAYMENT_CHARGE = 'fullstripe_before_payment_charge';
	const ACTION_NAME_AFTER_PAYMENT_CHARGE = 'fullstripe_after_payment_charge';
	const ACTION_NAME_BEFORE_CHECKOUT_PAYMENT_CHARGE = 'fullstripe_before_checkout_payment_charge';
	const ACTION_NAME_AFTER_CHECKOUT_PAYMENT_CHARGE = 'fullstripe_after_checkout_payment_charge';

    const ACTION_NAME_BEFORE_DONATION_CHARGE = 'fullstripe_before_donation_charge';
    const ACTION_NAME_AFTER_DONATION_CHARGE = 'fullstripe_after_donation_charge';
    const ACTION_NAME_BEFORE_CHECKOUT_DONATION_CHARGE = 'fullstripe_before_checkout_donation_charge';
    const ACTION_NAME_AFTER_CHECKOUT_DONATION_CHARGE = 'fullstripe_after_checkout_donation_charge';

    const ACTION_NAME_BEFORE_SUBSCRIPTION_CHARGE = 'fullstripe_before_subscription_charge';
	const ACTION_NAME_AFTER_SUBSCRIPTION_CHARGE = 'fullstripe_after_subscription_charge';
	const ACTION_NAME_BEFORE_CHECKOUT_SUBSCRIPTION_CHARGE = 'fullstripe_before_checkout_subscription_charge';
	const ACTION_NAME_AFTER_CHECKOUT_SUBSCRIPTION_CHARGE = 'fullstripe_after_checkout_subscription_charge';

	const ACTION_NAME_BEFORE_SUBSCRIPTION_CANCELLATION = 'fullstripe_before_subscription_cancellation';
	const ACTION_NAME_AFTER_SUBSCRIPTION_CANCELLATION = 'fullstripe_after_subscription_cancellation';

	const FILTER_NAME_GET_VAT_PERCENT = 'fullstripe_get_vat_percent';
	const FILTER_NAME_SELECT_SUBSCRIPTION_PLAN = 'fullstripe_select_subscription_plan';
	const FILTER_NAME_SET_CUSTOM_AMOUNT = 'fullstripe_set_custom_amount';
	const FILTER_NAME_ADD_TRANSACTION_METADATA = 'fullstripe_add_transaction_metadata';
	const FILTER_NAME_MODIFY_EMAIL_MESSAGE = 'fullstripe_modify_email_message';
	const FILTER_NAME_MODIFY_EMAIL_SUBJECT = 'fullstripe_modify_email_subject';

	const STRIPE_OBJECT_ID_PREFIX_PAYMENT_INTENT = 'pi_';
	const STRIPE_OBJECT_ID_PREFIX_CHARGE = 'ch_';
	const PAYMENT_OBJECT_TYPE_UNKNOWN = 'Unknown';
	const PAYMENT_OBJECT_TYPE_STRIPE_PAYMENT_INTENT = '\StripeWPFS\PaymentIntent';
	const PAYMENT_OBJECT_TYPE_STRIPE_CHARGE = '\StripeWPFS\Charge';

	const SUBSCRIBER_STATUS_CANCELLED = 'cancelled';
	const SUBSCRIBER_STATUS_RUNNING = 'running';
	const SUBSCRIBER_STATUS_ENDED = 'ended';
	const SUBSCRIBER_STATUS_INCOMPLETE = 'incomplete';

	const HTTP_PARAM_NAME_PLAN = 'wpfsPlan';
	const HTTP_PARAM_NAME_AMOUNT = 'wpfsAmount';

	const L10N_DOMAIN_PUBLIC = 'wp-full-stripe';
	const L10N_DOMAIN_ADMIN = 'wp-full-stripe-admin';

    const DONATION_PLAN_ID_PREFIX = "wpfsDonationPlan";

    public static $instance;

	private $debugLog = false;

	/** @var MM_WPFS_Customer */
	private $customer = null;
	/** @var MM_WPFS_Admin */
	private $admin = null;
	/** @var MM_WPFS_Database */
	private $database = null;
	/** @var MM_WPFS_Stripe */
	private $stripe = null;
	/** @var MM_WPFS_Admin_Menu */
	private $adminMenu = null;
	/** @var MM_WPFS_TransactionDataService */
	private $transactionDataService = null;
	/** @var MM_WPFS_CardUpdateService */
	private $cardUpdateService = null;
	/** @var MM_WPFS_CheckoutSubmissionService */
	private $checkoutSubmissionService = null;
	/**
	 * @var bool Choose to load scripts and styles the WordPress way. We should move this field to a wp_option later.
	 */
	private $loadScriptsAndStylesWithActionHook = false;

	public function __construct() {

		$this->includes();
		$this->setup();
		$this->hooks();

	}

	function includes() {

		include 'wp-full-stripe-localization.php';
		include 'wp-full-stripe-admin.php';
		include 'wp-full-stripe-admin-menu.php';
		include 'wp-full-stripe-form-models.php';
		include 'wp-full-stripe-assets.php';
		include 'wp-full-stripe-my-account-service.php';
		include 'wp-full-stripe-checkout-charge-handler.php';
		include 'wp-full-stripe-checkout-submission-service.php';
		include 'wp-full-stripe-countries.php';
		include 'wp-full-stripe-currencies.php';
		include 'wp-full-stripe-customer.php';
		include 'wp-full-stripe-database.php';
		include 'wp-full-stripe-logger-service.php';
		include 'wp-full-stripe-mailer.php';
		include 'wp-full-stripe-news-feed-url.php';
		include 'wp-full-stripe-patcher.php';
		include 'wp-full-stripe-payments.php';
		include 'wp-full-stripe-public-form-views.php';
		include 'wp-full-stripe-transaction-data-service.php';
		include 'wp-full-stripe-validators.php';
		include 'wp-full-stripe-web-hook-events.php';

		do_action( 'fullstripe_includes_action' );
	}

	function setup() {

		//set option defaults
		$options = get_option( 'fullstripe_options' );
		if ( ! $options || $options['fullstripe_version'] != self::VERSION ) {
			$this->set_option_defaults( $options );
			// tnagy reload saved options
			$options = get_option( 'fullstripe_options' );
		}
		$this->update_option_defaults( $options );

		MM_WPFS_LicenseManager::getInstance()->activateLicenseIfNeeded();

		//set API key
		if ( $options['apiMode'] === 'test' ) {
			$this->fullstripe_set_api_key_and_version( $options['secretKey_test'] );
		} else {
			$this->fullstripe_set_api_key_and_version( $options['secretKey_live'] );
		}

		//setup subclasses to handle everything
		$this->admin                     = new MM_WPFS_Admin();
		$this->adminMenu                 = new MM_WPFS_Admin_Menu();
		$this->customer                  = new MM_WPFS_Customer();
		$this->database                  = new MM_WPFS_Database();
		$this->stripe                    = new MM_WPFS_Stripe();
		$this->transactionDataService    = new MM_WPFS_TransactionDataService();
		$this->cardUpdateService         = new MM_WPFS_CardUpdateService();
		$this->checkoutSubmissionService = new MM_WPFS_CheckoutSubmissionService();

		do_action( 'fullstripe_setup_action' );

	}

	function set_option_defaults( $options ) {
		if ( ! $options ) {

			$emailReceipts = MM_WPFS_Utils::create_default_email_receipts();

			/** @noinspection PhpUndefinedClassInspection */
			$default_options = array(
				'secretKey_test'                                                  => 'YOUR_TEST_SECRET_KEY',
				'publishKey_test'                                                 => 'YOUR_TEST_PUBLISHABLE_KEY',
				'secretKey_live'                                                  => 'YOUR_LIVE_SECRET_KEY',
				'publishKey_live'                                                 => 'YOUR_LIVE_PUBLISHABLE_KEY',
				'apiMode'                                                         => 'test',
				'form_css'                                                        => "",
				'includeStyles'                                                   => '1',
				'receiptEmailType'                                                => 'plugin',
				'email_receipts'                                                  => json_encode( $emailReceipts ),
				'email_receipt_sender_address'                                    => '',
				'admin_payment_receipt'                                           => '0',
				'lock_email_field_for_logged_in_users'                            => '1',
				'fullstripe_version'                                              => self::VERSION,
				'webhook_token'                                                   => $this->create_webhook_token(),
				'custom_input_field_max_count'                                    => MM_WPFS::DEFAULT_CUSTOM_INPUT_FIELD_MAX_COUNT,
				MM_WPFS::OPTION_SECURE_INLINE_FORMS_WITH_GOOGLE_RE_CAPTCHA        => '0',
				MM_WPFS::OPTION_SECURE_CHECKOUT_FORMS_WITH_GOOGLE_RE_CAPTCHA      => '0',
				MM_WPFS::OPTION_SECURE_SUBSCRIPTION_UPDATE_WITH_GOOGLE_RE_CAPTCHA => '0',
				MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SITE_KEY                        => 'YOUR_GOOGLE_RECAPTCHA_SITE_KEY',
				MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SECRET_KEY                      => 'YOUR_GOOGLE_RECAPTCHA_SECRET_KEY',
				MM_WPFS::OPTION_MY_ACCOUNT_SHOW_INVOICES_SECTION                  => '1',
				MM_WPFS::OPTION_MY_ACCOUNT_SHOW_ALL_INVOICES                      => '0',
				MM_WPFS::OPTION_MY_ACCOUNT_LET_SUBSCRIBERS_CANCEL_SUBSCRIPTIONS   => '1',
				MM_WPFS::OPTION_DECIMAL_SEPARATOR_SYMBOL                          => MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT,
				MM_WPFS::OPTION_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE              => '1',
				MM_WPFS::OPTION_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION              => '1',
				MM_WPFS::OPTION_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT        => '0'
			);

			$edd_options   = MM_WPFS_LicenseManager::getInstance()->getLicenseOptionDefaults();
			$final_options = array_merge( $default_options, $edd_options );

			update_option( 'fullstripe_options', $final_options );
		} else {

			// different version

			$options['fullstripe_version'] = self::VERSION;
			if ( ! array_key_exists( 'secretKey_test', $options ) ) {
				$options['secretKey_test'] = 'YOUR_TEST_SECRET_KEY';
			}
			if ( ! array_key_exists( 'publishKey_test', $options ) ) {
				$options['publishKey_test'] = 'YOUR_TEST_PUBLISHABLE_KEY';
			}
			if ( ! array_key_exists( 'secretKey_live', $options ) ) {
				$options['secretKey_live'] = 'YOUR_LIVE_SECRET_KEY';
			}
			if ( ! array_key_exists( 'publishKey_live', $options ) ) {
				$options['publishKey_live'] = 'YOUR_LIVE_PUBLISHABLE_KEY';
			}
			if ( ! array_key_exists( 'apiMode', $options ) ) {
				$options['apiMode'] = 'test';
			}
			if ( ! array_key_exists( 'form_css', $options ) ) {
				$options['form_css'] = "";
			}
			if ( ! array_key_exists( 'includeStyles', $options ) ) {
				$options['includeStyles'] = '1';
			}
			if ( ! array_key_exists( 'receiptEmailType', $options ) ) {
				$options['receiptEmailType'] = 'plugin';
			}
			if ( ! array_key_exists( 'email_receipts', $options ) ) {
				$emailReceipts             = MM_WPFS_Utils::create_default_email_receipts();
				$options['email_receipts'] = json_encode( $emailReceipts );
			} else {
				$emailReceipts = json_decode( $options['email_receipts'] );
				if ( ! array_key_exists( 'cardCaptured', $emailReceipts ) ) {
					$emailReceipts->cardCaptured = MM_WPFS_Utils::create_default_card_captured_email_receipt();
					$options['email_receipts']   = json_encode( $emailReceipts );
				}
				if ( ! array_key_exists( 'cardUpdateConfirmationRequest', $emailReceipts ) ) {
					$emailReceipts->cardUpdateConfirmationRequest = MM_WPFS_Utils::create_default_card_update_confirmation_request_email_receipt();
					$options['email_receipts']                    = json_encode( $emailReceipts );
				}
			}
			if ( ! array_key_exists( 'email_receipt_sender_address', $options ) ) {
				$options['email_receipt_sender_address'] = '';
			}
			if ( ! array_key_exists( 'admin_payment_receipt', $options ) ) {
				$options['admin_payment_receipt'] = '0';
			}
			if ( ! array_key_exists( 'lock_email_field_for_logged_in_users', $options ) ) {
				$options['lock_email_field_for_logged_in_users'] = '1';
			}
			if ( ! array_key_exists( 'webhook_token', $options ) ) {
				$options['webhook_token'] = $this->create_webhook_token();
			}
			if ( ! array_key_exists( 'custom_input_field_max_count', $options ) ) {
				$options['custom_input_field_max_count'] = MM_WPFS::DEFAULT_CUSTOM_INPUT_FIELD_MAX_COUNT;
			} elseif ( $options['custom_input_field_max_count'] != MM_WPFS::DEFAULT_CUSTOM_INPUT_FIELD_MAX_COUNT ) {
				$options['custom_input_field_max_count'] = MM_WPFS::DEFAULT_CUSTOM_INPUT_FIELD_MAX_COUNT;
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_SECURE_INLINE_FORMS_WITH_GOOGLE_RE_CAPTCHA, $options ) ) {
				$options[ MM_WPFS::OPTION_SECURE_INLINE_FORMS_WITH_GOOGLE_RE_CAPTCHA ] = '0';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_SECURE_CHECKOUT_FORMS_WITH_GOOGLE_RE_CAPTCHA, $options ) ) {
				$options[ MM_WPFS::OPTION_SECURE_CHECKOUT_FORMS_WITH_GOOGLE_RE_CAPTCHA ] = '0';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_SECURE_SUBSCRIPTION_UPDATE_WITH_GOOGLE_RE_CAPTCHA, $options ) ) {
				$options[ MM_WPFS::OPTION_SECURE_SUBSCRIPTION_UPDATE_WITH_GOOGLE_RE_CAPTCHA ] = '0';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SITE_KEY, $options ) ) {
				$options[ MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SITE_KEY ] = 'YOUR_GOOGLE_RECAPTCHA_SITE_KEY';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SECRET_KEY, $options ) ) {
				$options[ MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SECRET_KEY ] = 'YOUR_GOOGLE_RECAPTCHA_SECRET_KEY';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_MY_ACCOUNT_SHOW_INVOICES_SECTION, $options ) ) {
				$options[ MM_WPFS::OPTION_MY_ACCOUNT_SHOW_INVOICES_SECTION ] = '1';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_MY_ACCOUNT_SHOW_ALL_INVOICES, $options ) ) {
				$options[ MM_WPFS::OPTION_MY_ACCOUNT_SHOW_ALL_INVOICES ] = '0';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_MY_ACCOUNT_LET_SUBSCRIBERS_CANCEL_SUBSCRIPTIONS, $options ) ) {
				$options[ MM_WPFS::OPTION_MY_ACCOUNT_LET_SUBSCRIBERS_CANCEL_SUBSCRIPTIONS ] = '1';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_DECIMAL_SEPARATOR_SYMBOL, $options ) ) {
				$options[ MM_WPFS::OPTION_DECIMAL_SEPARATOR_SYMBOL ] = MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT;
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE, $options ) ) {
				$options[ MM_WPFS::OPTION_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE ] = '1';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION, $options ) ) {
				$options[ MM_WPFS::OPTION_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION ] = '1';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT, $options ) ) {
				$options[ MM_WPFS::OPTION_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT ] = '0';
			}

			MM_WPFS_LicenseManager::getInstance()->setLicenseOptionDefaultsIfEmpty( $options );

			update_option( 'fullstripe_options', $options );
		}

		// also, if version changed then the DB might be out of date
		MM_WPFS::setup_db( false );
	}

	/**
	 * Generates a unique random token for authenticating webhook callbacks.
	 *
	 * @return string
	 */
	private function create_webhook_token() {
		$siteURL           = get_site_url();
		$randomToken       = hash( 'md5', rand() );
		$generatedPassword = substr( hash( 'sha512', rand() ), 0, 6 );

		return hash( 'md5', $siteURL . '|' . $randomToken . '|' . $generatedPassword );
	}

	public static function setup_db( $network_wide ) {
		if ( $network_wide ) {
			MM_WPFS_Utils::log( "setup_db() - Activating network-wide" );
			if ( function_exists( 'get_sites' ) && function_exists( 'get_current_network_id' ) ) {
				$site_ids = get_sites( array( 'fields' => 'ids', 'network_id' => get_current_network_id() ) );
			} else {
				$site_ids = MM_WPFS_Database::get_site_ids();
			}

			foreach ( $site_ids as $site_id ) {
				switch_to_blog( $site_id );
				self::setup_db_single_site();
				restore_current_blog();
			}
		} else {
			MM_WPFS_Utils::log( "setup_db() - Activating for single site" );
			self::setup_db_single_site();
		}
	}

	public static function setup_db_single_site() {
		MM_WPFS_Database::fullstripe_setup_db();
		MM_WPFS_Patcher::apply_patches();
	}

	function update_option_defaults( $options ) {
		if ( $options ) {
			if ( ! array_key_exists( 'secretKey_test', $options ) ) {
				$options['secretKey_test'] = 'YOUR_TEST_SECRET_KEY';
			}
			if ( ! array_key_exists( 'publishKey_test', $options ) ) {
				$options['publishKey_test'] = 'YOUR_TEST_PUBLISHABLE_KEY';
			}
			if ( ! array_key_exists( 'secretKey_live', $options ) ) {
				$options['secretKey_live'] = 'YOUR_LIVE_SECRET_KEY';
			}
			if ( ! array_key_exists( 'publishKey_live', $options ) ) {
				$options['publishKey_live'] = 'YOUR_LIVE_PUBLISHABLE_KEY';
			}
			if ( ! array_key_exists( 'apiMode', $options ) ) {
				$options['apiMode'] = 'test';
			}
			if ( ! array_key_exists( 'form_css', $options ) ) {
				$options['form_css'] = "";
			}
			if ( ! array_key_exists( 'includeStyles', $options ) ) {
				$options['includeStyles'] = '1';
			}
			if ( ! array_key_exists( 'receiptEmailType', $options ) ) {
				$options['receiptEmailType'] = 'plugin';
			}
			if ( ! array_key_exists( 'email_receipts', $options ) ) {
				$emailReceipts             = MM_WPFS_Utils::create_default_email_receipts();
				$options['email_receipts'] = json_encode( $emailReceipts );
			} else {
				$emailReceipts = json_decode( $options['email_receipts'] );
				if ( ! property_exists( $emailReceipts, 'cardCaptured' ) ) {
					$emailReceipts->cardCaptured = MM_WPFS_Utils::create_default_card_captured_email_receipt();
					$options['email_receipts']   = json_encode( $emailReceipts );
				}
				if ( ! property_exists( $emailReceipts, 'cardUpdateConfirmationRequest' ) ) {
					$emailReceipts->cardUpdateConfirmationRequest = MM_WPFS_Utils::create_default_card_update_confirmation_request_email_receipt();
					$options['email_receipts']                    = json_encode( $emailReceipts );
				}
			}
			if ( ! array_key_exists( 'email_receipt_sender_address', $options ) ) {
				$options['email_receipt_sender_address'] = '';
			}
			if ( ! array_key_exists( 'admin_payment_receipt', $options ) ) {
				$options['admin_payment_receipt'] = 'no';
			} else {
				if ( $options['admin_payment_receipt'] == '0' ) {
					$options['admin_payment_receipt'] = 'no';
				}
				if ( $options['admin_payment_receipt'] == '1' ) {
					$options['admin_payment_receipt'] = 'website_admin';
				}
			}
			if ( ! array_key_exists( 'lock_email_field_for_logged_in_users', $options ) ) {
				$options['lock_email_field_for_logged_in_users'] = '1';
			}
			if ( ! array_key_exists( 'webhook_token', $options ) ) {
				$options['webhook_token'] = $this->create_webhook_token();
			}
			if ( ! array_key_exists( 'custom_input_field_max_count', $options ) ) {
				$options['custom_input_field_max_count'] = MM_WPFS::DEFAULT_CUSTOM_INPUT_FIELD_MAX_COUNT;
			} elseif ( $options['custom_input_field_max_count'] != MM_WPFS::DEFAULT_CUSTOM_INPUT_FIELD_MAX_COUNT ) {
				$options['custom_input_field_max_count'] = MM_WPFS::DEFAULT_CUSTOM_INPUT_FIELD_MAX_COUNT;
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_SECURE_INLINE_FORMS_WITH_GOOGLE_RE_CAPTCHA, $options ) ) {
				$options[ MM_WPFS::OPTION_SECURE_INLINE_FORMS_WITH_GOOGLE_RE_CAPTCHA ] = '0';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_SECURE_CHECKOUT_FORMS_WITH_GOOGLE_RE_CAPTCHA, $options ) ) {
				$options[ MM_WPFS::OPTION_SECURE_CHECKOUT_FORMS_WITH_GOOGLE_RE_CAPTCHA ] = '0';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_SECURE_SUBSCRIPTION_UPDATE_WITH_GOOGLE_RE_CAPTCHA, $options ) ) {
				$options[ MM_WPFS::OPTION_SECURE_SUBSCRIPTION_UPDATE_WITH_GOOGLE_RE_CAPTCHA ] = '0';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SITE_KEY, $options ) ) {
				$options[ MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SITE_KEY ] = 'YOUR_GOOGLE_RECAPTCHA_SITE_KEY';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SECRET_KEY, $options ) ) {
				$options[ MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SECRET_KEY ] = 'YOUR_GOOGLE_RECAPTCHA_SECRET_KEY';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_MY_ACCOUNT_SHOW_INVOICES_SECTION, $options ) ) {
				$options[ MM_WPFS::OPTION_MY_ACCOUNT_SHOW_INVOICES_SECTION ] = '1';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_MY_ACCOUNT_SHOW_ALL_INVOICES, $options ) ) {
				$options[ MM_WPFS::OPTION_MY_ACCOUNT_SHOW_ALL_INVOICES ] = '0';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_MY_ACCOUNT_LET_SUBSCRIBERS_CANCEL_SUBSCRIPTIONS, $options ) ) {
				$options[ MM_WPFS::OPTION_MY_ACCOUNT_LET_SUBSCRIBERS_CANCEL_SUBSCRIPTIONS ] = '1';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_DECIMAL_SEPARATOR_SYMBOL, $options ) ) {
				$options[ MM_WPFS::OPTION_DECIMAL_SEPARATOR_SYMBOL ] = MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT;
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE, $options ) ) {
				$options[ MM_WPFS::OPTION_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE ] = '1';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION, $options ) ) {
				$options[ MM_WPFS::OPTION_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION ] = '1';
			}
			if ( ! array_key_exists( MM_WPFS::OPTION_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT, $options ) ) {
				$options[ MM_WPFS::OPTION_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT ] = '0';
			}

			MM_WPFS_LicenseManager::getInstance()->setLicenseOptionDefaultsIfEmpty( $options );

			update_option( 'fullstripe_options', $options );
		}
	}

	function fullstripe_set_api_key_and_version( $key ) {
		if ( $key != '' && $key != 'YOUR_TEST_SECRET_KEY' && $key != 'YOUR_LIVE_SECRET_KEY' ) {
			try {
				\StripeWPFS\StripeWPFS::setApiKey( $key );
				\StripeWPFS\StripeWPFS::setApiVersion( MM_WPFS_Stripe::DESIRED_STRIPE_API_VERSION );
			} catch ( Exception $e ) {
				MM_WPFS_Utils::logException( $e, $this );
			}
		}
	}

	function hooks() {

		add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );

		// add_filter( MM_WPFS::FILTER_NAME_GET_VAT_PERCENT, array( $this, 'determine_custom_vat_percent' ), 10, 4 );

		add_shortcode( self::SHORTCODE_FULLSTRIPE_PAYMENT, array( $this, 'fullstripe_payment_form' ) );
		add_shortcode( self::SHORTCODE_FULLSTRIPE_SUBSCRIPTION, array( $this, 'fullstripe_subscription_form' ) );
		add_shortcode( self::SHORTCODE_FULLSTRIPE_CHECKOUT, array( $this, 'fullstripe_checkout_form' ) );
		add_shortcode( self::SHORTCODE_FULLSTRIPE_SUBSCRIPTION_CHECKOUT, array(
			$this,
			'fullstripe_checkout_subscription_form'
		) );
		add_shortcode( self::SHORTCODE_FULLSTRIPE_FORM, array( $this, 'fullstripe_form' ) );

		add_shortcode( self::SHORTCODE_FULLSTRIPE_THANKYOU, array( $this, 'fullstripe_thankyou' ) );
		add_shortcode( self::SHORTCODE_FULLSTRIPE_THANKYOU_SUCCESS, array( $this, 'fullstripe_thankyou_success' ) );
		add_shortcode( self::SHORTCODE_FULLSTRIPE_THANKYOU_DEFAULT, array( $this, 'fullstripe_thankyou_default' ) );

		add_action( 'wp_head', array( $this, 'fullstripe_wp_head' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'fullstripe_enqueue_scripts_and_styles' ) );

		do_action( 'fullstripe_main_hooks_action' );
	}

	public static function getInstance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new MM_WPFS();
		}

		return self::$instance;
	}

	/**
	 * @param $value
	 *
	 * @return mixed
	 */
	public static function esc_html_id_attr( $value ) {
		return preg_replace( '/[^a-z0-9\-_:\.]|^[^a-z]+/i', '', $value );
	}

	public static function get_credit_card_image_for( $currency ) {
		$creditCardImage = 'creditcards.png';

		if ( $currency === MM_WPFS::CURRENCY_USD ) {
			$creditCardImage = 'creditcards-us.png';
		}

		return $creditCardImage;
	}

	/**
	 * Creates an array of locales/languages supported by Stripe Checkout.
	 *
	 * @return array list of locales/languages
	 */
	public static function get_available_checkout_languages() {
		return array(
            array(
                'value' => 'bg',
                'name'  => 'Bulgarian'
            ),
            array(
                'value' => 'cs',
                'name'  => 'Czech'
            ),
			array(
				'value' => 'da',
				'name'  => 'Danish'
			),
			array(
				'value' => 'de',
				'name'  => 'German'
			),
            array(
                'value' => 'el',
                'name'  => 'Greek'
            ),
			array(
				'value' => 'en',
				'name'  => 'English'
			),
            array(
                'value' => 'en-GB',
                'name'  => 'English (United Kingdom)'
            ),
			array(
				'value' => 'es',
				'name'  => 'Spanish (Spain)'
			),
            array(
                'value' => 'es-419',
                'name'  => 'Spanish (Latin America)'
            ),
            array(
                'value' => 'et',
                'name'  => 'Estonian'
            ),
			array(
				'value' => 'fi',
				'name'  => 'Finnish'
			),
            array(
                'value' => 'fr',
                'name'  => 'French (France)'
            ),
			array(
				'value' => 'fr-CA',
				'name'  => 'French (Canada)'
			),
            array(
                'value' => 'hu',
                'name'  => 'Hungarian'
            ),
            array(
                'value' => 'id',
                'name'  => 'Indonesian'
            ),
            array(
                'value' => 'it',
                'name'  => 'Italian'
            ),
            array(
                'value' => 'ja',
                'name'  => 'Japanese'
            ),
			array(
				'value' => 'lv',
				'name'  => 'Lithuanian'
			),
            array(
                'value' => 'lt',
                'name'  => 'Latvian'
            ),
            array(
                'value' => 'ms',
                'name'  => 'Malay'
            ),
            array(
                'value' => 'mt',
                'name'  => 'Maltese'
            ),
			array(
				'value' => 'nb',
				'name'  => 'Norwegian Bokmål'
			),
			array(
				'value' => 'nl',
				'name'  => 'Dutch'
			),
			array(
				'value' => 'pl',
				'name'  => 'Polish'
			),
			array(
				'value' => 'pt',
				'name'  => 'Portuguese'
			),
            array(
                'value' => 'pt-BR',
                'name'  => 'Portuguese (Brazil)'
            ),
            array(
                'value' => 'ro',
                'name'  => 'Romanian'
            ),
            array(
                'value' => 'ru',
                'name'  => 'Russian'
            ),
            array(
                'value' => 'sk',
                'name'  => 'Slovak'
            ),
            array(
                'value' => 'sl',
                'name'  => 'Slovenian'
            ),
			array(
				'value' => 'sv',
				'name'  => 'Swedish'
			),
            array(
                'value' => 'tr',
                'name'  => 'Turkish'
            ),
			array(
				'value' => 'zh',
				'name'  => 'Simplified Chinese'
			)
		);
	}

    /**
     * Creates an array of locales/languages supported by Stripe Elements.
     *
     * @return array list of locales/languages
     */
    public static function get_available_stripe_elements_languages() {
        return array(
            array(
                'value' => 'ar',
                'name'  => 'Arabic'
            ),
            array(
                'value' => 'bg',
                'name'  => 'Bulgarian'
            ),
            array(
                'value' => 'cs',
                'name'  => 'Czech'
            ),
            array(
                'value' => 'da',
                'name'  => 'Danish'
            ),
            array(
                'value' => 'de',
                'name'  => 'German'
            ),
            array(
                'value' => 'el',
                'name'  => 'Greek'
            ),
            array(
                'value' => 'en',
                'name'  => 'English'
            ),
            array(
                'value' => 'en-GB',
                'name'  => 'English (United Kingdom)'
            ),
            array(
                'value' => 'es',
                'name'  => 'Spanish (Spain)'
            ),
            array(
                'value' => 'es-419',
                'name'  => 'Spanish (Latin America)'
            ),
            array(
                'value' => 'et',
                'name'  => 'Estonian'
            ),
            array(
                'value' => 'fi',
                'name'  => 'Finnish'
            ),
            array(
                'value' => 'fr',
                'name'  => 'French (France)'
            ),
            array(
                'value' => 'fr-CA',
                'name'  => 'French (Canada)'
            ),
            array(
                'value' => 'id',
                'name'  => 'Indonesian'
            ),
            array(
                'value' => 'it',
                'name'  => 'Italian'
            ),
            array(
                'value' => 'ja',
                'name'  => 'Japanese'
            ),
            array(
                'value' => 'lv',
                'name'  => 'Lithuanian'
            ),
            array(
                'value' => 'lt',
                'name'  => 'Latvian'
            ),
            array(
                'value' => 'ms',
                'name'  => 'Malay'
            ),
            array(
                'value' => 'nb',
                'name'  => 'Norwegian Bokmål'
            ),
            array(
                'value' => 'nl',
                'name'  => 'Dutch'
            ),
            array(
                'value' => 'pl',
                'name'  => 'Polish'
            ),
            array(
                'value' => 'pt',
                'name'  => 'Portuguese'
            ),
            array(
                'value' => 'pt-BR',
                'name'  => 'Portuguese (Brazil)'
            ),
            array(
                'value' => 'ro',
                'name'  => 'Romanian'
            ),
            array(
                'value' => 'ru',
                'name'  => 'Russian'
            ),
            array(
                'value' => 'sk',
                'name'  => 'Slovak'
            ),
            array(
                'value' => 'sl',
                'name'  => 'Slovenian'
            ),
            array(
                'value' => 'sv',
                'name'  => 'Swedish'
            ),
            array(
                'value' => 'zh',
                'name'  => 'Simplified Chinese'
            )
        );
    }

    public static function get_custom_input_field_max_count() {
		$options = get_option( 'fullstripe_options' );
		if ( is_array( $options ) && array_key_exists( 'custom_input_field_max_count', $options ) ) {
			$customInputFieldMaxCount = $options['custom_input_field_max_count'];
			if ( is_numeric( $customInputFieldMaxCount ) ) {
				return $customInputFieldMaxCount;
			}
		}

		return self::DEFAULT_CUSTOM_INPUT_FIELD_MAX_COUNT;
	}

	/**
	 * Sustain compatibility with WP Full Stripe Members
	 *
	 * @param $currency
	 *
	 * @return mixed|null
	 */
	public static function get_currency_symbol_for( $currency ) {
		return MM_WPFS_Currencies::get_currency_symbol_for( $currency );
	}

	/**
	 * Sustain compatibility with WP Full Stripe Members
	 *
	 * @param $currency
	 *
	 * @return mixed|null
	 */
	public static function get_currency_for( $currency ) {
		return MM_WPFS_Currencies::get_currency_for( $currency );
	}

	public function plugin_action_links( $links, $file ) {
		static $currentPlugin;

		if ( ! $currentPlugin ) {
			$currentPlugin = plugin_basename( 'wp-full-stripe/wp-full-stripe.php' );
		}

		if ( $file == $currentPlugin ) {
			$settingsLabel =
				/* translators: Link label displayed on the Plugins page in WP admin */
				__( 'Settings', 'fullstripe-settings' );
			$settingsLink  = '<a href="' . menu_page_url( 'fullstripe-settings', false ) . '">' . esc_html( $settingsLabel ) . '</a>';
			array_unshift( $links, $settingsLink );
		}

		return $links;
	}

	/**
	 * This is a sample implementation for custom VAT percent calculation
	 *
	 * @param $initialValue
	 * @param $fromCountry
	 * @param $toCountry
	 * @param $additionalArguments
	 *
	 * @return float
	 */
	public function determine_custom_vat_percent( $initialValue, $fromCountry, $toCountry, $additionalArguments ) {
		MM_WPFS_Utils::log( 'determine_custom_vat_percent(): initialValue=' . $initialValue . ', fromCountry=' . $fromCountry . ', toCountry=' . $toCountry . ', additionalArguments=' . print_r( $additionalArguments, true ) );
		// tnagy sample implementation to use the appropriate VAT percent by destination country
		if ( $toCountry == 'GB' ) {
			$vatPercent = 20.0;
		} elseif ( $toCountry == 'DE' ) {
			$vatPercent = 19.0;
		} elseif ( $toCountry == 'CZ' ) {
			$vatPercent = 21.0;
		} elseif ( $toCountry == 'HU' ) {
			$vatPercent = 27.0;
		} elseif ( $toCountry == 'ES' ) {
			$vatPercent = 21.0;
		} else {
			$vatPercent = $initialValue;
		}

		MM_WPFS_Utils::log( 'determine_custom_vat_percent(): vatPercent=' . $vatPercent );

		return $vatPercent;
	}

	/**
	 * Support for old shortcode format
	 *
	 * @param $attributes
	 *
	 * @return mixed|void
	 */
	function fullstripe_payment_form( $attributes ) {

		$curentAttributes = array(
			'type' => self::FORM_TYPE_INLINE_PAYMENT
		);

		if ( array_key_exists( 'form', $attributes ) ) {
			$curentAttributes['name'] = $attributes['form'];
		}

		$content = $this->fullstripe_form( $curentAttributes );

		return apply_filters( 'fullstripe_payment_form_output', $content );
	}

	/**
	 * Generalized function to handle the new shortcode format
	 *
	 * @param $atts
	 *
	 * @return mixed|void
	 */
	function fullstripe_form( $atts ) {

		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'fullstripe_form(): CALLED' );
		}

		$form_type = self::FORM_TYPE_PAYMENT;
		$form_name = 'default';
		if ( array_key_exists( 'type', $atts ) ) {
			$form_type = $atts['type'];
		}
		if ( array_key_exists( 'name', $atts ) ) {
			$form_name = $atts['name'];
		}

		$form = $this->getFormByTypeAndName( $form_type, $form_name );

		ob_start();
		if ( ! is_null( $form ) ) {
			$options           = get_option( 'fullstripe_options' );
			$lock_email        = $options['lock_email_field_for_logged_in_users'];
			$email_address     = '';
			$is_user_logged_in = is_user_logged_in();
			if ( '1' == $lock_email && $is_user_logged_in ) {
				$current_user  = wp_get_current_user();
				$email_address = $current_user->user_email;
			}

			$view = null;
			if ( self::FORM_TYPE_INLINE_PAYMENT === $form_type || self::FORM_TYPE_PAYMENT === $form_type ) {
				$view = new MM_WPFS_InlinePaymentFormView( $form );
				$view->setCurrentEmailAddress( $email_address );
			} elseif ( self::FORM_TYPE_INLINE_SUBSCRIPTION === $form_type || self::FORM_TYPE_SUBSCRIPTION === $form_type ) {
				$stripe_plans = $this->get_plans();
				$view         = new MM_WPFS_InlineSubscriptionFormView( $form, $stripe_plans );
				$view->setCurrentEmailAddress( $email_address );
			} elseif ( self::FORM_TYPE_INLINE_SAVE_CARD === $form_type ) {
				$view = new MM_WPFS_InlineCardCaptureFormView( $form );
				$view->setCurrentEmailAddress( $email_address );
			} elseif ( self::FORM_TYPE_POPUP_PAYMENT === $form_type ) {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$view = new MM_WPFS_PopupPaymentFormView( $form );
			} elseif ( self::FORM_TYPE_POPUP_SUBSCRIPTION === $form_type ) {
				$stripe_plans = $this->get_plans();
				/** @noinspection PhpUnusedLocalVariableInspection */
				$view = new MM_WPFS_PopupSubscriptionFormView( $form, $stripe_plans );
			} elseif ( self::FORM_TYPE_POPUP_SAVE_CARD === $form_type ) {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$view = new MM_WPFS_PopupCardCaptureFormView( $form );
			} elseif ( self::FORM_TYPE_INLINE_DONATION === $form_type ) {
                /** @noinspection PhpUnusedLocalVariableInspection */
                $view = new MM_WPFS_InlineDonationFormView( $form );
            } elseif ( self::FORM_TYPE_POPUP_DONATION === $form_type ) {
                /** @noinspection PhpUnusedLocalVariableInspection */
                $view = new MM_WPFS_PopupDonationFormView( $form );
            }

			$selectedPlanId = null;
			if ( $view instanceof MM_WPFS_SubscriptionFormView ) {
				$isSimpleButtonSubscription = $view instanceof MM_WPFS_PopupSubscriptionFormView && 1 == $form->simpleButtonLayout;
				if ( ! $isSimpleButtonSubscription ) {
					$selectedPlanParamValue = isset( $_GET[ self::HTTP_PARAM_NAME_PLAN ] ) ? sanitize_text_field( $_GET[ self::HTTP_PARAM_NAME_PLAN ] ) : null;
					// $selectedPlanId is used in the view included below
					$selectedPlanId = apply_filters( self::FILTER_NAME_SELECT_SUBSCRIPTION_PLAN, null, $view->getFormName(), $view->getSelectedStripePlanIds(), $selectedPlanParamValue );
				}
			}

			if ( $view instanceof MM_WPFS_PaymentFormView &&
			     MM_WPFS::PAYMENT_TYPE_CUSTOM_AMOUNT == $form->customAmount
			) {
				$customAmountParamValue = isset( $_GET[ self::HTTP_PARAM_NAME_AMOUNT ] ) ? sanitize_text_field( $_GET[ self::HTTP_PARAM_NAME_AMOUNT ] ) : null;

				if ( ! empty( $customAmountParamValue ) ) {
					$customAmount = apply_filters( self::FILTER_NAME_SET_CUSTOM_AMOUNT, 0, $view->getFormName(), $customAmountParamValue );

					if ( $customAmount !== 0 ) {
						$customAmountAttributes                                          = $view->customAmount()->attributes( false );
						$customAmountAttributes[ MM_WPFS_FormViewConstants::ATTR_VALUE ] = MM_WPFS_Currencies::formatByForm( $form, $form->currency, $customAmount, false, false );
						$view->customAmount()->setAttributes( $customAmountAttributes );
					}
				}
			}

			if ( false === $this->loadScriptsAndStylesWithActionHook ) {
				$renderedForms = self::get_rendered_forms()->render_later( $form_type );
				if ( $renderedForms->get_total() == 1 ) {
					$this->fullstripe_load_css();
					$this->fullstripe_load_js();
					$this->fullstripe_set_common_js_variables();
				}
			}

			$popupFormSubmit = null;
			if ( isset( $_GET[ MM_WPFS_CheckoutSubmissionService::STRIPE_CALLBACK_PARAM_WPFS_POPUP_FORM_SUBMIT_HASH ] ) ) {
				$submitHash = $_GET[ MM_WPFS_CheckoutSubmissionService::STRIPE_CALLBACK_PARAM_WPFS_POPUP_FORM_SUBMIT_HASH ];
				/** @noinspection PhpUnusedLocalVariableInspection */
				$popupFormSubmit = $this->checkoutSubmissionService->retrieveSubmitEntry( $submitHash );
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( 'fullstripe_form(): popupFormSubmit=' . print_r( $popupFormSubmit, true ) );
				}

				if ( isset( $popupFormSubmit ) && $popupFormSubmit->formHash === $view->getFormHash() ) {
					if (
						MM_WPFS_CheckoutSubmissionService::POPUP_FORM_SUBMIT_STATUS_CREATED === $popupFormSubmit->status
						|| MM_WPFS_CheckoutSubmissionService::POPUP_FORM_SUBMIT_STATUS_PENDING === $popupFormSubmit->status
						|| MM_WPFS_CheckoutSubmissionService::POPUP_FORM_SUBMIT_STATUS_COMPLETE === $popupFormSubmit->status
					) {
						// tnagy we do not render messages for created/complete submissions
						$popupFormSubmit = null;
					} else {
						// tnagy we set the form submission to complete, the last message will be shown when the shortcode renders
						$this->checkoutSubmissionService->updateSubmitEntryWithComplete( $popupFormSubmit );
					}
				}
			}

			/** @noinspection PhpIncludeInspection */
			include MM_WPFS_Assets::templates( 'forms/fullstripe_form.php' );
		} else {
			include MM_WPFS_Assets::templates( 'forms/form_not_found.php' );
		}

		$content = ob_get_clean();

		return apply_filters( 'fullstripe_form_output', $content );
	}

	/**
	 * Returns a form from database identified by type and name.
	 *
	 * @param $formType
	 * @param $formName
	 *
	 * @return mixed|null
	 */
	function getFormByTypeAndName( $formType, $formName ) {
		$form = null;

		if ( self::FORM_TYPE_INLINE_PAYMENT === $formType || self::FORM_TYPE_PAYMENT === $formType ) {
			$form = $this->database->getPaymentFormByName( $formName );
		} elseif ( self::FORM_TYPE_INLINE_SUBSCRIPTION === $formType || self::FORM_TYPE_SUBSCRIPTION === $formType ) {
			$form = $this->database->getSubscriptionFormByName( $formName );
		} elseif ( self::FORM_TYPE_INLINE_SAVE_CARD === $formType ) {
			$form = $this->database->getPaymentFormByName( $formName );
		} elseif ( self::FORM_TYPE_POPUP_PAYMENT === $formType ) {
			$form = $this->database->getCheckoutFormByName( $formName );
		} elseif ( self::FORM_TYPE_POPUP_SUBSCRIPTION === $formType ) {
			$form = $this->database->getCheckoutSubscriptionFormByName( $formName );
		} elseif ( self::FORM_TYPE_POPUP_SAVE_CARD === $formType ) {
			$form = $this->database->getCheckoutFormByName( $formName );
		} elseif ( self::FORM_TYPE_INLINE_DONATION === $formType ) {
            $form = $this->database->getInlineDonationFormByName( $formName );
        } elseif ( self::FORM_TYPE_POPUP_DONATION === $formType ) {
            $form = $this->database->getCheckoutDonationFormByName( $formName );
        }

		return $form;
	}

	/**
	 * @return array|mixed|void
	 */
	public function get_plans() {
		return $this->stripe != null ? apply_filters( 'fullstripe_subscription_plans_filter', $this->stripe->get_plans() ) : array();
	}

	/**
	 * @return WPFS_RenderedFormData
	 */
	public static function get_rendered_forms() {
		if ( ! array_key_exists( self::REQUEST_PARAM_NAME_WPFS_RENDERED_FORMS, $_REQUEST ) ) {
			$_REQUEST[ self::REQUEST_PARAM_NAME_WPFS_RENDERED_FORMS ] = new WPFS_RenderedFormData();
		}

		return $_REQUEST[ self::REQUEST_PARAM_NAME_WPFS_RENDERED_FORMS ];
	}

	/**
	 * Register and enqueue WPFS styles
	 */
	public function fullstripe_load_css() {
		$options = get_option( 'fullstripe_options' );
		if ( $options['includeStyles'] === '1' ) {

			wp_register_style( self::HANDLE_STYLE_WPFS_VARIABLES, MM_WPFS_Assets::css( 'wpfs-variables.css' ), null, MM_WPFS::VERSION );
			wp_register_style( self::HANDLE_STYLE_WPFS_FORMS, MM_WPFS_Assets::css( 'wpfs-forms.css' ), array( self::HANDLE_STYLE_WPFS_VARIABLES ), MM_WPFS::VERSION );

			wp_enqueue_style( self::HANDLE_STYLE_WPFS_FORMS );
		}

		do_action( 'fullstripe_load_css_action' );
	}

	/**
	 * Register and enqueue WPFS scripts
	 */
	public function fullstripe_load_js() {
		$source = add_query_arg(
			array(
				'render' => 'explicit'
			),
			self::SOURCE_GOOGLE_RECAPTCHA_V2_API_JS
		);
		wp_register_script( self::HANDLE_GOOGLE_RECAPTCHA_V_2, $source, null, MM_WPFS::VERSION, true /* in footer */ );
		wp_register_script( self::HANDLE_SPRINTF_JS, MM_WPFS_Assets::scripts( 'sprintf.min.js' ), null, MM_WPFS::VERSION );
		wp_register_script( self::HANDLE_STRIPE_JS_V_3, 'https://js.stripe.com/v3/', array( 'jquery' ) );
		wp_register_script( self::HANDLE_WP_FULL_STRIPE_UTILS_JS, MM_WPFS_Assets::scripts( 'wpfs-utils.js' ), null, MM_WPFS::VERSION );

		wp_enqueue_script( self::HANDLE_SPRINTF_JS );
		wp_enqueue_script( self::HANDLE_STRIPE_JS_V_3 );
		wp_enqueue_script( self::HANDLE_WP_FULL_STRIPE_UTILS_JS );
		if (
			MM_WPFS_Utils::get_secure_inline_forms_with_google_recaptcha()
			|| MM_WPFS_Utils::get_secure_checkout_forms_with_google_recaptcha()
		) {
			$dependencies = array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-selectmenu',
				'jquery-ui-autocomplete',
				'jquery-ui-tooltip',
				'jquery-ui-spinner',
				self::HANDLE_SPRINTF_JS,
				self::HANDLE_WP_FULL_STRIPE_UTILS_JS,
				self::HANDLE_STRIPE_JS_V_3,
				self::HANDLE_GOOGLE_RECAPTCHA_V_2
			);
		} else {
			$dependencies = array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-selectmenu',
				'jquery-ui-autocomplete',
				'jquery-ui-tooltip',
				'jquery-ui-spinner',
				self::HANDLE_SPRINTF_JS,
				self::HANDLE_WP_FULL_STRIPE_UTILS_JS,
				self::HANDLE_STRIPE_JS_V_3
			);
		}
		wp_enqueue_script( self::HANDLE_WP_FULL_STRIPE_JS, MM_WPFS_Assets::scripts( 'wpfs.js' ), $dependencies, MM_WPFS::VERSION );

		do_action( 'fullstripe_load_js_action' );
	}

	function fullstripe_set_common_js_variables() {
		wp_localize_script( self::HANDLE_WP_FULL_STRIPE_JS, self::JS_VARIABLE_AJAX_URL, admin_url( 'admin-ajax.php' ) );
		$options = get_option( 'fullstripe_options' );
		if ( $options['apiMode'] === 'test' ) {
			wp_localize_script( self::HANDLE_WP_FULL_STRIPE_JS, self::JS_VARIABLE_STRIPE_KEY, $options['publishKey_test'] );
		} else {
			wp_localize_script( self::HANDLE_WP_FULL_STRIPE_JS, self::JS_VARIABLE_STRIPE_KEY, $options['publishKey_live'] );
		}
		wp_localize_script( self::HANDLE_WP_FULL_STRIPE_JS, self::JS_VARIABLE_GOOGLE_RECAPTCHA_SITE_KEY, MM_WPFS_Utils::get_google_recaptcha_site_key() );
		wp_localize_script( self::HANDLE_WP_FULL_STRIPE_JS, self::JS_VARIABLE_L10N, array(
				'validation_errors'                      => array(
					'internal_error'                         =>
					/* translators: Banner message of internal error when no error message is returned by the application */
						__( 'An internal error occurred.', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'internal_error_title'                   =>
					/* translators: Banner title of internal error */
						__( 'Internal Error', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'mandatory_field_is_empty'               =>
					/* translators: Error message for required fields when empty.
					 * p1: custom input field label
					 */
						__( "Please enter a value for '%s'", MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'custom_payment_amount_value_is_invalid' =>
					/* translators: Field validation error message when payment amount is empty or invalid */
						__( 'Payment amount is invalid', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'invalid_payment_amount'                 =>
					/* translators: Banner message when the payment amount cannot be determined (the form has been tampered with) */
						__( 'Cannot determine payment amount', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'invalid_payment_amount_title'           =>
					/* translators: Banner title when the payment amount cannot be determined (the form has been tampered with) */
						__( 'Invalid payment amount', MM_WPFS::L10N_DOMAIN_PUBLIC )
				),
				'stripe_errors'                          => array(
					MM_WPFS_Stripe::INVALID_NUMBER_ERROR               => $this->stripe->resolve_error_message_by_code( MM_WPFS_Stripe::INVALID_NUMBER_ERROR ),
					MM_WPFS_Stripe::INVALID_NUMBER_ERROR_EXP_MONTH     => $this->stripe->resolve_error_message_by_code( MM_WPFS_Stripe::INVALID_NUMBER_ERROR_EXP_MONTH ),
					MM_WPFS_Stripe::INVALID_NUMBER_ERROR_EXP_YEAR      => $this->stripe->resolve_error_message_by_code( MM_WPFS_Stripe::INVALID_NUMBER_ERROR_EXP_YEAR ),
					MM_WPFS_Stripe::INVALID_EXPIRY_MONTH_ERROR         => $this->stripe->resolve_error_message_by_code( MM_WPFS_Stripe::INVALID_EXPIRY_MONTH_ERROR ),
					MM_WPFS_Stripe::INVALID_EXPIRY_YEAR_ERROR          => $this->stripe->resolve_error_message_by_code( MM_WPFS_Stripe::INVALID_EXPIRY_YEAR_ERROR ),
					MM_WPFS_Stripe::INVALID_CVC_ERROR                  => $this->stripe->resolve_error_message_by_code( MM_WPFS_Stripe::INVALID_CVC_ERROR ),
					MM_WPFS_Stripe::INCORRECT_NUMBER_ERROR             => $this->stripe->resolve_error_message_by_code( MM_WPFS_Stripe::INCORRECT_NUMBER_ERROR ),
					MM_WPFS_Stripe::EXPIRED_CARD_ERROR                 => $this->stripe->resolve_error_message_by_code( MM_WPFS_Stripe::EXPIRED_CARD_ERROR ),
					MM_WPFS_Stripe::INCORRECT_CVC_ERROR                => $this->stripe->resolve_error_message_by_code( MM_WPFS_Stripe::INCORRECT_CVC_ERROR ),
					MM_WPFS_Stripe::INCORRECT_ZIP_ERROR                => $this->stripe->resolve_error_message_by_code( MM_WPFS_Stripe::INCORRECT_ZIP_ERROR ),
					MM_WPFS_Stripe::CARD_DECLINED_ERROR                => $this->stripe->resolve_error_message_by_code( MM_WPFS_Stripe::CARD_DECLINED_ERROR ),
					MM_WPFS_Stripe::MISSING_ERROR                      => $this->stripe->resolve_error_message_by_code( MM_WPFS_Stripe::MISSING_ERROR ),
					MM_WPFS_Stripe::PROCESSING_ERROR                   => $this->stripe->resolve_error_message_by_code( MM_WPFS_Stripe::PROCESSING_ERROR ),
					MM_WPFS_Stripe::MISSING_PAYMENT_INFORMATION        => $this->stripe->resolve_error_message_by_code( MM_WPFS_Stripe::MISSING_PAYMENT_INFORMATION ),
					MM_WPFS_Stripe::COULD_NOT_FIND_PAYMENT_INFORMATION => $this->stripe->resolve_error_message_by_code( MM_WPFS_Stripe::COULD_NOT_FIND_PAYMENT_INFORMATION )
				),
				'subscription_charge_interval_templates' => array(
					'daily'            => __( 'Subscription will be charged every day.', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'weekly'           => __( 'Subscription will be charged every week.', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'monthly'          => __( 'Subscription will be charged every month.', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'yearly'           => __( 'Subscription will be charged every year.', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'y_days'           => __( 'Subscription will be charged every %d days.', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'y_weeks'          => __( 'Subscription will be charged every %d weeks.', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'y_months'         => __( 'Subscription will be charged every %d months.', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'y_years'          => __( 'Subscription will be charged every %d years.', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'x_times_daily'    => __( 'Subscription will be charged every day, for %d occasions.', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'x_times_weekly'   => __( 'Subscription will be charged every week, for %d occasions.', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'x_times_monthly'  => __( 'Subscription will be charged every month, for %d occasions.', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'x_times_yearly'   => __( 'Subscription will be charged every year, for %d occasions.', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'x_times_y_days'   => __( 'Subscription will be charged every %1$d days, for %2$d occasions.', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'x_times_y_weeks'  => __( 'Subscription will be charged every %1$d weeks, for %2$d occasions.', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'x_times_y_months' => __( 'Subscription will be charged every %1$d months, for %2$d occasions.', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'x_times_y_years'  => __( 'Subscription will be charged every %1$d years, for %2$d occasions.', MM_WPFS::L10N_DOMAIN_PUBLIC ),
				)
			)
		);
		wp_localize_script( self::HANDLE_WP_FULL_STRIPE_JS, self::JS_VARIABLE_FORM_FIELDS, array(
				'inlinePayment'      => MM_WPFS_InlinePaymentFormView::getFields(),
				'inlineCardCapture'  => MM_WPFS_InlineCardCaptureFormView::getFields(),
				'inlineSubscription' => MM_WPFS_InlineSubscriptionFormView::getFields(),
                'inlineDonation'     => MM_WPFS_InlineDonationFormView::getFields(),
				'popupPayment'       => MM_WPFS_PopupPaymentFormView::getFields(),
				'popupCardCapture'   => MM_WPFS_PopupCardCaptureFormView::getFields(),
				'popupSubscription'  => MM_WPFS_PopupSubscriptionFormView::getFields(),
                'popupDonation'      => MM_WPFS_PopupDonationFormView::getFields(),
			)
		);

	}

	/**
	 * @param $subscriptionId
	 * @param $planId
	 * @param $chargeMaxCount
	 *
	 * @return bool|int
	 * @throws Exception
	 */
	public function updateSubscriptionPlanAndCounters( $subscriptionId, $planId, $chargeMaxCount ) {
		return $this->database->updateSubscriptionPlanAndCounters( $subscriptionId, $planId, $chargeMaxCount );
	}

	/**
	 * Support for old shortcode format
	 *
	 * @param $attributes
	 *
	 * @return mixed|void
	 */
	function fullstripe_subscription_form( $attributes ) {

		$currentAttributes = array(
			'type' => self::FORM_TYPE_INLINE_SUBSCRIPTION
		);

		if ( array_key_exists( 'form', $attributes ) ) {
			$currentAttributes['name'] = $attributes['form'];
		}

		$content = $this->fullstripe_form( $currentAttributes );

		return apply_filters( 'fullstripe_subscription_form_output', $content );
	}

	/**
	 * Support for old shortcode format
	 *
	 * @param $attributes
	 *
	 * @return mixed|void
	 */
	function fullstripe_checkout_form( $attributes ) {

		$currentAttributes = array(
			'type' => self::FORM_TYPE_POPUP_PAYMENT
		);

		if ( array_key_exists( 'form', $attributes ) ) {
			$currentAttributes['name'] = $attributes['form'];
		}

		$content = $this->fullstripe_form( $currentAttributes );

		return apply_filters( 'fullstripe_checkout_form_output', $content );
	}

	/**
	 * Support for old shortcode format
	 *
	 * @param $attributes
	 *
	 * @return mixed|void
	 */
	function fullstripe_checkout_subscription_form( $attributes ) {

		$currentAttributes = array(
			'type' => self::FORM_TYPE_POPUP_SUBSCRIPTION
		);

		if ( array_key_exists( 'form', $attributes ) ) {
			$currentAttributes['name'] = $attributes['form'];
		}

		$content = $this->fullstripe_form( $currentAttributes );

		return apply_filters( 'fullstripe_checkout_subscription_form_output', $content );
	}

	function fullstripe_thankyou( $attributes, $content = null ) {
		$transactionDataKey = isset( $_REQUEST[ MM_WPFS_TransactionDataService::REQUEST_PARAM_NAME_WPFS_TRANSACTION_DATA_KEY ] ) ? $_REQUEST[ MM_WPFS_TransactionDataService::REQUEST_PARAM_NAME_WPFS_TRANSACTION_DATA_KEY ] : null;
		$transactionData    = $this->transactionDataService->retrieve( $transactionDataKey );

		if ( $transactionData !== false ) {
			$_REQUEST['transaction_data'] = $transactionData;
		}

		return do_shortcode( $content );
	}

	function fullstripe_thankyou_default( $attributes, $content = null ) {
		if ( isset( $_REQUEST['transaction_data'] ) ) {
			return '';
		} else {
			return $content;
		}
	}

	function fullstripe_thankyou_success( $attributes, $content = null ) {
		if ( isset( $_REQUEST['transaction_data'] ) ) {
			$transactionData = $_REQUEST['transaction_data'];
		} else {
			$transactionData = null;
		}

		if ( ! is_null( $transactionData ) && $transactionData instanceof MM_WPFS_TransactionData ) {

            if ($transactionData instanceof MM_WPFS_SubscriptionTransactionData) {
                /** @var $transactionData MM_WPFS_SubscriptionTransactionData */
                $search = MM_WPFS_Utils::get_subscription_macros();
                $replace = MM_WPFS_Utils::getSubscriptionMacroValues($this->getFormByName($transactionData->getFormName()), $transactionData);

                $result = str_replace(
                    $search,
                    $replace,
                    $content
                );

                $result = MM_WPFS_Utils::replace_custom_fields($result, $transactionData->getCustomInputValues());

            } else if ( $transactionData instanceof MM_WPFS_DonationTransactionData ) {
                /** @var $transactionData MM_WPFS_SubscriptionTransactionData */
                $donationForm = $this->getFormByName( $transactionData->getFormName() );

                $replacer = new DonationMacroReplacer( $donationForm, $transactionData );
                $result = $replacer->replaceMacrosWithHtmlEscape( $content );

            } else {
				/** @var $transactionData MM_WPFS_PaymentTransactionData */
				$search  = MM_WPFS_Utils::get_payment_macros();
				$replace = MM_WPFS_Utils::getPaymentMacroValues( $this->getFormByName( $transactionData->getFormName() ), $transactionData );

				$result = str_replace(
					$search,
					$replace,
					$content
				);

				$result = MM_WPFS_Utils::replace_custom_fields( $result, $transactionData->getCustomInputValues() );

			}

			return $result;
		} else {
			return '';
		}
	}

	/**
	 * Returns a form from database identified by a name.
	 *
	 * @param $formName
	 *
	 * @return mixed|null
	 */
	function getFormByName( $formName ) {
		$form = null;

		if ( is_null( $form ) ) {
			$form = $this->database->getPaymentFormByName( $formName );
		}
		if ( is_null( $form ) ) {
			$form = $this->database->getSubscriptionFormByName( $formName );
		}
		if ( is_null( $form ) ) {
			$form = $this->database->getCheckoutFormByName( $formName );
		}
		if ( is_null( $form ) ) {
			$form = $this->database->getCheckoutSubscriptionFormByName( $formName );
		}
        if ( is_null( $form ) ) {
            $form = $this->database->getInlineDonationFormByName( $formName );
        }
        if ( is_null( $form ) ) {
            $form = $this->database->getCheckoutDonationFormByName( $formName );
        }

		return $form;
	}

	function fullstripe_wp_head() {
		//output the custom css
		$options = get_option( 'fullstripe_options' );
		echo '<style type="text/css" media="screen">' . $options['form_css'] . '</style>';
	}

	/**
	 * Register and enqueue styles and scripts to load for this addon
	 */
	public function fullstripe_enqueue_scripts_and_styles() {
		if ( $this->loadScriptsAndStylesWithActionHook ) {
			global $wp;
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'fullstripe_enqueue_scripts_and_styles(): CALLED, wp=' . print_r( $wp, true ) );
			}
			if ( ! is_null( $wp ) && isset( $wp->request ) ) {
				$pageByPath = get_page_by_path( $wp->request );
				if ( ! is_null( $pageByPath ) && isset( $pageByPath->post_content ) ) {
					if (
						has_shortcode( $pageByPath->post_content, self::SHORTCODE_FULLSTRIPE_FORM )
						|| has_shortcode( $pageByPath->post_content, self::SHORTCODE_FULLSTRIPE_CHECKOUT )
						|| has_shortcode( $pageByPath->post_content, self::SHORTCODE_FULLSTRIPE_PAYMENT )
						|| has_shortcode( $pageByPath->post_content, self::SHORTCODE_FULLSTRIPE_SUBSCRIPTION )
						|| has_shortcode( $pageByPath->post_content, self::SHORTCODE_FULLSTRIPE_SUBSCRIPTION_CHECKOUT )
					) {
						$this->fullstripe_load_css();
						$this->fullstripe_load_js();
						$this->fullstripe_set_common_js_variables();
					}
				}
			}
		}
	}

	public function get_recipients() {
		return $this->stripe != null ? apply_filters( 'fullstripe_transfer_receipients_filter', $this->stripe->get_recipients() ) : array();
	}

	public function get_subscription( $customerID, $subscriptionID ) {
		return $this->stripe != null ? apply_filters( 'fullstripe_customer_subscription_filter', $this->stripe->retrieve_subscription( $customerID, $subscriptionID ) ) : array();
	}

	/**
	 * @return MM_WPFS_Admin_Menu
	 */
	public function getAdminMenu() {
		return $this->adminMenu;
	}

	/**
	 * @return MM_WPFS_Admin
	 */
	public function getAdmin() {
		return $this->admin;
	}

	public function get_form_validation_data() {
		return new WPFS_FormValidationData();
	}

	public function get_plan( $plan_id ) {
		return $this->stripe != null ? apply_filters( 'fullstripe_subscription_plan_filter', $this->stripe->retrieve_plan( $plan_id ) ) : null;
	}

}

class WPFS_FormValidationData {

	const NAME_LENGTH = 100;
	const FORM_TITLE_LENGTH = 100;
	const BUTTON_TITLE_LENGTH = 100;
	const REDIRECT_URL_LENGTH = 1024;
	const COMPANY_NAME_LENGTH = 100;
	const PRODUCT_DESCRIPTION_LENGTH = 100;
	const OPEN_BUTTON_TITLE_LENGTH = 100;
	const PAYMENT_AMOUNT_LENGTH = 8;
	const PAYMENT_AMOUNT_DESCRIPTION_LENGTH = 128;
	const IMAGE_LENGTH = 500;

}

class WPFS_PlanValidationData {
	const STATEMENT_DESCRIPTOR_LENGTH = 22;
}

class WPFS_RenderedFormData {

	private $payments = 0;
	private $subscriptions = 0;
	private $checkouts = 0;
	private $checkoutSubscriptions = 0;
	private $donations = 0;
    private $checkoutDonations = 0;

	public function render_later( $type ) {
		if ( MM_WPFS::FORM_TYPE_PAYMENT === $type ) {
			// todo tnagy remove later
			$this->payments += 1;
		} elseif ( MM_WPFS::FORM_TYPE_SUBSCRIPTION === $type ) {
			// todo tnagy remove later
			$this->subscriptions += 1;
		} elseif ( MM_WPFS::FORM_TYPE_CHECKOUT === $type ) {
			// todo tnagy remove later
			$this->checkouts += 1;
		} elseif ( MM_WPFS::FORM_TYPE_CHECKOUT_SUBSCRIPTION === $type ) {
			// todo tnagy remove later
			$this->checkoutSubscriptions += 1;
		} elseif ( MM_WPFS::FORM_TYPE_INLINE_PAYMENT === $type ) {
			$this->payments += 1;
		} elseif ( MM_WPFS::FORM_TYPE_INLINE_SAVE_CARD === $type ) {
			$this->payments += 1;
		} elseif ( MM_WPFS::FORM_TYPE_INLINE_SUBSCRIPTION === $type ) {
			$this->subscriptions += 1;
		} elseif ( MM_WPFS::FORM_TYPE_INLINE_DONATION === $type ) {
            $this->donations += 1;
        } elseif ( MM_WPFS::FORM_TYPE_POPUP_PAYMENT === $type ) {
			$this->checkouts += 1;
		} elseif ( MM_WPFS::FORM_TYPE_POPUP_SAVE_CARD === $type ) {
			$this->checkouts += 1;
		} elseif ( MM_WPFS::FORM_TYPE_POPUP_SUBSCRIPTION === $type ) {
			$this->checkoutSubscriptions += 1;
		} elseif ( MM_WPFS::FORM_TYPE_POPUP_DONATION === $type ) {
            $this->checkoutDonations += 1;
        }

		return $this;
	}

	/**
	 * @return int
	 */
	public function get_payments() {
		return $this->payments;
	}

	/**
	 * @return int
	 */
	public function get_subscriptions() {
		return $this->subscriptions;
	}

	/**
	 * @return int
	 */
	public function get_checkouts() {
		return $this->checkouts;
	}

	/**
	 * @return int
	 */
	public function get_checkout_subscriptions() {
		return $this->checkoutSubscriptions;
	}

    /**
     * @return int
     */
    public function get_donations() {
        return $this->donations;
    }

    /**
     * @return int
     */
    public function get_checkout_donations() {
        return $this->checkoutDonations;
    }

    /**
	 * @return int
	 */
	public function get_total() {
		return $this->payments + $this->subscriptions + $this->checkouts + $this->checkoutSubscriptions + $this->donations + $this->checkoutDonations;
	}

}

class MM_WPFS_Utils {

	const ADDITIONAL_DATA_KEY_ACTION_NAME = 'action_name';
	const ADDITIONAL_DATA_KEY_CUSTOMER = 'customer';
	const ADDITIONAL_DATA_KEY_MACROS = 'macros';
	const ADDITIONAL_DATA_KEY_MACRO_VALUES = 'macroValues';
	const WPFS_LOG_MESSAGE_PREFIX = "WPFS: ";
	const STRIPE_METADATA_KEY_MAX_LENGTH = 40;
	const STRIPE_METADATA_VALUE_MAX_LENGTH = 500;
	const STRIPE_METADATA_KEY_MAX_COUNT = 20;
	const ELEMENT_PART_SEPARATOR = '--';
	const SHORTCODE_PATTERN = '[fullstripe_form name="%s" type="%s"]';

	const ESCAPE_TYPE_NONE = 'none';
	const ESCAPE_TYPE_HTML = 'esc_html';
	const ESCAPE_TYPE_ATTR = 'esc_attr';
	const WPFS_ENCRYPT_METHOD_AES_256_CBC = 'AES-256-CBC';

	public static function extractFirstTierPricingFromPlan( $plan ) {
        return $plan->tiers[0]['unit_amount'];
    }

	public static function formatPlanAmountForPlanList( $plan ) {
        $amountStr = '';

	    if ( $plan->billing_scheme == 'tiered' ) {
            $formattedAmount = MM_WPFS_Currencies::formatAndEscape( $plan->currency, MM_WPFS_Utils::extractFirstTierPricingFromPlan( $plan ) );
            $amountStr = sprintf(__( "Starting at %s", MM_WPFS::L10N_DOMAIN_ADMIN ), $formattedAmount );
        } else {
            $amountStr = MM_WPFS_Currencies::formatAndEscape( $plan->currency, $plan->amount );
        }

        return $amountStr;
    }

	/**
	 * @param MM_WPFS_Public_FormModel $formModel
	 *
	 * @return bool
	 */
	public static function isSendingPluginEmail( $formModel ) {
	    //todo: this is fucked up
		$sendPluginEmail = true;
		$options         = get_option( 'fullstripe_options' );
		if ( 'stripe' == $options['receiptEmailType'] && 1 == $formModel->getForm()->sendEmailReceipt ) {
			$sendPluginEmail = false;

			return $sendPluginEmail;
		}

		return $sendPluginEmail;
	}

	public static function isSendingPluginEmailByForm( $form ) {
		$sendReceipt = false;

		$formSendReceipt = $form->sendEmailReceipt == 1 ? true : false;
		if ( $formSendReceipt ) {
			$options         = get_option( 'fullstripe_options' );
			$sendPluginEmail = false;
			if ( $options['receiptEmailType'] == 'plugin' ) {
				$sendPluginEmail = true;
			}
			$sendReceipt = $formSendReceipt && $sendPluginEmail;
		}

		return $sendReceipt;
	}

	/**
	 * @return bool
	 */
	public static function generateCSSFormID( $form_hash ) {
		return MM_WPFS_FormView::ATTR_ID_VALUE_PREFIX . $form_hash;
	}


	/**
	 * @return bool
	 */
	public static function isDemoMode() {
		return defined( 'WP_FULL_STRIPE_DEMO_MODE' );
	}


	/**
	 * @param $form
	 *
	 * @return bool|string
	 */
	public static function createShortCodeString( $form ) {
		$formType = MM_WPFS_Utils::getFormType( $form );
		if ( MM_WPFS::FORM_TYPE_INLINE_PAYMENT === $formType ) {
			return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_INLINE_PAYMENT );
		} elseif ( MM_WPFS::FORM_TYPE_INLINE_SAVE_CARD === $formType ) {
			return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_INLINE_SAVE_CARD );
		} elseif ( MM_WPFS::FORM_TYPE_INLINE_SUBSCRIPTION === $formType ) {
			return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_INLINE_SUBSCRIPTION );
		} elseif ( MM_WPFS::FORM_TYPE_INLINE_DONATION === $formType ) {
            return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_INLINE_DONATION );
        } elseif ( MM_WPFS::FORM_TYPE_POPUP_PAYMENT === $formType ) {
			return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_POPUP_PAYMENT );
		} elseif ( MM_WPFS::FORM_TYPE_POPUP_SAVE_CARD === $formType ) {
			return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_POPUP_SAVE_CARD );
		} elseif ( MM_WPFS::FORM_TYPE_POPUP_SUBSCRIPTION === $formType ) {
			return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_POPUP_SUBSCRIPTION );
		} elseif ( MM_WPFS::FORM_TYPE_POPUP_DONATION === $formType ) {
            return sprintf( self::SHORTCODE_PATTERN, $form->name, MM_WPFS::FORM_TYPE_POPUP_DONATION );
        }

		return false;
	}

	/**
	 * @param $form
	 *
	 * @return null|string
	 */
	public static function getFormType( $form ) {
		if ( is_null( $form ) ) {
			return null;
		}
		if ( isset( $form->paymentFormID ) ) {
			if ( MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE === $form->customAmount ) {
				return MM_WPFS::FORM_TYPE_INLINE_SAVE_CARD;
			} else {
				return MM_WPFS::FORM_TYPE_INLINE_PAYMENT;
			}
		}
		if ( isset( $form->subscriptionFormID ) ) {
			return MM_WPFS::FORM_TYPE_INLINE_SUBSCRIPTION;
		}
		if ( isset( $form->checkoutFormID ) ) {
			if ( MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE === $form->customAmount ) {
				return MM_WPFS::FORM_TYPE_POPUP_SAVE_CARD;
			} else {
				return MM_WPFS::FORM_TYPE_POPUP_PAYMENT;
			}
		}
		if ( isset( $form->checkoutSubscriptionFormID ) ) {
			return MM_WPFS::FORM_TYPE_POPUP_SUBSCRIPTION;
		}

        if ( isset( $form->donationFormID ) ) {
            return MM_WPFS::FORM_TYPE_INLINE_DONATION;
        }
        if ( isset( $form->checkoutDonationFormID ) ) {
            return MM_WPFS::FORM_TYPE_POPUP_DONATION;
        }

        return null;
	}

	public static function generate_form_element_id( $element_id, $form_hash, $index = null ) {
		if ( is_null( $element_id ) ) {
			return null;
		}

		$generated_id = $element_id . MM_WPFS_Utils::ELEMENT_PART_SEPARATOR . $form_hash;
		if ( ! is_null( $index ) ) {
			$generated_id .= MM_WPFS_Utils::ELEMENT_PART_SEPARATOR . $index;
		}

		return esc_attr( $generated_id );
	}

	public static function generate_form_hash( $form_type, $form_id, $form_name ) {
		$data = $form_type . '|' . $form_id . '|' . $form_name;

		return substr( base64_encode( hash( 'sha256', $data ) ), 0, 7 );
	}

	public static function sanitize_text( $value ) {
		return self::stripslashes_deep( sanitize_text_field( $value ) );
	}

	public static function stripslashes_deep( $value ) {
		$value = is_array( $value ) ?
			array_map( 'MM_WPFS_Utils::stripslashes_deep', $value ) :
			stripslashes( $value );

		return $value;
	}

	public static function add_http_prefix( $url ) {
		if ( ! isset( $url ) ) {
			return null;
		}
		if ( substr( $url, 0, 7 ) != 'http://' && substr( $url, 0, 8 ) != 'https://' ) {
			return 'http://' . $url;
		}

		return sanitize_text_field( $url );
	}

	public static function get_card_update_confirmation_request_macros() {
		return array(
			'%CUSTOMERNAME%',
			'%CUSTOMER_EMAIL%',
			'%CARD_UPDATE_SECURITY_CODE%',
			'%CARD_UPDATE_SESSION_HASH%',
			'%NAME%',
			'%DATE%'
		);
	}

	/**
	 * @param $customerName
	 * @param $customerEmail
	 * @param $cardUpdateSessionHash
	 * @param $securityCode
	 *
	 * @return array
	 */
	public static function get_card_update_confirmation_request_macro_values( $customerName, $customerEmail, $cardUpdateSessionHash, $securityCode ) {
		$siteTitle  = get_bloginfo( 'name' );
		$dateFormat = get_option( 'date_format' );

		return array(
			esc_attr( $customerName ),
			esc_attr( $customerEmail ),
			esc_attr( $securityCode ),
			esc_attr( $cardUpdateSessionHash ),
			esc_attr( $siteTitle ),
			esc_attr( date( $dateFormat ) )
		);
	}

	/**
	 * @param $googleReCAPTCHAResponse
	 *
	 * @return array|bool|mixed|object|WP_Error
	 */
	public static function verifyReCAPTCHA( $googleReCAPTCHAResponse ) {
		$googleReCAPTCHASecretKey = MM_WPFS_Utils::get_google_recaptcha_secret_key();

		if ( ! is_null( $googleReCAPTCHASecretKey ) && ! is_null( $googleReCAPTCHAResponse ) ) {
			$inputArray = array(
				'secret'   => $googleReCAPTCHASecretKey,
				'response' => $googleReCAPTCHAResponse,
				'remoteip' => $_SERVER['REMOTE_ADDR']
			);
			$request    = wp_remote_post(
				MM_WPFS::URL_RECAPTCHA_API_SITEVERIFY,
				array(
					'timeout'   => 10,
					'sslverify' => true,
					'body'      => $inputArray
				)
			);
			if ( ! is_wp_error( $request ) ) {
				$request = json_decode( wp_remote_retrieve_body( $request ) );

				return $request;
			} else {
				return false;
			}
		}

		return false;
	}

	public static function get_google_recaptcha_secret_key() {
		$googleReCAPTCHASecretKey = null;
		$options                  = get_option( 'fullstripe_options' );
		if ( array_key_exists( MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SECRET_KEY, $options ) ) {
			$googleReCAPTCHASecretKey = $options[ MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SECRET_KEY ];
		}

		return $googleReCAPTCHASecretKey;
	}

	/**
	 * @return array
	 */
	public static function get_subscription_macros() {
		return array(
			'%SETUP_FEE%',
			'%SETUP_FEE_NET%',
			'%SETUP_FEE_GROSS%',
			'%SETUP_FEE_VAT%',
			'%SETUP_FEE_VAT_RATE%',
			'%SETUP_FEE_TOTAL%',
			'%SETUP_FEE_NET_TOTAL%',
			'%SETUP_FEE_GROSS_TOTAL%',
			'%SETUP_FEE_VAT_TOTAL%',
			'%PLAN_NAME%',
			'%PLAN_AMOUNT%',
			'%PLAN_AMOUNT_NET%',
			'%PLAN_AMOUNT_GROSS%',
			'%PLAN_AMOUNT_VAT%',
			'%PLAN_AMOUNT_VAT_RATE%',
			'%PLAN_QUANTITY%',
			'%PLAN_AMOUNT_TOTAL%',
			'%PLAN_AMOUNT_NET_TOTAL%',
			'%PLAN_AMOUNT_GROSS_TOTAL%',
			'%PLAN_AMOUNT_VAT_TOTAL%',
			'%AMOUNT%',
			'%NAME%',
			'%CUSTOMERNAME%',
			'%CUSTOMER_EMAIL%',
			'%BILLING_NAME%',
			'%ADDRESS1%',
			'%ADDRESS2%',
			'%CITY%',
			'%STATE%',
			'%COUNTRY%',
			'%COUNTRY_CODE%',
			'%ZIP%',
			'%SHIPPING_NAME%',
			'%SHIPPING_ADDRESS1%',
			'%SHIPPING_ADDRESS2%',
			'%SHIPPING_CITY%',
			'%SHIPPING_STATE%',
			'%SHIPPING_COUNTRY%',
			'%SHIPPING_COUNTRY_CODE%',
			'%SHIPPING_ZIP%',
			'%PRODUCT_NAME%',
			'%DATE%',
			'%TRANSACTION_ID%',
			'%FORM_NAME%',
			'%INVOICE_URL%'
		);
	}

	/**
	 * @param $line1
	 * @param $line2
	 * @param $city
	 * @param $state
	 * @param $countryName
	 * @param $countryCode
	 * @param $zip
	 *
	 * @return array
	 */
	public static function prepare_address_data( $line1, $line2, $city, $state, $countryName, $countryCode, $zip ) {
		$addressData = array(
			'line1'        => is_null( $line1 ) ? '' : $line1,
			'line2'        => is_null( $line2 ) ? '' : $line2,
			'city'         => is_null( $city ) ? '' : $city,
			'state'        => is_null( $state ) ? '' : $state,
			'country'      => is_null( $countryName ) ? '' : $countryName,
			'country_code' => is_null( $countryCode ) ? '' : $countryCode,
			'zip'          => is_null( $zip ) ? '' : $zip
		);

		return $addressData;
	}

	/**
	 * This function creates a Stripe shipping address hash
	 *
	 * @param $shipping_name
	 * @param $shipping_phone
	 * @param $address_array array previously created with prepare_address_data()
	 *
	 * @return array
	 */
	public static function prepare_stripe_shipping_hash_from_array( $shipping_name, $shipping_phone, $address_array ) {
		return self::prepare_stripe_shipping_hash(
			$shipping_name,
			$shipping_phone,
			$address_array['line1'],
			$address_array['line2'],
			$address_array['city'],
			$address_array['state'],
			$address_array['country_code'],
			$address_array['zip']
		);
	}

	/**
	 * This function creates a Stripe shipping address hash
	 *
	 * @param $shipping_name string Customer name
	 * @param $shipping_phone string Customer phone (including extension)
	 * @param $line1 string Address line 1 (Street address/PO Box/Company name)
	 * @param $line2 string Address line 2 (Apartment/Suite/Unit/Building)
	 * @param $city string City/District/Suburb/Town/Village
	 * @param $state string State/County/Province/Region
	 * @param $country_code string 2-letter country code
	 * @param $postal_code string ZIP or postal code
	 *
	 * @return array
	 */
	public static function prepare_stripe_shipping_hash( $shipping_name, $shipping_phone, $line1, $line2, $city, $state, $country_code, $postal_code ) {
		$shipping_hash = array();

		//-- The 'name' property is required. It must contain a non-empty value or be null
		$shipping_hash['name'] = ! empty( $shipping_name ) ? $shipping_name : null;

		if ( ! empty( $shipping_phone ) ) {
			$shipping_hash['phone'] = $shipping_phone;
		}
		$address_hash             = self::prepare_stripe_address_hash( $line1, $line2, $city, $state, $country_code, $postal_code );
		$shipping_hash['address'] = $address_hash;

		return $shipping_hash;
	}

	/**
	 * This function creates a Stripe address hash
	 *
	 * @param $line1 string Address line 1 (Street address/PO Box/Company name)
	 * @param $line2 string Address line 2 (Apartment/Suite/Unit/Building)
	 * @param $city string City/District/Suburb/Town/Village
	 * @param $state string State/County/Province/Region
	 * @param $country_code string 2-letter country code
	 * @param $postal_code string ZIP or postal code
	 *
	 * @return array
	 */
	public static function prepare_stripe_address_hash( $line1, $line2, $city, $state, $country_code, $postal_code ) {
		$address_hash = array();

		//-- The 'line1' property is required
		if ( empty( $line1 ) ) {
			throw new InvalidArgumentException( __FUNCTION__ . '(): address line1 is required.' );
		} else {
			$address_hash['line1'] = $line1;
		}
		if ( ! empty( $line2 ) ) {
			$address_hash['line2'] = $line2;
		}
		if ( ! empty( $city ) ) {
			$address_hash['city'] = $city;
		}
		if ( ! empty( $state ) ) {
			$address_hash['state'] = $state;
		}
		if ( ! empty( $country_code ) ) {
			$address_hash['country'] = $country_code;
		}
		if ( ! empty( $postal_code ) ) {
			$address_hash['postal_code'] = $postal_code;
		}

		return $address_hash;
	}

	/**
	 * This function creates a Stripe address hash from an array created previously created with prepare_address_data()
	 *
	 * @param array $address_array
	 *
	 * @return array
	 */
	public static function prepare_stripe_address_hash_from_array( $address_array ) {
		return self::prepare_stripe_address_hash(
			$address_array['line1'],
			$address_array['line2'],
			$address_array['city'],
			$address_array['state'],
			$address_array['country_code'],
			$address_array['zip']
		);
	}

	/**
	 * @param $action_name
	 * @param $customer
	 *
	 * @param $macros
	 * @param $macro_values
	 *
	 * @return array
	 */
	public static function prepare_additional_data_for_subscription_charge( $action_name, $customer, $macros, $macro_values ) {
		$additionalData = array(
			self::ADDITIONAL_DATA_KEY_ACTION_NAME  => $action_name,
			self::ADDITIONAL_DATA_KEY_CUSTOMER     => $customer,
			self::ADDITIONAL_DATA_KEY_MACROS       => $macros,
			self::ADDITIONAL_DATA_KEY_MACRO_VALUES => $macro_values
		);

		return $additionalData;
	}

	/**
	 * @param $form
	 * @param MM_WPFS_SubscriptionTransactionData $transactionData
	 * @param string $escapeType
	 *
	 * @return array
	 */
	public static function getSubscriptionMacroValues( $form, $transactionData, $escapeType = MM_WPFS_Utils::ESCAPE_TYPE_ATTR ) {
		return self::get_subscription_macro_values(
			$form,
			$transactionData->getCustomerName(),
			$transactionData->getCustomerEmail(),
			$transactionData->getBillingName(),
			$transactionData->getBillingAddress(),
			$transactionData->getShippingName(),
			$transactionData->getShippingAddress(),
			$transactionData->getPlanName(),
			$transactionData->getPlanCurrency(),
			$transactionData->getPlanNetSetupFee(),
			$transactionData->getPlanGrossSetupFee(),
			$transactionData->getPlanSetupFeeVAT(),
			$transactionData->getPlanSetupFeeVATRate(),
			$transactionData->getPlanNetSetupFeeTotal(),
			$transactionData->getPlanGrossSetupFeeTotal(),
			$transactionData->getPlanSetupFeeVATTotal(),
			$transactionData->getPlanNetAmount(),
			$transactionData->getPlanGrossAmount(),
			$transactionData->getPlanAmountVAT(),
			$transactionData->getPlanAmountVATRate(),
			$transactionData->getPlanQuantity(),
			$transactionData->getPlanNetAmountTotal(),
			$transactionData->getPlanGrossAmountTotal(),
			$transactionData->getPlanAmountVATTotal(),
			$transactionData->getPlanGrossAmountAndGrossSetupFeeTotal(),
			$transactionData->getProductName(),
			$transactionData->getTransactionId(),
			$transactionData->getFormName(),
			$transactionData->getInvoiceUrl(),
			$escapeType
		);
	}

	/**
	 * @param $form
	 * @param $customerName
	 * @param $customerEmail
	 * @param $billingName
	 * @param $billingAddress
	 * @param $shippingName
	 * @param $shippingAddress
	 * @param $stripePlanName
	 * @param $stripePlanCurrency
	 * @param $stripePlanNetSetupFee
	 * @param $stripePlanGrossSetupFee
	 * @param $stripePlanSetupFeeVAT
	 * @param $stripePlanSetupFeeVATRate
	 * @param $stripePlanNetSetupFeeTotal
	 * @param $stripePlanGrossSetupFeeTotal
	 * @param $stripePlanSetupFeeVATTotal
	 * @param $stripePlanNetAmount
	 * @param $stripePlanGrossAmount
	 * @param $stripePlanAmountVAT
	 * @param $stripePlanAmountVATRate
	 * @param $stripePlanQuantity
	 * @param $stripePlanNetAmountTotal
	 * @param $stripePlanGrossAmountTotal
	 * @param $stripePlanAmountVATTotal
	 * @param $grossAmountAndGrossSetupFeeTotal
	 * @param $productName
	 * @param $transactionId
	 * @param $formName
	 * @param $invoiceUrl
	 * @param string $escapeType
	 *
	 * @return array
	 */
	public static function get_subscription_macro_values(
		$form,
		$customerName, $customerEmail, $billingName, $billingAddress, $shippingName, $shippingAddress,
		$stripePlanName, $stripePlanCurrency,
		$stripePlanNetSetupFee, $stripePlanGrossSetupFee, $stripePlanSetupFeeVAT, $stripePlanSetupFeeVATRate,
		$stripePlanNetSetupFeeTotal, $stripePlanGrossSetupFeeTotal, $stripePlanSetupFeeVATTotal,
		$stripePlanNetAmount, $stripePlanGrossAmount, $stripePlanAmountVAT, $stripePlanAmountVATRate,
		$stripePlanQuantity,
		$stripePlanNetAmountTotal, $stripePlanGrossAmountTotal, $stripePlanAmountVATTotal,
		$grossAmountAndGrossSetupFeeTotal,
		$productName,
		$transactionId,
		$formName,
		$invoiceUrl,
		$escapeType = MM_WPFS_Utils::ESCAPE_TYPE_ATTR
	) {
		$siteTitle  = get_bloginfo( 'name' );
		$dateFormat = get_option( 'date_format' );

		return array(
			MM_WPFS_Currencies::formatAndEscapeByForm( $form, $stripePlanCurrency, $stripePlanGrossSetupFee ),
			MM_WPFS_Currencies::formatAndEscapeByForm( $form, $stripePlanCurrency, $stripePlanNetSetupFee ),
			MM_WPFS_Currencies::formatAndEscapeByForm( $form, $stripePlanCurrency, $stripePlanGrossSetupFee ),
			MM_WPFS_Currencies::formatAndEscapeByForm( $form, $stripePlanCurrency, $stripePlanSetupFeeVAT ),
			self::escape( round( $stripePlanSetupFeeVATRate, 4 ) . '%', $escapeType ),
			MM_WPFS_Currencies::formatAndEscapeByForm( $form, $stripePlanCurrency, $stripePlanGrossSetupFeeTotal ),
			MM_WPFS_Currencies::formatAndEscapeByForm( $form, $stripePlanCurrency, $stripePlanNetSetupFeeTotal ),
			MM_WPFS_Currencies::formatAndEscapeByForm( $form, $stripePlanCurrency, $stripePlanGrossSetupFeeTotal ),
			MM_WPFS_Currencies::formatAndEscapeByForm( $form, $stripePlanCurrency, $stripePlanSetupFeeVATTotal ),
			self::escape( $stripePlanName, $escapeType ),
			MM_WPFS_Currencies::formatAndEscapeByForm( $form, $stripePlanCurrency, $stripePlanGrossAmount ),
			MM_WPFS_Currencies::formatAndEscapeByForm( $form, $stripePlanCurrency, $stripePlanNetAmount ),
			MM_WPFS_Currencies::formatAndEscapeByForm( $form, $stripePlanCurrency, $stripePlanGrossAmount ),
			MM_WPFS_Currencies::formatAndEscapeByForm( $form, $stripePlanCurrency, $stripePlanAmountVAT ),
			self::escape( round( $stripePlanAmountVATRate, 4 ) . '%', $escapeType ),
			self::escape( $stripePlanQuantity, $escapeType ),
			MM_WPFS_Currencies::formatAndEscapeByForm( $form, $stripePlanCurrency, $stripePlanGrossAmountTotal ),
			MM_WPFS_Currencies::formatAndEscapeByForm( $form, $stripePlanCurrency, $stripePlanNetAmountTotal ),
			MM_WPFS_Currencies::formatAndEscapeByForm( $form, $stripePlanCurrency, $stripePlanGrossAmountTotal ),
			MM_WPFS_Currencies::formatAndEscapeByForm( $form, $stripePlanCurrency, $stripePlanAmountVATTotal ),
			MM_WPFS_Currencies::formatAndEscapeByForm( $form, $stripePlanCurrency, $grossAmountAndGrossSetupFeeTotal ),
			self::escape( $siteTitle, $escapeType ),
			self::escape( $customerName, $escapeType ),
			self::escape( $customerEmail, $escapeType ),
			self::escape( $billingName, $escapeType ),
			self::escape( $billingAddress['line1'], $escapeType ),
			self::escape( $billingAddress['line2'], $escapeType ),
			self::escape( $billingAddress['city'], $escapeType ),
			self::escape( $billingAddress['state'], $escapeType ),
			self::escape( $billingAddress['country'], $escapeType ),
			self::escape( $billingAddress['country_code'], $escapeType ),
			self::escape( $billingAddress['zip'], $escapeType ),
			self::escape( $shippingName, $escapeType ),
			self::escape( $shippingAddress['line1'], $escapeType ),
			self::escape( $shippingAddress['line2'], $escapeType ),
			self::escape( $shippingAddress['city'], $escapeType ),
			self::escape( $shippingAddress['state'], $escapeType ),
			self::escape( $shippingAddress['country'], $escapeType ),
			self::escape( $shippingAddress['country_code'], $escapeType ),
			self::escape( $shippingAddress['zip'], $escapeType ),
			self::escape( $productName, $escapeType ),
			self::escape( date( $dateFormat ), $escapeType ),
			self::escape( $transactionId, $escapeType ),
			self::escape( $formName, $escapeType ),
			self::escape( $invoiceUrl, $escapeType )
		);
	}

	/**
	 * @param $value
	 * @param $escapeType
	 *
	 * @return string|void
	 */
	public static function escape( $value, $escapeType ) {
		if ( is_null( $value ) ) {
			return $value;
		}
		if ( self::ESCAPE_TYPE_HTML === $escapeType ) {
			return esc_html( $value );
		} elseif ( self::ESCAPE_TYPE_ATTR === $escapeType ) {
			return esc_attr( $value );
		} else {
			return $value;
		}
	}

	/**
	 * @param $netValue
	 * @param $taxPercent
	 *
	 * @return mixed
	 */
	public static function calculateGrossFromNet( $netValue, $taxPercent ) {
		if ( ! is_numeric( $netValue ) ) {
			throw new InvalidArgumentException( sprintf( 'Parameter %s=%s is not numeric.', 'netValue', $netValue ) );
		}
		if ( ! is_numeric( $taxPercent ) ) {
			throw new InvalidArgumentException( sprintf( 'Parameter %s=%s is not numeric.', 'taxPercent', $taxPercent ) );
		}

		if ( $taxPercent == 0.0 ) {
			$grossValue = $netValue;
			$taxValue   = 0;
		} else {
			$grossValue = round( $netValue * ( 1.0 + round( $taxPercent, 4 ) / 100.0 ) );
			$taxValue   = $grossValue - $netValue;
		}

		$result = array(
			'net'        => $netValue,
			'taxPercent' => $taxPercent,
			'taxValue'   => $taxValue,
			'gross'      => $grossValue
		);

		return $result;
	}

	/**
	 * @param $formId
	 * @param $formType
	 * @param $stripePlan
	 * @param $billingAddress
	 * @param null $customInputs
	 *
	 * @return array
	 */
	public static function prepare_vat_filter_arguments( $formId, $formType, $stripePlan, $billingAddress, $customInputs = null ) {
		$planSetupFee = MM_WPFS_Utils::get_setup_fee_for_plan( $stripePlan );

		$vatFilterArguments = array(
			'form_id'         => $formId,
			'form_type'       => $formType,
			'plan_id'         => isset( $stripePlan ) ? $stripePlan->id : '',
			'plan_currency'   => isset( $stripePlan ) ? $stripePlan->currency : '',
			'plan_amount'     => isset( $stripePlan ) ? $stripePlan->amount : '',
			'plan_setup_fee'  => $planSetupFee,
			'billing_address' => $billingAddress
		);

		if ( is_array( $customInputs ) && sizeof( $customInputs ) > 0 ) {
			$vatFilterArguments['custom_inputs'] = $customInputs;
		}

		return $vatFilterArguments;
	}

	/**
	 * @param \StripeWPFS\StripeObject $stripePlan
	 *
	 * @return int
	 */
	public static function get_setup_fee_for_plan( $stripePlan ) {
		$planSetupFee = 0;
		if ( isset( $stripePlan ) && isset( $stripePlan->metadata ) && isset( $stripePlan->metadata->setup_fee ) ) {
			$planSetupFee = $stripePlan->metadata->setup_fee;
		}

		return $planSetupFee;
	}

	/**
	 * @param $customInputLabels
	 * @param $customInputValues
	 *
	 * @return array
	 */
	public static function prepare_custom_input_data( $customInputLabels, $customInputValues ) {
		$customInputs = array();
		if ( ! is_null( $customInputLabels ) && ! is_null( $customInputValues ) ) {
			foreach ( $customInputLabels as $i => $label ) {
				$customInputs[ $label ] = $customInputValues[ $i ];
			}
		}

		return $customInputs;
	}

	/**
	 * @param $stripePlans
	 * @param $formPlans
	 *
	 * @return array
	 */
	public static function get_sorted_form_plans( $stripePlans, $formPlans ) {
		$plans       = array();
		$formPlanIDs = json_decode( $formPlans );
		foreach ( $stripePlans as $stripePlan ) {
			$item = array_search( $stripePlan->id, $formPlanIDs );
			if ( $item !== false ) {
				$plans[ $item ] = $stripePlan;
			}
		}
		ksort( $plans );

		return $plans;
	}

	/**
	 * @deprecated
	 *
	 * @param $currency
	 * @param $amount
	 *
	 * @return null|string|void
	 */
	public static function format_amount( $currency, $amount ) {
		$formattedAmount = null;
		$currencyArray   = MM_WPFS_Currencies::get_currency_for( $currency );
		if ( is_array( $currencyArray ) ) {
			if ( $currencyArray['zeroDecimalSupport'] == true ) {
				$pattern   = '%d';
				$theAmount = is_numeric( $amount ) ? $amount : 0;
			} else {
				$pattern   = '%0.2f';
				$theAmount = is_numeric( $amount ) ? ( $amount / 100.0 ) : 0;
			}

			$formattedAmount = esc_attr( sprintf( $pattern, $theAmount ) );
		}

		return $formattedAmount;
	}

	/**
	 * Constructs an array with the default email receipt templates.
	 *
	 * @return array
	 */
	public static function create_default_email_receipts() {
		$emailReceipts = array();

		$paymentMade                   = self::create_default_payment_made_email_receipt();
		$cardCaptured                  = self::create_default_card_captured_email_receipt();
		$subscriptionStarted           = self::create_default_subscription_started_email_receipt();
		$subscriptionFinished          = self::create_default_subscription_finished_email_receipt();
		$donationMade                  = self::create_default_donation_made_email_receipt();
		$cardUpdateConfirmationRequest = self::create_default_card_update_confirmation_request_email_receipt();

		$emailReceipts['paymentMade']                   = $paymentMade;
		$emailReceipts['cardCaptured']                  = $cardCaptured;
		$emailReceipts['subscriptionStarted']           = $subscriptionStarted;
		$emailReceipts['subscriptionFinished']          = $subscriptionFinished;
        $emailReceipts['donationMade']                  = $donationMade;
		$emailReceipts['cardUpdateConfirmationRequest'] = $cardUpdateConfirmationRequest;

		return $emailReceipts;
	}

	/**
	 * @return stdClass
	 */
	public static function create_default_payment_made_email_receipt() {
		$paymentMade          = new stdClass();
		$paymentMade->subject = 'Payment Receipt';
		$paymentMade->html    = "<html><body><p>Hi,</p><p>Here's your receipt for your payment of %AMOUNT%</p><p>Thanks</p><br/>%NAME%</body></html>";

		return $paymentMade;
	}

    /**
     * @return stdClass
     */
    public static function create_default_donation_made_email_receipt() {
        $paymentMade          = new stdClass();
        $paymentMade->subject = 'Donation Receipt';
        $paymentMade->html    = "<html><body><p>Hi,</p><p>Here's your receipt for your donation of %AMOUNT%</p><p>Thanks</p><br/>%NAME%</body></html>";

        return $paymentMade;
    }

    /**
	 * @return stdClass
	 */
	public static function create_default_card_captured_email_receipt() {
		$cardCaptured          = new stdClass();
		$cardCaptured->subject = 'Card Information Saved';
		$cardCaptured->html    = '<html><body><p>Hi,</p><p>Your payment information has been saved.</p><p>Thanks</p><br/>%NAME%</body></html>';

		return $cardCaptured;
	}

	/**
	 * @return stdClass
	 */
	public static function create_default_subscription_started_email_receipt() {
		$subscriptionStarted          = new stdClass();
		$subscriptionStarted->subject = 'Subscription Receipt';
		$subscriptionStarted->html    = "<html><body><p>Hi,</p><p>Here's your receipt for your subscription of %AMOUNT%</p><p>Thanks</p><br/>%NAME%</body></html>";

		return $subscriptionStarted;
	}

	/**
	 * @return stdClass
	 */
	public static function create_default_subscription_finished_email_receipt() {
		$subscriptionFinished          = new stdClass();
		$subscriptionFinished->subject = 'Subscription Ended';
		$subscriptionFinished->html    = '<html><body><p>Hi,</p><p>Your subscription has ended.</p><p>Thanks</p><br/>%NAME%</body></html>';

		return $subscriptionFinished;
	}

	public static function create_default_card_update_confirmation_request_email_receipt() {
		/** @noinspection PhpUnusedLocalVariableInspection */
		$homeUrl                                = home_url();
		$cardUpdateConfirmationRequest          = new stdClass();
		$cardUpdateConfirmationRequest->subject = 'Security code for updating subscription';
		$cardUpdateConfirmationRequest->html    = '<html>
<body>
<p>Dear %CUSTOMER_EMAIL%,</p>

<p>You are receiving this email because you requested access to the page where you can manage your subscription(s).</p>

<br/>
<table>
    <tr>
        <td><b>Subscription management page:</b></td>
        <td><a href="https://www.example.com/manage-subscription">https://www.example.com/manage-subscription</a></td>
    </tr>
    <tr>
        <td><b>Your security code:</b></td>
        <td>%CARD_UPDATE_SECURITY_CODE%</td>
    </tr>
</table>

<br/>
<p>
    Thanks,<br/>
    %NAME%
</p>
</body>
</html>';

		return $cardUpdateConfirmationRequest;
	}

	/**
	 * Parse amount as smallest common currency unit with the given currency if the amount is a number.
	 *
	 * @param $currency
	 * @param $amount
	 *
	 * @return int|string the parsed value if the amount is a valid number, the amount itself otherwise
	 */
	public static function parse_amount( $currency, $amount ) {
		if ( ! is_numeric( $amount ) ) {
			return $amount;
		}
		$currencyArray = MM_WPFS_Currencies::get_currency_for( $currency );
		if ( is_array( $currencyArray ) ) {
			if ( $currencyArray['zeroDecimalSupport'] == true ) {
				$theAmount = $amount;
			} else {
				$theAmount = $amount * 100.0;
			}

			return $theAmount;
		}

		return $amount;
	}

	/**
	 * @deprecated
	 * Insert the inputs into the metadata array
	 *
	 * @param $metadata
	 * @param $customInputs
	 * @param $customInputValues
	 *
	 * @return mixed
	 */
	public static function add_custom_inputs( $metadata, $customInputs, $customInputValues ) {

		// MM_WPFS_Utils::log( 'add_custom_inputs(): CALLED, params: metadata=' . print_r( $metadata, true ) . ', customInputs=' . print_r( $customInputs, true ) . ', customInputValues=' . print_r( $customInputValues, true ) );

		if ( $customInputs == null ) {
			$customInputValueString = is_array( $customInputValues ) ? implode( ",", $customInputValues ) : printf( $customInputValues );
			if ( ! empty( $customInputValueString ) ) {
				$metadata['custom_inputs'] = $customInputValueString;
			}
		} else {
			$customInputLabels = MM_WPFS_Utils::decode_custom_input_labels( $customInputs );
			foreach ( $customInputLabels as $i => $label ) {
				$key = $label;
				if ( array_key_exists( $key, $metadata ) ) {
					$key = $label . $i;
				}
				if ( ! empty( $customInputValues[ $i ] ) ) {
					$metadata[ $key ] = $customInputValues[ $i ];
				}
			}
		}

		return $metadata;
	}

	/**
	 * @deprecated
	 *
	 * @param $encodedCustomInputs
	 *
	 * @return array
	 */
	public static function decode_custom_input_labels( $encodedCustomInputs ) {
		$customInputLabels = array();
		if ( ! is_null( $encodedCustomInputs ) ) {
			$customInputLabels = explode( '{{', $encodedCustomInputs );
		}

		return $customInputLabels;
	}

	/**
	 * @param $metadata
	 * @param $key
	 * @param $value
	 *
	 * @return mixed
	 */
	public static function add_metadata_entry( $metadata, $key, $value ) {
		if ( is_string( $value ) && strlen( $value ) > self::STRIPE_METADATA_VALUE_MAX_LENGTH ) {
			$parts = str_split( $value, self::STRIPE_METADATA_VALUE_MAX_LENGTH );
			foreach ( $parts as $i => $part ) {
				$indexPostfix                    = '_' . ( $i + 1 );
				$metadataIndexedKey              = substr( $key, 0, self::STRIPE_METADATA_KEY_MAX_LENGTH - sizeof( $indexPostfix ) ) . $indexPostfix;
				$metadata[ $metadataIndexedKey ] = $parts[ $i ];
			}
		} else {
			if ( sizeof( $metadata ) < self::STRIPE_METADATA_KEY_MAX_COUNT ) {
				$metadataKey              = substr( $key, 0, self::STRIPE_METADATA_KEY_MAX_LENGTH );
				$metadata[ $metadataKey ] = substr( $value, 0, self::STRIPE_METADATA_VALUE_MAX_LENGTH );
			}
		}

		return $metadata;
	}

	/**
	 * @deprecated
	 *
	 * @param $customerEmail
	 * @param $customerName
	 * @param $formName
	 * @param $billingName
	 * @param $billingAddressLine1
	 * @param $billingAddressLine2
	 * @param $billingAddressZip
	 * @param $billingAddressCity
	 * @param $billingAddressState
	 * @param $billingAddressCountry
	 * @param $billingAddressCountryCode
	 * @param $shippingName
	 * @param $shippingAddressLine1
	 * @param $shippingAddressLine2
	 * @param $shippingAddressZip
	 * @param $shippingAddressCity
	 * @param $shippingAddressState
	 * @param $shippingAddressCountry
	 * @param $shippingAddressCountryCode
	 *
	 * @return array
	 */
	public static function create_metadata( $customerEmail, $customerName, $formName, $billingName = null, $billingAddressLine1 = null, $billingAddressLine2 = null, $billingAddressZip = null, $billingAddressCity = null, $billingAddressState = null, $billingAddressCountry = null, $billingAddressCountryCode = null, $shippingName = null, $shippingAddressLine1 = null, $shippingAddressLine2 = null, $shippingAddressZip = null, $shippingAddressCity = null, $shippingAddressState = null, $shippingAddressCountry = null, $shippingAddressCountryCode = null ) {
		$metadata = array();

		if ( ! empty( $customerEmail ) ) {
			$metadata['customer_email'] = $customerEmail;
		}
		if ( ! empty( $customerName ) ) {
			$metadata['customer_name'] = $customerName;
		}
		if ( ! empty( $formName ) ) {
			$metadata['form_name'] = $formName;
		}

		if ( ! empty( $billingName ) ) {
			$metadata['billing_name'] = $billingName;
		}
		if ( ! empty( $billingAddressLine1 ) || ! empty( $billingAddressZip ) || ! empty( $billingAddressCity ) || ! empty( $billingAddressCountry ) ) {
			$metadata['billing_address'] = implode( '|', array(
				$billingAddressLine1,
				$billingAddressLine2,
				$billingAddressZip,
				$billingAddressCity,
				$billingAddressState,
				$billingAddressCountry,
				$billingAddressCountryCode
			) );
		}
		if ( ! empty( $shippingName ) ) {
			$metadata['shipping_name'] = $shippingName;
		}
		if ( ! empty( $shippingAddressLine1 ) || ! empty( $shippingAddressZip ) || ! empty( $shippingAddressCity ) || ! empty( $shippingAddressCountry ) ) {
			$metadata['shipping_address'] = implode( '|', array(
				$shippingAddressLine1,
				$shippingAddressLine2,
				$shippingAddressZip,
				$shippingAddressCity,
				$shippingAddressState,
				$shippingAddressCountry,
				$shippingAddressCountryCode
			) );
		}

		return $metadata;
	}

	/**
	 * @deprecated
	 *
	 * @param $showCustomInput
	 * @param $customInputLabels
	 * @param $customInputTitle
	 * @param $customInputValues
	 * @param $customInputRequired
	 *
	 * @return ValidationResult
	 */
	public static function validate_custom_input_values( $showCustomInput, $customInputLabels, $customInputTitle, $customInputValues, $customInputRequired ) {
		$result = new ValidationResult();

		if ( $showCustomInput == 0 ) {
			return $result;
		}

		if ( $customInputRequired == 1 ) {
			if ( $customInputLabels == null ) {
				if ( is_null( $customInputValues ) || ( trim( $customInputValues ) == false ) ) {
					$result->setValid( false );
					$result->setMessage( sprintf( __( "Please enter a value for '%s'", MM_WPFS::L10N_DOMAIN_PUBLIC ), MM_WPFS_Localization::translateLabel( $customInputTitle ) ) );
				}
			} else {
				$customInputLabelArray = MM_WPFS_Utils::decode_custom_input_labels( $customInputLabels );
				foreach ( $customInputLabelArray as $i => $label ) {
					if ( $result->isValid() && ( is_null( $customInputValues[ $i ] ) || ( trim( $customInputValues[ $i ] ) == false ) ) ) {
						$result->setValid( false );
						$result->setMessage( sprintf( __( "Please enter a value for '%s'", MM_WPFS::L10N_DOMAIN_PUBLIC ), MM_WPFS_Localization::translateLabel( $label ) ) );
					}
				}
			}
		}

		if ( $result->isValid() ) {
			if ( $customInputLabels == null ) {
				if ( is_string( $customInputValues ) && strlen( $customInputValues ) > MM_WPFS_Utils::STRIPE_METADATA_VALUE_MAX_LENGTH ) {
					$result->setValid( false );
					$result->setMessage( sprintf(
					/* translators: Validation error message for a field whose value is too long */
						__( "The value for '%s' is too long.", MM_WPFS::L10N_DOMAIN_PUBLIC ), MM_WPFS_Localization::translateLabel( $customInputTitle ) ) );
				}
			} else {
				$customInputLabelArray = MM_WPFS_Utils::decode_custom_input_labels( $customInputLabels );
				foreach ( $customInputLabelArray as $i => $label ) {
					if ( $result->isValid() && ( is_string( $customInputValues[ $i ] ) && strlen( $customInputValues[ $i ] ) > MM_WPFS_Utils::STRIPE_METADATA_VALUE_MAX_LENGTH ) ) {
						$result->setValid( false );
						$result->setMessage( sprintf(
						/* translators: Validation error message for a field whose value is too long */
							__( "The value for '%s' is too long.", MM_WPFS::L10N_DOMAIN_PUBLIC ), MM_WPFS_Localization::translateLabel( $label ) ) );
					}
				}
			}
		}

		return $result;
	}

	/**
	 * @deprecated
	 *
	 * @param \StripeWPFS\StripeObject $stripeCustomer
	 *
	 * @return string Stripe Customer's name or null
	 */
	public static function retrieve_customer_name( $stripeCustomer ) {
		$customerName = null;
		if ( isset( $stripeCustomer ) && isset( $stripeCustomer->metadata ) && isset( $stripeCustomer->metadata->customer_name ) ) {
			$customerName = $stripeCustomer->metadata->customer_name;
		}
		if ( is_null( $customerName ) ) {
			if ( isset( $stripeCustomer->subscriptions ) ) {
				foreach ( $stripeCustomer->subscriptions as $subscription ) {
					if ( is_null( $customerName ) ) {
						if ( isset( $subscription->metadata ) && isset( $subscription->metadata->customer_name ) ) {
							$customerName = $subscription->metadata->customer_name;
						}
					}
				}
			}
		}

		return $customerName;
	}

	/**
	 * @param MM_WPFS_Database $databaseService
	 * @param MM_WPFS_Payment_API $stripeService
	 * @param string $stripeCustomerEmail
	 *
	 * @param bool $returnStripeCustomerObject
	 *
	 * @return \StripeWPFS\Customer
	 */
	public static function find_existing_stripe_customer_by_email( $databaseService, $stripeService, $stripeCustomerEmail, $returnStripeCustomerObject = false ) {

		$options  = get_option( 'fullstripe_options' );
		$liveMode = $options['apiMode'] === 'live';

		$customers = $databaseService->get_existing_stripe_customers_by_email( $stripeCustomerEmail, $liveMode );

		$result = null;
		foreach ( $customers as $customer ) {
			$stripeCustomer = null;
			try {
				$stripeCustomer = $stripeService->retrieve_customer( $customer['stripeCustomerID'] );
			} catch ( Exception $e ) {
				MM_WPFS_Utils::logException( $e );
			}

			if ( isset( $stripeCustomer ) && ( ! isset( $stripeCustomer->deleted ) || ! $stripeCustomer->deleted ) ) {
				if ( $returnStripeCustomerObject ) {
					$result = $stripeCustomer;
				} else {
					$result = $customer;
				}
				break;
			}
		}

		return $result;
	}

	public static function logException( Exception $e, $object = null ) {
		if ( isset( $e ) ) {
			if ( is_null( $object ) ) {
				$message = sprintf( 'Message=%s, Stack=%s ', $e->getMessage(), $e->getTraceAsString() );
			} else {
				$message = sprintf( 'Class=%s, Message=%s, Stack=%s ', get_class( $object ), $e->getMessage(), $e->getTraceAsString() );
			}
			MM_WPFS_Utils::log( $message );
		}
	}

	public static function log( $message ) {
		error_log( self::WPFS_LOG_MESSAGE_PREFIX . $message );
	}

	/**
	 * @param MM_WPFS_Public_PaymentFormModel $paymentFormModel
	 * @param MM_WPFS_PaymentTransactionData $transactionData
	 *
	 * @return mixed
	 */
	public static function prepareStripeChargeDescription( $paymentFormModel, $transactionData ) {
		$stripeChargeDescription = '';
		if ( isset( $paymentFormModel->getForm()->stripeDescription ) && ! empty( $paymentFormModel->getForm()->stripeDescription ) ) {
			$formStripeDescription   = MM_WPFS_Localization::translateLabel( $paymentFormModel->getForm()->stripeDescription );
			$macros                  = MM_WPFS_Utils::get_payment_macros();
			$macroValues             = MM_WPFS_Utils::getPaymentMacroValues( $paymentFormModel->getForm(), $transactionData, MM_WPFS_Utils::ESCAPE_TYPE_NONE );
			$stripeChargeDescription = str_replace(
				$macros,
				$macroValues,
				$formStripeDescription
			);
			$stripeChargeDescription = MM_WPFS_Utils::replace_custom_fields( $stripeChargeDescription, $paymentFormModel->getCustomInputvalues(), MM_WPFS_Utils::ESCAPE_TYPE_NONE );
		}

		return $stripeChargeDescription;
	}

    /**
     * @param MM_WPFS_Public_DonationFormModel $donationFormModel
     * @param MM_WPFS_DonationTransactionData $transactionData
     *
     * @return mixed
     */
    public static function prepareStripeDonationDescription($donationFormModel, $transactionData ) {
        $stripeChargeDescription = '';
        if ( isset( $donationFormModel->getForm()->stripeDescription ) && ! empty( $donationFormModel->getForm()->stripeDescription ) ) {
            $formStripeDescription   = MM_WPFS_Localization::translateLabel( $donationFormModel->getForm()->stripeDescription );
            $macros                  = MM_WPFS_Utils::get_payment_macros();
            $macroValues             = MM_WPFS_Utils::getPaymentMacroValues( $donationFormModel->getForm(), $transactionData, MM_WPFS_Utils::ESCAPE_TYPE_NONE );
            $stripeChargeDescription = str_replace(
                $macros,
                $macroValues,
                $formStripeDescription
            );
            $stripeChargeDescription = MM_WPFS_Utils::replace_custom_fields( $stripeChargeDescription, $donationFormModel->getCustomInputvalues(), MM_WPFS_Utils::ESCAPE_TYPE_NONE );
        }

        return $stripeChargeDescription;
    }

    /**
	 * @return array
	 */
	public static function get_payment_macros() {
		return array(
			'%AMOUNT%',
			'%NAME%',
			'%CUSTOMERNAME%',
			'%CUSTOMER_EMAIL%',
			'%BILLING_NAME%',
			'%ADDRESS1%',
			'%ADDRESS2%',
			'%CITY%',
			'%STATE%',
			'%COUNTRY%',
			'%COUNTRY_CODE%',
			'%ZIP%',
			'%SHIPPING_NAME%',
			'%SHIPPING_ADDRESS1%',
			'%SHIPPING_ADDRESS2%',
			'%SHIPPING_CITY%',
			'%SHIPPING_STATE%',
			'%SHIPPING_COUNTRY%',
			'%SHIPPING_COUNTRY_CODE%',
			'%SHIPPING_ZIP%',
			'%PRODUCT_NAME%',
			'%DATE%',
			'%FORM_NAME%',
			'%TRANSACTION_ID%'
		);
	}

    /**
     * @return array
     */
    public static function getDonationMacros() {
        return array(
            '%AMOUNT%',
            '%NAME%',
            '%CUSTOMERNAME%',
            '%CUSTOMER_EMAIL%',
            '%BILLING_NAME%',
            '%ADDRESS1%',
            '%ADDRESS2%',
            '%CITY%',
            '%STATE%',
            '%COUNTRY%',
            '%COUNTRY_CODE%',
            '%ZIP%',
            '%SHIPPING_NAME%',
            '%SHIPPING_ADDRESS1%',
            '%SHIPPING_ADDRESS2%',
            '%SHIPPING_CITY%',
            '%SHIPPING_STATE%',
            '%SHIPPING_COUNTRY%',
            '%SHIPPING_COUNTRY_CODE%',
            '%SHIPPING_ZIP%',
            '%PRODUCT_NAME%',
            '%DATE%',
            '%FORM_NAME%',
            '%TRANSACTION_ID%'
        );
    }

    /**
	 * @param $form
	 * @param MM_WPFS_PaymentTransactionData $transactionData
	 * @param string $escapeType
	 *
	 * @return array
	 */
	public static function getPaymentMacroValues( $form, $transactionData, $escapeType = MM_WPFS_Utils::ESCAPE_TYPE_ATTR ) {
		return MM_WPFS_Utils::get_payment_macro_values(
			$form,
			$transactionData->getCustomerName(),
			$transactionData->getCustomerEmail(),
			$transactionData->getCurrency(),
			$transactionData->getAmount(),
			$transactionData->getBillingName(),
			$transactionData->getBillingAddress(),
			$transactionData->getShippingName(),
			$transactionData->getShippingAddress(),
			$transactionData->getProductName(),
			$transactionData->getFormName(),
			$transactionData->getTransactionId(),
			$escapeType
		);
	}

    /**
     * @param $form
     * @param MM_WPFS_DonationTransactionData $transactionData
     * @param string $escapeType
     *
     * @return array
     */
    public static function getDonationMacroValues( $form, $transactionData, $escapeType = MM_WPFS_Utils::ESCAPE_TYPE_ATTR ) {
        // cdx todo: create get_donation_macro_values() function
        return MM_WPFS_Utils::get_payment_macro_values(
            $form,
            $transactionData->getCustomerName(),
            $transactionData->getCustomerEmail(),
            $transactionData->getCurrency(),
            $transactionData->getAmount(),
            $transactionData->getBillingName(),
            $transactionData->getBillingAddress(),
            $transactionData->getShippingName(),
            $transactionData->getShippingAddress(),
            $transactionData->getProductName(),
            $transactionData->getFormName(),
            $transactionData->getTransactionId(),
            $escapeType
        );
    }

    /**
	 * @param $form
	 * @param $customerName
	 * @param $email
	 * @param $currency
	 * @param $amount
	 * @param $billingName
	 * @param $billingAddress
	 * @param $shippingName
	 * @param $shippingAddress
	 * @param $productName
	 * @param $formName
	 * @param $transaction_id
	 * @param string $escapeType
	 *
	 * @return array
	 */
	public static function get_payment_macro_values( $form, $customerName, $email, $currency, $amount, $billingName, $billingAddress, $shippingName, $shippingAddress, $productName, $formName, $transaction_id, $escapeType = MM_WPFS_Utils::ESCAPE_TYPE_ATTR ) {
		$siteTitle  = get_bloginfo( 'name' );
		$dateFormat = get_option( 'date_format' );

		// This is a terrible patch.
        // One-time payment forms and save card forms use the same transaction data structure at the moment.
        // However, save card forms don't have a currency and amount. So let's have them a default value.
        // todo: Use a separate transaction data structure for saved cards
        $verifiedCurrency   = isset( $currency ) ? $currency : 'usd';
        $verifiedAmount     = isset( $amount ) ? $amount : 0;

		return array(
			MM_WPFS_Currencies::formatAndEscapeByForm( $form, $verifiedCurrency, $verifiedAmount ),
			self::escape( $siteTitle, $escapeType ),
			self::escape( $customerName, $escapeType ),
			self::escape( $email, $escapeType ),
			self::escape( $billingName, $escapeType ),
			self::escape( $billingAddress['line1'], $escapeType ),
			self::escape( $billingAddress['line2'], $escapeType ),
			self::escape( $billingAddress['city'], $escapeType ),
			self::escape( $billingAddress['state'], $escapeType ),
			self::escape( $billingAddress['country'], $escapeType ),
			self::escape( $billingAddress['country_code'], $escapeType ),
			self::escape( $billingAddress['zip'], $escapeType ),
			self::escape( $shippingName, $escapeType ),
			self::escape( $shippingAddress['line1'], $escapeType ),
			self::escape( $shippingAddress['line2'], $escapeType ),
			self::escape( $shippingAddress['city'], $escapeType ),
			self::escape( $shippingAddress['state'], $escapeType ),
			self::escape( $shippingAddress['country'], $escapeType ),
			self::escape( $shippingAddress['country_code'], $escapeType ),
			self::escape( $shippingAddress['zip'], $escapeType ),
			self::escape( $productName, $escapeType ),
			self::escape( date( $dateFormat ), $escapeType ),
			self::escape( $formName, $escapeType ),
			self::escape( $transaction_id, $escapeType )
		);
	}

    /**
     * @return array
     */
    public static function get_save_card_macros() {
        return array(
            '%NAME%',
            '%CUSTOMERNAME%',
            '%CUSTOMER_EMAIL%',
            '%BILLING_NAME%',
            '%ADDRESS1%',
            '%ADDRESS2%',
            '%CITY%',
            '%STATE%',
            '%COUNTRY%',
            '%COUNTRY_CODE%',
            '%ZIP%',
            '%SHIPPING_NAME%',
            '%SHIPPING_ADDRESS1%',
            '%SHIPPING_ADDRESS2%',
            '%SHIPPING_CITY%',
            '%SHIPPING_STATE%',
            '%SHIPPING_COUNTRY%',
            '%SHIPPING_COUNTRY_CODE%',
            '%SHIPPING_ZIP%',
            '%DATE%',
            '%FORM_NAME%',
            '%TRANSACTION_ID%'
        );
    }

    /**
     * @param $form
     * @param $customerName
     * @param $email
     * @param $billingName
     * @param $billingAddress
     * @param $shippingName
     * @param $shippingAddress
     * @param $formName
     * @param $transaction_id
     * @param string $escapeType
     *
     * @return array
     */
    public static function get_save_card_macro_values( $form, $customerName, $email, $billingName, $billingAddress, $shippingName, $shippingAddress, $formName, $transaction_id, $escapeType = MM_WPFS_Utils::ESCAPE_TYPE_ATTR ) {
        $siteTitle  = get_bloginfo( 'name' );
        $dateFormat = get_option( 'date_format' );

        return array(
            self::escape( $siteTitle, $escapeType ),
            self::escape( $customerName, $escapeType ),
            self::escape( $email, $escapeType ),
            self::escape( $billingName, $escapeType ),
            self::escape( $billingAddress['line1'], $escapeType ),
            self::escape( $billingAddress['line2'], $escapeType ),
            self::escape( $billingAddress['city'], $escapeType ),
            self::escape( $billingAddress['state'], $escapeType ),
            self::escape( $billingAddress['country'], $escapeType ),
            self::escape( $billingAddress['country_code'], $escapeType ),
            self::escape( $billingAddress['zip'], $escapeType ),
            self::escape( $shippingName, $escapeType ),
            self::escape( $shippingAddress['line1'], $escapeType ),
            self::escape( $shippingAddress['line2'], $escapeType ),
            self::escape( $shippingAddress['city'], $escapeType ),
            self::escape( $shippingAddress['state'], $escapeType ),
            self::escape( $shippingAddress['country'], $escapeType ),
            self::escape( $shippingAddress['country_code'], $escapeType ),
            self::escape( $shippingAddress['zip'], $escapeType ),
            self::escape( date( $dateFormat ), $escapeType ),
            self::escape( $formName, $escapeType ),
            self::escape( $transaction_id, $escapeType )
        );
    }

    /**
     * @param $form
     * @param MM_WPFS_PaymentTransactionData $transactionData
     * @param string $escapeType
     *
     * @return array
     */
    public static function getSaveCardMacroValues( $form, $transactionData, $escapeType = MM_WPFS_Utils::ESCAPE_TYPE_ATTR ) {
        return MM_WPFS_Utils::get_save_card_macro_values(
            $form,
            $transactionData->getCustomerName(),
            $transactionData->getCustomerEmail(),
            $transactionData->getBillingName(),
            $transactionData->getBillingAddress(),
            $transactionData->getShippingName(),
            $transactionData->getShippingAddress(),
            $transactionData->getFormName(),
            $transactionData->getTransactionId(),
            $escapeType
        );
    }

    /**
	 * @param $content
	 * @param $custom_input_values
	 * @param string $escapeTypes
	 *
	 * @return mixed
	 */
	public static function replace_custom_fields( $content, $custom_input_values, $escapeTypes = MM_WPFS_Utils::ESCAPE_TYPE_ATTR ) {
		$custom_field_macros       = self::get_custom_field_macros();
		$custom_field_macro_values = self::get_custom_field_macro_values( count( $custom_field_macros ), $custom_input_values, $escapeTypes );
		$content                   = str_replace(
			$custom_field_macros,
			$custom_field_macro_values,
			$content
		);

		return $content;
	}

	/**
	 * @return array
	 */
	public static function get_custom_field_macros() {
		$customInputFieldMaxCount = MM_WPFS::get_custom_input_field_max_count();

		$customFieldMacros = array();

		for ( $i = 1; $i <= $customInputFieldMaxCount; $i ++ ) {
			array_push( $customFieldMacros, "%CUSTOMFIELD$i%" );
		}

		return $customFieldMacros;
	}

	/**
	 * @param $customFieldCount
	 * @param $customInputValues
	 * @param string $escapeType
	 *
	 * @return array
	 */
	public static function get_custom_field_macro_values( $customFieldCount, $customInputValues, $escapeType = MM_WPFS_Utils::ESCAPE_TYPE_ATTR ) {
		$macroValues = array();
		if ( isset( $customInputValues ) && is_array( $customInputValues ) ) {
			$customInputValueCount = count( $customInputValues );
			for ( $index = 0; $index < $customFieldCount; $index ++ ) {
				if ( $index < $customInputValueCount ) {
					$value = $customInputValues[ $index ];
				} else {
					$value = '';
				}
				array_push( $macroValues, self::escape( $value, $escapeType ) );
			}
		}

		return $macroValues;
	}

	/**
	 * @param MM_WPFS_Public_PaymentFormModel $paymentFormModel
	 * @param MM_WPFS_PaymentTransactionData $transactionData
	 *
	 * @return mixed
	 */
	public static function prepareStripeCardSavedDescription( $paymentFormModel, $transactionData ) {
		$stripeCustomerDescription = '';
		if ( isset( $paymentFormModel->getForm()->stripeDescription ) && ! empty( $paymentFormModel->getForm()->stripeDescription ) ) {
			$formStripeDescription     = MM_WPFS_Localization::translateLabel( $paymentFormModel->getForm()->stripeDescription );
			$macros                    = MM_WPFS_Utils::get_save_card_macros();
			$macroValues               = MM_WPFS_Utils::getSaveCardMacroValues( $paymentFormModel->getForm(), $transactionData, MM_WPFS_Utils::ESCAPE_TYPE_NONE );
			$stripeCustomerDescription = str_replace(
				$macros,
				$macroValues,
				$formStripeDescription
			);
			$stripeCustomerDescription = MM_WPFS_Utils::replace_custom_fields( $stripeCustomerDescription, $paymentFormModel->getCustomInputvalues(), MM_WPFS_Utils::ESCAPE_TYPE_NONE );
		}

		return $stripeCustomerDescription;
	}

	/**
	 * @param MM_WPFS_Database $databaseService
	 * @param MM_WPFS_Payment_API $stripeService
	 * @param string $stripeCustomerEmail
	 *
	 * @return \StripeWPFS\Customer
	 */
	public static function find_existing_stripe_customer_anywhere_by_email( $databaseService, $stripeService, $stripeCustomerEmail ) {

		$options  = get_option( 'fullstripe_options' );
		$liveMode = $options['apiMode'] === 'live';

		$customers = $databaseService->get_existing_stripe_customers_by_email( $stripeCustomerEmail, $liveMode );

		$result = null;
		foreach ( $customers as $customer ) {
			$stripeCustomer = null;
			try {
				$stripeCustomer = $stripeService->retrieve_customer( $customer['stripeCustomerID'] );
			} catch ( Exception $e ) {
				MM_WPFS_Utils::logException( $e );
			}

			if ( isset( $stripeCustomer ) && ( ! isset( $stripeCustomer->deleted ) || ! $stripeCustomer->deleted ) ) {
				$result = $stripeCustomer;
				break;
			}
		}

		if ( is_null( $result ) ) {
			$stripeCustomers = $stripeService->get_customers_by_email( $stripeCustomerEmail );

			foreach ( $stripeCustomers as $stripeCustomer ) {
				if ( isset( $stripeCustomer ) && ( ! isset( $stripeCustomer->deleted ) || ! $stripeCustomer->deleted ) ) {
					$result = $stripeCustomer;
					break;
				}
			}
		}

		return $result;
	}

	public static function get_google_recaptcha_site_key() {
		$googleReCAPTCHASiteKey = null;
		$options                = get_option( 'fullstripe_options' );
		if ( array_key_exists( MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SITE_KEY, $options ) ) {
			$googleReCAPTCHASiteKey = $options[ MM_WPFS::OPTION_GOOGLE_RE_CAPTCHA_SITE_KEY ];
		}

		return $googleReCAPTCHASiteKey;
	}

	public static function get_secure_inline_forms_with_google_recaptcha() {
		$options = get_option( 'fullstripe_options' );
		if ( array_key_exists( MM_WPFS::OPTION_SECURE_INLINE_FORMS_WITH_GOOGLE_RE_CAPTCHA, $options ) ) {
			return $options[ MM_WPFS::OPTION_SECURE_INLINE_FORMS_WITH_GOOGLE_RE_CAPTCHA ] == '1';
		}

		return false;
	}

	public static function get_secure_checkout_forms_with_google_recaptcha() {
		$options = get_option( 'fullstripe_options' );
		if ( array_key_exists( MM_WPFS::OPTION_SECURE_CHECKOUT_FORMS_WITH_GOOGLE_RE_CAPTCHA, $options ) ) {
			return $options[ MM_WPFS::OPTION_SECURE_CHECKOUT_FORMS_WITH_GOOGLE_RE_CAPTCHA ] == '1';
		}

		return false;
	}

	public static function get_secure_subscription_update_with_google_recaptcha() {
		$options = get_option( 'fullstripe_options' );
		if ( array_key_exists( MM_WPFS::OPTION_SECURE_SUBSCRIPTION_UPDATE_WITH_GOOGLE_RE_CAPTCHA, $options ) ) {
			return $options[ MM_WPFS::OPTION_SECURE_SUBSCRIPTION_UPDATE_WITH_GOOGLE_RE_CAPTCHA ] == '1';
		}

		return false;
	}

	public static function create_default_payment_stripe_description() {
		return
			/* translators: Default transaction description for one-time payments */
			__( 'Payment on form %FORM_NAME%', MM_WPFS::L10N_DOMAIN_PUBLIC );
	}

	public static function createDefaultCardSavedDescription() {
		return
			/* translators: Default transaction description for saved cards */
			__( 'Card saved on form %FORM_NAME%', MM_WPFS::L10N_DOMAIN_PUBLIC );
	}

    public static function create_default_donation_stripe_description() {
        return
            /* translators: Default transaction description for donations */
            __( 'Donation on form %FORM_NAME%', MM_WPFS::L10N_DOMAIN_PUBLIC );
    }

    /**
	 * @deprecated
	 *
	 * @param $data
	 *
	 * @return array|string
	 */
	public static function html_escape_value( $data ) {
		if ( ! is_array( $data ) ) {
			return htmlspecialchars( $data, ENT_QUOTES, 'UTF-8', false );
		}

		$escapedData = array();

		foreach ( $data as $value ) {
			array_push( $escapedData, self::html_escape_value( $value ) );
		}

		return $escapedData;
	}

	public static function get_default_terms_of_use_label() {
		$defaultTermsOfUseURL = home_url( '/terms-of-use' );

		return sprintf(
		/* translators: Default label for the Terms of Use checkbox */
			__( "I accept the <a href='%s' target='_blank'>Terms of Use</a>" ), $defaultTermsOfUseURL );
	}

	public static function get_default_terms_of_use_not_checked_error_message() {
		return
			/* translation: Field validation error message when the Terms of use checkbox is not checked */
			__( 'Please accept the Terms of Use', MM_WPFS::L10N_DOMAIN_PUBLIC );
	}

	public static function get_payment_statuses() {
		return array(
			MM_WPFS::PAYMENT_STATUS_FAILED,
			MM_WPFS::PAYMENT_STATUS_RELEASED,
			MM_WPFS::PAYMENT_STATUS_REFUNDED,
			MM_WPFS::PAYMENT_STATUS_EXPIRED,
			MM_WPFS::PAYMENT_STATUS_PAID,
			MM_WPFS::PAYMENT_STATUS_AUTHORIZED,
			MM_WPFS::PAYMENT_STATUS_PENDING
		);
	}

	/**
	 * @param $payment
	 *
	 * @return string
	 */
	public static function get_payment_status( $payment ) {
		if ( is_null( $payment ) ) {
			$payment_status = MM_WPFS::PAYMENT_STATUS_UNKNOWN;
		} elseif ( MM_WPFS::STRIPE_CHARGE_STATUS_FAILED === $payment->last_charge_status ) {
			$payment_status = MM_WPFS::PAYMENT_STATUS_FAILED;
		} elseif ( MM_WPFS::STRIPE_CHARGE_STATUS_PENDING === $payment->last_charge_status ) {
			$payment_status = MM_WPFS::PAYMENT_STATUS_PENDING;
		} elseif ( 1 == $payment->expired ) {
			$payment_status = MM_WPFS::PAYMENT_STATUS_EXPIRED;
		} elseif ( 1 == $payment->refunded ) {
			if ( 1 == $payment->captured ) {
				$payment_status = MM_WPFS::PAYMENT_STATUS_REFUNDED;
			} else {
				$payment_status = MM_WPFS::PAYMENT_STATUS_RELEASED;
			}
		} elseif ( MM_WPFS::STRIPE_CHARGE_STATUS_SUCCEEDED === $payment->last_charge_status && 1 == $payment->paid && 1 == $payment->captured ) {
			$payment_status = MM_WPFS::PAYMENT_STATUS_PAID;
		} elseif ( MM_WPFS::STRIPE_CHARGE_STATUS_SUCCEEDED === $payment->last_charge_status && 1 == $payment->paid && 0 == $payment->captured ) {
			$payment_status = MM_WPFS::PAYMENT_STATUS_AUTHORIZED;
		} else {
			$payment_status = MM_WPFS::PAYMENT_STATUS_UNKNOWN;
		}

		return $payment_status;
	}

    /**
     * @param $donation
     *
     * @return string
     */
    public static function getDonationStatus($donation ) {
        if ( is_null( $donation ) ) {
            $payment_status = MM_WPFS::PAYMENT_STATUS_UNKNOWN;
        } elseif ( MM_WPFS::STRIPE_CHARGE_STATUS_FAILED === $donation->lastChargeStatus ) {
            $payment_status = MM_WPFS::PAYMENT_STATUS_FAILED;
        } elseif ( MM_WPFS::STRIPE_CHARGE_STATUS_PENDING === $donation->lastChargeStatus ) {
            $payment_status = MM_WPFS::PAYMENT_STATUS_PENDING;
        } elseif ( 1 == $donation->expired ) {
            $payment_status = MM_WPFS::PAYMENT_STATUS_EXPIRED;
        } elseif ( 1 == $donation->refunded ) {
            if ( 1 == $donation->captured ) {
                $payment_status = MM_WPFS::PAYMENT_STATUS_REFUNDED;
            } else {
                $payment_status = MM_WPFS::PAYMENT_STATUS_RELEASED;
            }
        } elseif ( MM_WPFS::STRIPE_CHARGE_STATUS_SUCCEEDED === $donation->lastChargeStatus && 1 == $donation->paid && 1 == $donation->captured ) {
            $payment_status = MM_WPFS::PAYMENT_STATUS_PAID;
        } elseif ( MM_WPFS::STRIPE_CHARGE_STATUS_SUCCEEDED === $donation->lastChargeStatus && 1 == $donation->paid && 0 == $donation->captured ) {
            $payment_status = MM_WPFS::PAYMENT_STATUS_AUTHORIZED;
        } else {
            $payment_status = MM_WPFS::PAYMENT_STATUS_UNKNOWN;
        }

        return $payment_status;
    }

    public static function get_cancellation_count_for_plan( $plan ) {
		$cancellation_count = 0;
		if ( isset( $plan ) && isset( $plan->metadata ) ) {
			if ( isset( $plan->metadata->cancellation_count ) ) {
				if ( is_numeric( $plan->metadata->cancellation_count ) ) {
					$cancellation_count = intval( $plan->metadata->cancellation_count );
				}
			}
		}

		return $cancellation_count;
	}

	/**
	 * @param $form
	 *
	 * @return null|string
	 */
	public static function getFormId( $form ) {
		if ( is_null( $form ) ) {
			return null;
		}
		if ( isset( $form->paymentFormID ) ) {
			return $form->paymentFormID;
		}
		if ( isset( $form->subscriptionFormID ) ) {
			return $form->subscriptionFormID;
		}
		if ( isset( $form->checkoutFormID ) ) {
			return $form->checkoutFormID;
		}
		if ( isset( $form->checkoutSubscriptionFormID ) ) {
			return $form->checkoutSubscriptionFormID;
		}

		return null;
	}

	/**
	 * @param $payment
	 *
	 * @return string
	 */
	public static function get_payment_object_type( $payment ) {
		if ( isset( $payment ) && isset( $payment->eventID ) ) {
			if ( strlen( $payment->eventID ) > 3 ) {
				if ( MM_WPFS::STRIPE_OBJECT_ID_PREFIX_PAYMENT_INTENT === substr( $payment->eventID, 0, 3 ) ) {
					return MM_WPFS::PAYMENT_OBJECT_TYPE_STRIPE_PAYMENT_INTENT;
				} elseif ( MM_WPFS::STRIPE_OBJECT_ID_PREFIX_CHARGE === substr( $payment->eventID, 0, 3 ) ) {
					return MM_WPFS::PAYMENT_OBJECT_TYPE_STRIPE_CHARGE;
				}
			}
		}

		return MM_WPFS::PAYMENT_OBJECT_TYPE_UNKNOWN;
	}

	/**
	 * @param MM_WPFS_Public_PaymentFormModel $paymentFormModel
	 *
	 * @return bool
	 */
	public static function prepareCaptureIntentByFormModel( $paymentFormModel ) {
		if ( MM_WPFS::CHARGE_TYPE_IMMEDIATE === $paymentFormModel->getForm()->chargeType ) {
			$capture = true;
		} elseif ( MM_WPFS::CHARGE_TYPE_AUTHORIZE_AND_CAPTURE === $paymentFormModel->getForm()->chargeType ) {
			$capture = false;
		} else {
			$capture = true;
		}

		return $capture;
	}

	/**
	 * @param \StripeWPFS\Plan $stripePlan
	 *
	 * @return mixed|null
	 */
	public static function get_trial_period_days_for_plan( $stripePlan ) {
		$trialPeriodDays = null;
		if ( $stripePlan instanceof \StripeWPFS\Plan ) {
			if ( isset( $stripePlan->trial_period_days ) ) {
				$trialPeriodDays = $stripePlan->trial_period_days;
			}
		}

		return $trialPeriodDays;
	}

	/**
	 * @param MM_WPFS_Public_FormModel $formModelObject
	 *
	 * @return mixed|string|void
	 */
	public static function generateFormNonce( $formModelObject ) {
		$nonceObject            = new stdClass();
		$nonceObject->created   = time();
		$nonceObject->formHash  = $formModelObject->getFormHash();
		$nonceObject->fieldHash = md5( json_encode( $formModelObject ) );
		$nonceObject->salt      = wp_generate_password( 16, false );

		return json_encode( $nonceObject );
	}

	public static function decodeFormNonce( $text ) {
		$decodedObject = json_decode( $text );

		if ( null === $decodedObject || false === $decodedObject || JSON_ERROR_NONE !== json_last_error() ) {
			return false;
		}

		return $decodedObject;
	}

	public static function encrypt( $message ) {
		$nonce = \Sodium\randombytes_buf( \Sodium\CRYPTO_SECRETBOX_NONCEBYTES );

		$encodedMessage = base64_encode(
			$nonce . \Sodium\crypto_secretbox(
				$message,
				$nonce,
				self::getEncryptionKey()
			)
		);

		return $encodedMessage;
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	private static function getEncryptionKey() {
		$desiredKeyLength = 32;
		if ( strlen( NONCE_KEY ) == $desiredKeyLength ) {
			return NONCE_KEY;
		} elseif ( strlen( NONCE_KEY ) > $desiredKeyLength ) {
			return substr( NONCE_KEY, 0, 32 );
		} else {
			throw new Exception( 'WordPress Constant NONCE_KEY is too short' );
		}
	}

	public static function decrypt( $secretMessage ) {
		$decodedMessage   = base64_decode( $secretMessage );
		$nonce            = mb_substr( $decodedMessage, 0, \Sodium\CRYPTO_SECRETBOX_NONCEBYTES, '8bit' );
		$encryptedMessage = mb_substr( $decodedMessage, \Sodium\CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit' );
		$decryptedMessage = \Sodium\crypto_secretbox_open( $encryptedMessage, $nonce, self::getEncryptionKey() );

		return $decryptedMessage;
	}

    /**
     * This function is the exact copy of wp_timezone_string() of Wordpress.
     * We had to copy it here because it's available only since v5.3.0 .
     *
     * @return string
     */
    public static function getWordpressTimezone() {
        $timezone_string = get_option( 'timezone_string' );

        if ( $timezone_string ) {
            return $timezone_string;
        }

        $offset  = (float) get_option( 'gmt_offset' );
        $hours   = (int) $offset;
        $minutes = ( $offset - $hours );

        $sign      = ( $offset < 0 ) ? '-' : '+';
        $abs_hour  = abs( $hours );
        $abs_mins  = abs( $minutes * 60 );
        $tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );

        return $tz_offset;
    }


    public static function calculateTrialEndFromNow($trialDays ) {
        $currentTimestamp     = time();
        $oneDayInSeconds      = 24 * 60 * 60;

        return $currentTimestamp + $trialDays * $oneDayInSeconds;
    }

    public static function calculateBillingCycleAnchorFromNow( $billingCycleAnchorDay ) {
        return self::calculateBillingCycleAnchorFromTimestamp( $billingCycleAnchorDay, time() );
    }

    public static function calculateBillingCycleAnchorFromTimestamp( $billingCycleAnchorDay, $startingTimestamp ) {
        $oneDayInSeconds      = 24 * 60 * 60;

        // We save the default timezone because we'll restore it after our calculations
        $defaultTz            = date_default_timezone_get();
        $userTz               = self::getWordpressTimezone();

        date_default_timezone_set( $userTz );
        $currentDayOfMonth    = date("d", $startingTimestamp);
        $numDaysInMonth       = date("t", $startingTimestamp);
        date_default_timezone_set( $defaultTz );

        $billingAnchorTimestamp = null;
        if ( $billingCycleAnchorDay >= $currentDayOfMonth ) {
            $billingAnchorTimestamp = $startingTimestamp + ( $billingCycleAnchorDay - $currentDayOfMonth ) * $oneDayInSeconds;
        } else {
            $billingAnchorTimestamp = $startingTimestamp + ( $numDaysInMonth - $currentDayOfMonth + $billingCycleAnchorDay ) * $oneDayInSeconds;
        }


        return $billingAnchorTimestamp;
    }

    public static function generateDonationAmountsLabel( $donationForm ) {
        $donationAmounts      = json_decode( $donationForm->donationAmounts );
        $donationAmountsLabel = '';

        if ( json_last_error() == JSON_ERROR_NONE ) {
            for ( $idx = 0; $idx < count( $donationAmounts ); $idx++ ) {
                $donationAmount = (int)$donationAmounts[ $idx ];
                $donationAmountsLabel .= MM_WPFS_Currencies::formatAndEscapeByAdmin( $donationForm->currency, $donationAmount, true, true );

                if ( $idx != count( $donationAmounts ) - 1 ) {
                    $donationAmountsLabel .= ", ";
                }
            }
        }
        if ( $donationForm->allowCustomDonationAmount == 1 ) {
            $donationAmountsLabel .= ", custom";
        }

        return $donationAmountsLabel;
    }

    public static function decodeJsonArray( $arr ) {
	    $res = json_decode( $arr );
        if ( json_last_error() != JSON_ERROR_NONE ) {
            // todo: Log the json decode error
            $res = array();
        }

        return $res;
    }

    /**
     * @param $plan \StripeWPFS\Plan
     *
     * @return boolean
     */
    public static function isDonationPlan( $plan ) {
        return strpos($plan->id, MM_WPFS::DONATION_PLAN_ID_PREFIX) === 0;
    }
}

/**
 * @deprecated
 * Class ValidationResult
 */
class ValidationResult {
	/** @var bool */
	protected $valid = true;
	/** @var  string */
	protected $message;

	/**
	 * @return boolean
	 */
	public function isValid() {
		return $this->valid;
	}

	/**
	 * @param boolean $valid
	 */
	public function setValid( $valid ) {
		$this->valid = $valid;
	}

	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @param string $message
	 */
	public function setMessage( $message ) {
		$this->message = $message;
	}

}

abstract class MacroReplacer {
    /** @var MM_WPFS_TransactionData */
    protected $transactionData;
    /** @var stdClass */
    protected $form;
    /** @var array */
    protected $keyValuePairs;

    function __construct( $form, $transactionData ) {
        $this->form             = $form;
        $this->transactionData  = $transactionData;

        $this->initKeyValuePairs();
    }

    protected function initKeyValuePairs() {
        $siteTitle      = get_bloginfo( 'name' );
        $dateFormat     = get_option( 'date_format' );
        $billingAddress = $this->transactionData->getBillingAddress();
        $shippingAddress = $this->transactionData->getShippingAddress();

        $keyValuePairs = array(
            '%NAME%'		            => $siteTitle,
            '%CUSTOMERNAME%'		    => $this->transactionData->getCustomerName(),
            '%CUSTOMER_EMAIL%'		    => $this->transactionData->getCustomerEmail(),
            '%BILLING_NAME%'            => $this->transactionData->getBillingName(),
            '%ADDRESS1%'                => $billingAddress['line1'],
            '%ADDRESS2%'                => $billingAddress['line2'],
            '%CITY%'                    => $billingAddress['city'],
            '%STATE%'                   => $billingAddress['state'],
            '%COUNTRY%'                 => $billingAddress['country'],
            '%COUNTRY_CODE%'            => $billingAddress['country_code'],
            '%ZIP%'                     => $billingAddress['zip'],
            '%SHIPPING_NAME%'           => $this->transactionData->getShippingName(),
            '%SHIPPING_ADDRESS1%'       => $shippingAddress['line1'],
            '%SHIPPING_ADDRESS2%'       => $shippingAddress['line2'],
            '%SHIPPING_CITY%'           => $shippingAddress['city'],
            '%SHIPPING_STATE%'          => $shippingAddress['state'],
            '%SHIPPING_COUNTRY%'        => $shippingAddress['country'],
            '%SHIPPING_COUNTRY_CODE%'   => $shippingAddress['country_code'],
            '%SHIPPING_ZIP%'            => $shippingAddress['zip'],
            '%DATE%'                    => date( $dateFormat ),
            '%FORM_NAME%'               => $this->transactionData->getFormName(),
            '%TRANSACTION_ID%'          => $this->transactionData->getTransactionId()
        );

        $customInputKeyValuePairs = $this->getCustomInputKeyValuePairs();

        $this->keyValuePairs = array_merge( $keyValuePairs, $customInputKeyValuePairs );
    }


    private function createCustomPlaceHolderKey( $idx ) {
        return "%CUSTOMFIELD" . $idx . "%";
    }

    private function getCustomInputKeyValuePairs() {
        $keyValuePairs            = array();
        $customInputValues        = $this->transactionData->getCustomInputValues();
        $customInputFieldMaxCount = MM_WPFS::get_custom_input_field_max_count();

        if ( isset( $customInputValues ) && is_array( $customInputValues ) ) {
            $customInputValueCount = count( $customInputValues );

            for ( $idx = 0; $idx < $customInputFieldMaxCount; $idx++ ) {
                $key = $this->createCustomPlaceHolderKey( $idx+1 );

                if ( $idx < $customInputValueCount ) {
                    $value = $customInputValues[ $idx ];
                } else {
                    $value = '';
                }
                $customInputElement = array( $key => $value  );

                $keyValuePairs = array_merge( $keyValuePairs, $customInputElement );
            }
        } else {
            for ( $idx = 0; $idx < $customInputFieldMaxCount; $idx++ ) {
                $key = $this->createCustomPlaceHolderKey( $idx+1 );
                $customInputElement = array( $key => '' );

                $keyValuePairs = array_merge( $keyValuePairs, $customInputElement );
            }
        }

        return $keyValuePairs;
    }

    protected function getMacroKeys() {
        return array_keys( $this->keyValuePairs );
    }

    protected function getMacroValues() {
        return array_values( $this->keyValuePairs );
    }

    protected function replaceMacrosWithEscape($template, $escapeType ) {
        $keys 	= $this->getMacroKeys();
        $values = $this->getMacroValues();

        $escapedValues = array();
        foreach ( $values as $value ) {
            array_push( $escapedValues, MM_WPFS_Utils::escape( $value, $escapeType ));
        }

        $template = str_replace( $keys, $escapedValues, $template );

        return $template;
    }

    public function replaceMacrosWithHtmlEscape( $template ) {
        return $this->replaceMacrosWithEscape( $template, MM_WPFS_Utils::ESCAPE_TYPE_HTML );
    }

    public function replaceMacrosWithAttributeEscape( $template ) {
        return $this->replaceMacrosWithEscape( $template, MM_WPFS_Utils::ESCAPE_TYPE_ATTR );
    }

    public function replaceMacros( $template ) {
        return $this->replaceMacrosWithEscape( $template, MM_WPFS_Utils::ESCAPE_TYPE_NONE );
    }
}

class DonationMacroReplacer extends MacroReplacer {
    /**
     * @param $form array
     * @param $transactionData MM_WPFS_DonationTransactionData
     */
    function __construct( $form, $transactionData ) {
        parent::__construct( $form, $transactionData );
    }

    protected function initKeyValuePairs() {
        parent::initKeyValuePairs();

        $keyValuePairs = array(
            '%AMOUNT%'		            => MM_WPFS_Currencies::formatAndEscapeByForm( $this->form,
                                                                                      $this->transactionData->getCurrency(),
                                                                                      $this->transactionData->getAmount() )
        );

        $this->keyValuePairs = array_merge( $this->keyValuePairs, $keyValuePairs );
    }
}


MM_WPFS::getInstance();
