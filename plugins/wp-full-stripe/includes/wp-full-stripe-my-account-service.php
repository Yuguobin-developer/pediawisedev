<?php

/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2018.04.10.
 * Time: 14:06
 */
class MM_WPFS_CardUpdateService {

	const PARAM_CARD_UPDATE_SESSION = 'wpfs-card-update-session';
	const PARAM_CARD_UPDATE_SECURITY_CODE = 'wpfs-security-code';
	const COOKIE_NAME_WPFS_CARD_UPDATE_SESSION_ID = 'WPFS_CARD_UPDATE_SESSION_ID';
	const TEMPLATE_ENTER_EMAIL_ADDRESS = 'enter-email-address';
	const TEMPLATE_ENTER_SECURITY_CODE = 'enter-security-code';
	const TEMPLATE_INVALID_SESSION = 'invalid-session';
	const TEMPLATE_MANAGE_SUBSCRIPTIONS = 'manage-subscriptions';
	const SESSION_STATUS_WAITING_FOR_CONFIRMATION = 'waiting_for_confirmation';
	const SESSION_STATUS_CONFIRMED = 'confirmed';
	const SESSION_STATUS_INVALIDATED = 'invalidated';
	const SECURITY_CODE_STATUS_PENDING = 'pending';
	const SECURITY_CODE_STATUS_SENT = 'sent';
	const SECURITY_CODE_STATUS_CONSUMED = 'consumed';
	const SECURITY_CODE_REQUEST_LIMIT = 5;
	const SECURITY_CODE_INPUT_LIMIT = 5;
	const COOKIE_ACTION_SET = 'set';
	const COOKIE_ACTION_REMOVE = 'remove';
	const CARD_UPDATE_SESSION_VALID_UNTIL_HOURS = 1;
	const URL_RECAPTCHA_API_SITEVERIFY = 'https://www.google.com/recaptcha/api/siteverify';
	const SOURCE_GOOGLE_RECAPTCHA_V2_API_JS = 'https://www.google.com/recaptcha/api.js';
	const ASSET_DIR_MY_ACCOUNT = 'my-account';
	const ASSET_ENTER_EMAIL_ADDRESS_PHP = 'enter-email-address.php';
	const ASSET_INVALID_SESSION_PHP = 'invalid-session.php';
	const ASSET_MY_ACCOUNT_PHP = 'my-account.php';
	const ASSET_ENTER_SECURITY_CODE_PHP = 'enter-security-code.php';
	const ASSET_WPFS_MANAGE_SUBSCRIPTIONS_CSS = 'wpfs-manage-subscriptions.css';
	const ASSET_WPFS_MANAGE_SUBSCRIPTIONS_JS = 'wpfs-manage-subscriptions.js';
	const CARD_AMERICAN_EXPRESS = 'Amex';
	const CARD_DINERS_CLUB = 'Diners';
	const CARD_DISCOVER = 'Discover';
	const CARD_JCB = 'JCB';
	const CARD_MASTERCARD = 'MasterCard';
	const CARD_UNIONPAY = 'UnionPay';
	const CARD_VISA = 'Visa';
	const CARD_UNKNOWN = 'Unknown';
	const PARAM_WPFS_SUBSCRIPTION_ID = 'wpfs-subscription-id';
	const PARAM_EMAIL_ADDRESS = 'emailAddress';
	const PARAM_GOOGLE_RE_CAPTCHA_RESPONSE = 'googleReCAPTCHAResponse';
	const FULLSTRIPE_SHORTCODE_MANAGE_SUBSCRIPTIONS = 'fullstripe_manage_subscriptions';
	const FULLSTRIPE_SHORTCODE_SUBSCRIPTION_UPDATE = 'fullstripe_subscription_update';

	const JS_VARIABLE_AJAX_URL = 'wpfsAjaxURL';
	const JS_VARIABLE_REST_URL = 'wpfsRESTURL';
	const JS_VARIABLE_STRIPE_KEY = 'wpfsStripeKey';
	const JS_VARIABLE_GOOGLE_RECAPTCHA_SITE_KEY = 'wpfsGoogleReCAPTCHASiteKey';

	const SHORTCODE_ATTRIBUTE_NAME_AUTHENTICATION = 'authentication';
	const SHORTCODE_ATTRIBUTE_VALUE_AUTHENTICATION = 'Wordpress';

	const HANDLE_STRIPE_JS_V_3 = 'stripe-js-v3';
	const HANDLE_GOOGLE_RECAPTCHA_V_2 = 'google-recaptcha-v2';
	const HANDLE_MANAGE_SUBSCRIPTIONS_CSS = 'wpfs-manage-subscriptions-css';
	const HANDLE_MANAGE_SUBSCRIPTIONS_JS = 'wpfs-manage-subscriptions-js';

	const WPFS_PLUGIN_SLUG = 'wp-full-stripe';
	const WPFS_REST_API_VERSION = 'v1';
	const WPFS_REST_ROUTE_MANAGE_SUBSCRIPTIONS_SUBSCRIPTION = 'manage-subscriptions/subscription';

	const INVOICE_DISPLAY_HEAD_LIMIT = 5;

	const INVOICE_DISPLAY_MODE_FEW = 0;
	const INVOICE_DISPLAY_MODE_HEAD = 1;
	const INVOICE_DISPLAY_MODE_ALL = 2;

	/* @var bool */
	private $debugLog = false;
	/* @var MM_WPFS_LoggerService */
	private $loggerService = null;
	/* @var MM_WPFS_Logger */
	private $logger = null;
	/* @var $db MM_WPFS_Database */
	private $db = null;
	/* @var $stripe MM_WPFS_Payment_API */
	private $stripe = null;
	/* @var $mailer MM_WPFS_Mailer */
	private $mailer = null;

	public function __construct() {
		$this->setup();
		$this->hooks();
	}

	private function setup() {
		$this->db            = new MM_WPFS_Database();
		$this->stripe        = new MM_WPFS_Stripe();
		$this->mailer        = new MM_WPFS_Mailer();
		$this->loggerService = new MM_WPFS_LoggerService();
		$this->logger        = $this->loggerService->createManageCardsAndSubscriptionsLogger( MM_WPFS_CardUpdateService::class );
	}

	private function hooks() {

		add_shortcode( self::FULLSTRIPE_SHORTCODE_MANAGE_SUBSCRIPTIONS, array( $this, 'renderShortCode' ) );
		add_shortcode( self::FULLSTRIPE_SHORTCODE_SUBSCRIPTION_UPDATE, array( $this, 'renderShortCode' ) );

		add_action( 'fullstripe_check_card_update_sessions', array(
			$this,
			'checkCardUpdateSessionsAndCodes'
		) );

		add_action( 'wp_ajax_wp_full_stripe_create_card_update_session', array(
			$this,
			'handleCardUpdateSessionRequest'
		) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_create_card_update_session', array(
			$this,
			'handleCardUpdateSessionRequest'
		) );
		add_action( 'wp_ajax_wp_full_stripe_reset_card_update_session', array(
			$this,
			'handleResetCardUpdateSessionRequest'
		) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_reset_card_update_session', array(
			$this,
			'handleResetCardUpdateSessionRequest'
		) );
		add_action( 'wp_ajax_wp_full_stripe_validate_security_code', array(
			$this,
			'handleSecurityCodeValidationRequest'
		) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_validate_security_code', array(
			$this,
			'handleSecurityCodeValidationRequest'
		) );
		add_action( 'wp_ajax_wp_full_stripe_update_card', array(
			$this,
			'handleCardUpdateRequest'
		) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_update_card', array(
			$this,
			'handleCardUpdateRequest'
		) );
		add_action( 'wp_ajax_wp_full_stripe_cancel_my_subscription', array(
			$this,
			'handleSubscriptionCancellationRequest'
		) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_cancel_my_subscription', array(
			$this,
			'handleSubscriptionCancellationRequest'
		) );
		add_action( 'wp_ajax_wp_full_stripe_toggle_invoice_view', array(
			$this,
			'toggleInvoiceView'
		) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_toggle_invoice_view', array(
			$this,
			'toggleInvoiceView'
		) );

		// tnagy register REST API Endpoint for Manage Subscriptions
		add_action( 'rest_api_init', array( $this, 'registerRESTAPIRoutes' ) );

		// tnagy WPFS-861: prevent caching of pages generated by the shortcode
		add_action( 'send_headers', array( $this, 'addCacheControlHeader' ), 10, 1 );

		add_filter( 'script_loader_tag', array( $this, 'addAsyncDeferAttributes' ), 10, 2 );

	}

	public static function onActivation() {
		if ( ! wp_next_scheduled( 'fullstripe_check_card_update_sessions' ) ) {
			wp_schedule_event( time(), 'hourly', 'fullstripe_check_card_update_sessions' );
		}
	}

	public static function onDeactivation() {
		wp_clear_scheduled_hook( 'fullstripe_check_card_update_sessions' );
	}

	public function checkCardUpdateSessionsAndCodes() {
		try {

			// tnagy invalidate expired sessions
			$this->db->invalidate_expired_card_update_sessions( self::CARD_UPDATE_SESSION_VALID_UNTIL_HOURS );

			// tnagy invalidate sessions where security code request and security code input limits reached
			$this->db->invalidate_card_update_sessions_by_security_code_request_limit( self::SECURITY_CODE_REQUEST_LIMIT );
			$this->db->invalidate_card_update_sessions_by_security_code_input_limit( self::SECURITY_CODE_INPUT_LIMIT );

			// tnagy remove invalidated sessions
			$invalidatedSessionIdObjects = $this->db->find_invalidated_session_ids();
			$invalidatedSessionIds       = array_map( function ( $o ) {
				return $o->id;
			}, $invalidatedSessionIdObjects );
			if ( isset( $invalidatedSessionIds ) && sizeof( $invalidatedSessionIds ) > 0 ) {
				$this->db->delete_security_codes_by_sessions( $invalidatedSessionIds );
				$this->db->delete_invalidated_card_update_sessions( $invalidatedSessionIds );
			}

		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$this->logger->error( __FUNCTION__, $e->getMessage(), $e );
		}
	}

	public function renderShortCode( $attributes ) {

		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'renderShortCode(): COOKIE=' . print_r( $_COOKIE, true ) );
		}

		if ( self::isWordpressAuthenticationNeeded( $attributes ) ) {
			$content           = null;
			$cookieAction      = null;
			$cardUpdateSession = null;

			$cardUpdateSessionHash = $this->findCardUpdateSessionCookieValue();
			if ( ! is_null( $cardUpdateSessionHash ) ) {
				$cardUpdateSession = $this->findCardUpdateSessionByHash( $cardUpdateSessionHash );
				if ( ! $this->isConfirmed( $cardUpdateSession ) ) {
					$cardUpdateSession = null;
				}
			}

			if ( is_user_logged_in() ) {
				$stripeCustomer = null;
				$createSession  = false;

				$user = wp_get_current_user();

				if ( ! is_null( $cardUpdateSession ) ) {
					if ( $user->user_email !== $cardUpdateSession->email ) {
						$this->invalidate( $cardUpdateSession );

						$stripeCustomer = MM_WPFS_Utils::find_existing_stripe_customer_anywhere_by_email(
							$this->db,
							$this->stripe,
							$user->user_email
						);
						if ( ! is_null( $stripeCustomer ) ) {
							$createSession = true;
						}
						$cardUpdateSession = null;
					} else {
						$stripeCustomer = MM_WPFS_Utils::find_existing_stripe_customer_anywhere_by_email(
							$this->db,
							$this->stripe,
							$cardUpdateSession->email
						);
						$createSession  = false;
					}
				} else {
					$stripeCustomer = MM_WPFS_Utils::find_existing_stripe_customer_anywhere_by_email(
						$this->db,
						$this->stripe,
						$user->user_email
					);
					if ( ! is_null( $stripeCustomer ) ) {
						$createSession = true;
					}
				}

				if ( $createSession ) {
					$cardUpdateSession = $this->createCardUpdateSessionForWPUser( $user, $stripeCustomer );
					$this->confirmCardUpdateSession( $cardUpdateSession );
					$cookieAction = self::COOKIE_ACTION_SET;
				}

				if ( ! is_null( $cardUpdateSession ) ) {
					$model = new MM_WPFS_CardUpdateModel();
					$model->setAuthenticationType( MM_WPFS_CardUpdateModel::AUTHENTICATION_TYPE_WORDPRESS );
					$this->fetchDataIntoCardUpdateModel( $model, $stripeCustomer );

					$this->enqueueCardUpdateScript( $cookieAction, $cardUpdateSession->hash, $model );

					$content = $this->renderCardsAndSubscriptionsTable( $attributes, $model );
				} else {
					$content = __( 'You haven\'t made any payments yet', MM_WPFS::L10N_DOMAIN_PUBLIC );
				}
			} else {
				if ( ! is_null( $cardUpdateSession ) ) {
					$this->invalidate( $cardUpdateSession );

					$cookieAction = self::COOKIE_ACTION_REMOVE;
					$this->enqueueCardUpdateScript( $cookieAction, $cardUpdateSession->hash, null );
				}

				$content = "You are not logged in";
			}
		} else {
			$cardUpdateSession = null;
			$cookieAction      = null;

			// tnagy pick up session by cookie
			$cardUpdateSessionHash = $this->findCardUpdateSessionCookieValue();
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'renderShortCode(): $cardUpdateSessionHash by cookie=' . $cardUpdateSessionHash );
			}
			if ( ! is_null( $cardUpdateSessionHash ) ) {
				$cardUpdateSession = $this->findCardUpdateSessionByHash( $cardUpdateSessionHash );
				if ( ! is_null( $cardUpdateSession ) ) {
					$isWaitingForConfirmation = $this->isWaitingForConfirmation( $cardUpdateSession );
					$isConfirmed              = $this->isConfirmed( $cardUpdateSession );
					if ( ! $isWaitingForConfirmation && ! $isConfirmed ) {
						$cardUpdateSession = null;
						$cookieAction      = self::COOKIE_ACTION_REMOVE;
					} elseif ( $isWaitingForConfirmation ) {
						$securityCode = $this->findSecurityCodeByRequest();
						if ( $this->debugLog ) {
							MM_WPFS_Utils::log( 'renderShortCode(): cardUpdateSessionHash=' . $cardUpdateSessionHash . ', securityCode=' . $securityCode );
						}
						if ( ! is_null( $securityCode ) ) {
							if ( ! is_null( $cardUpdateSession ) && $this->isWaitingForConfirmation( $cardUpdateSession ) && ! $this->securityCodeInputExhausted( $cardUpdateSession ) ) {
								$this->incrementSecurityCodeInput( $cardUpdateSession );
								$validationResult     = $this->validateSecurityCode( $cardUpdateSession, $securityCode );
								$valid                = $validationResult['valid'];
								$matchingSecurityCode = $validationResult['securityCode'];
								if ( $valid ) {
									$this->confirmCardUpdateSessionWithSecurityCode( $cardUpdateSession, $matchingSecurityCode );
									// tnagy reload session after update
									$cardUpdateSession = $this->findCardUpdateSessionByHash( $cardUpdateSessionHash );
									$cookieAction      = self::COOKIE_ACTION_SET;
								} else {
									$cardUpdateSession = null;
									$cookieAction      = self::COOKIE_ACTION_REMOVE;
								}
							}
						}
					}
				}
			}

			// tnagy check request parameters to pick up existing card update session
			if ( is_null( $cardUpdateSession ) ) {
				$cardUpdateSessionHash = $this->findSessionHashByRequest();
				$securityCode          = $this->findSecurityCodeByRequest();

				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( 'renderShortCode(): cardUpdateSessionHash=' . $cardUpdateSessionHash . ', securityCode=' . $securityCode );
				}

				if ( ! is_null( $cardUpdateSessionHash ) && ! is_null( $securityCode ) ) {
					$cardUpdateSession = $this->findCardUpdateSessionByHash( $cardUpdateSessionHash );
					if ( ! is_null( $cardUpdateSession ) && $this->isWaitingForConfirmation( $cardUpdateSession ) && ! $this->securityCodeInputExhausted( $cardUpdateSession ) ) {
						$this->incrementSecurityCodeInput( $cardUpdateSession );
						$validationResult     = $this->validateSecurityCode( $cardUpdateSession, $securityCode );
						$valid                = $validationResult['valid'];
						$matchingSecurityCode = $validationResult['securityCode'];
						if ( $valid ) {
							$this->confirmCardUpdateSessionWithSecurityCode( $cardUpdateSession, $matchingSecurityCode );
							// tnagy reload session after update
							$cardUpdateSession = $this->findCardUpdateSessionByHash( $cardUpdateSessionHash );
							$cookieAction      = self::COOKIE_ACTION_SET;
						} else {
							$cardUpdateSession = null;
							$cookieAction      = self::COOKIE_ACTION_REMOVE;
						}
					}
				}
			}

			$model = new MM_WPFS_CardUpdateModel();
			$model->setAuthenticationType( MM_WPFS_CardUpdateModel::AUTHENTICATION_TYPE_PLUGIN );

			if ( is_null( $cardUpdateSession ) ) {
				$templateToShow = self::TEMPLATE_ENTER_EMAIL_ADDRESS;
			} elseif ( $this->isWaitingForConfirmation( $cardUpdateSession ) ) {
				$templateToShow = self::TEMPLATE_ENTER_SECURITY_CODE;
			} elseif ( $this->isConfirmed( $cardUpdateSession ) ) {
				$templateToShow = self::TEMPLATE_MANAGE_SUBSCRIPTIONS;
			} else {
				$templateToShow = self::TEMPLATE_ENTER_EMAIL_ADDRESS;
			}

			if ( self::TEMPLATE_ENTER_EMAIL_ADDRESS === $templateToShow ) {
				$this->enqueueCardUpdateScript( $cookieAction, is_null( $cardUpdateSession ) ? null : $cardUpdateSession->hash, $model );
				$content = $this->renderEmailForm( $attributes );
			} elseif ( self::TEMPLATE_ENTER_SECURITY_CODE === $templateToShow ) {
				$this->enqueueCardUpdateScript( $cookieAction, is_null( $cardUpdateSession ) ? null : $cardUpdateSession->hash, $model );
				$content = $this->renderSecurityCodeForm( $attributes );
			} elseif ( self::TEMPLATE_MANAGE_SUBSCRIPTIONS === $templateToShow ) {
				$stripeCustomer = MM_WPFS_Utils::find_existing_stripe_customer_anywhere_by_email(
					$this->db,
					$this->stripe,
					$cardUpdateSession->email
				);

				$this->fetchDataIntoCardUpdateModel( $model, $stripeCustomer );

				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( 'renderShortCode(): model=' . print_r( $model, true ) );
				}
				$this->enqueueCardUpdateScript( $cookieAction, is_null( $cardUpdateSession ) ? null : $cardUpdateSession->hash, $model );
				$content = $this->renderCardsAndSubscriptionsTable( $attributes, $model );
			} else {
				$content = $this->renderInvalidCardUpdateSession( $attributes );
			}
		}

		return $content;
	}

	private static function isWordpressAuthenticationNeeded( $shortcode_attributes ) {
		$res = false;

		if ( isset( $shortcode_attributes ) && is_array( $shortcode_attributes ) ) {
			if ( array_key_exists( self::SHORTCODE_ATTRIBUTE_NAME_AUTHENTICATION, $shortcode_attributes ) &&
			     $shortcode_attributes[ self::SHORTCODE_ATTRIBUTE_NAME_AUTHENTICATION ] === self::SHORTCODE_ATTRIBUTE_VALUE_AUTHENTICATION
			) {
				$res = true;
			}
		}

		return $res;
	}

	/**
	 * @return null|string
	 */
	private function findCardUpdateSessionCookieValue() {
		return isset( $_COOKIE[ self::COOKIE_NAME_WPFS_CARD_UPDATE_SESSION_ID ] ) ? sanitize_text_field( $_COOKIE[ self::COOKIE_NAME_WPFS_CARD_UPDATE_SESSION_ID ] ) : null;
	}

	/**
	 * @param $cardUpdateSessionHash
	 *
	 * @return array|null|object
	 */
	private function findCardUpdateSessionByHash( $cardUpdateSessionHash ) {
		return $this->db->find_card_update_session_by_hash( $cardUpdateSessionHash );
	}

	/**
	 * @param $cardUpdateSession
	 *
	 * @return bool
	 */
	private function isConfirmed( $cardUpdateSession ) {
		if ( isset( $cardUpdateSession ) && isset( $cardUpdateSession->status ) ) {
			return self::SESSION_STATUS_CONFIRMED === $cardUpdateSession->status;
		} else {
			return false;
		}
	}

	/**
	 * @param $cardUpdateSession
	 */
	private function invalidate( $cardUpdateSession ) {
		$this->db->update_card_update_session( $cardUpdateSession->id, array( 'status' => self::SESSION_STATUS_INVALIDATED ) );
	}

	/**
	 * @param $wpUser
	 * @param $stripeCustomer
	 *
	 * @return null
	 */
	private function createCardUpdateSessionForWPUser( $wpUser, $stripeCustomer ) {
		$options           = get_option( 'fullstripe_options' );
		$liveMode          = $options['apiMode'] === 'live';
		$cardUpdateSession = $this->createCardUpdateSession( $wpUser->user_email, $liveMode, $stripeCustomer->id );

		return $cardUpdateSession;
	}

	public function createCardUpdateSession( $stripeCustomerEmail, $liveMode, $stripeCustomerId ) {

		$salt = wp_generate_password( 16, false );
		$data = time() . '|' . $stripeCustomerEmail . '|' . $liveMode . '|' . $stripeCustomerId . '|' . $salt;

		$cardUpdateSessionHash = hash( 'sha256', $data );

		$insertResult = $this->db->insert_card_update_session( $stripeCustomerEmail, $liveMode, $stripeCustomerId, $cardUpdateSessionHash );

		if ( $insertResult !== - 1 ) {
			return $this->findValidCardUpdateSessionById( $insertResult );
		}

		return null;
	}

	private function findValidCardUpdateSessionById( $cardUpdateSessionId ) {
		$cardUpdateSessions = $this->db->find_card_update_sessions_by_id( $cardUpdateSessionId );

		$validCardUpdateSession = null;
		if ( isset( $cardUpdateSessions ) ) {
			foreach ( $cardUpdateSessions as $cardUpdateSession ) {
				if ( is_null( $validCardUpdateSession ) && ! $this->isInvalidated( $cardUpdateSession ) ) {
					$validCardUpdateSession = $cardUpdateSession;
				}
			}
		}

		return $validCardUpdateSession;
	}

	/**
	 * @param $cardUpdateSession
	 *
	 * @return bool
	 */
	private function isInvalidated( $cardUpdateSession ) {
		if ( isset( $cardUpdateSession ) && isset( $cardUpdateSession->status ) ) {
			return self::SESSION_STATUS_INVALIDATED === $cardUpdateSession->status;
		} else {
			return false;
		}
	}

	/**
	 * @param $cardUpdateSession
	 *
	 * @return false|int
	 */
	private function confirmCardUpdateSession( $cardUpdateSession ) {
		return $this->db->update_card_update_session( $cardUpdateSession->id, array( 'status' => self::SESSION_STATUS_CONFIRMED ) );
	}

	/**
	 * @param MM_WPFS_CardUpdateModel $model
	 * @param \StripeWPFS\Customer $stripeCustomer
	 */
	private function fetchDataIntoCardUpdateModel( $model, $stripeCustomer ) {
		/**
		 * @var null|\StripeWPFS\PaymentMethod
		 */
		$defaultPaymentMethod = null;
		/**
		 * @var null|\StripeWPFS\Source
		 */
		$defaultSource = null;
		if ( isset( $stripeCustomer ) && $stripeCustomer instanceof \StripeWPFS\Customer ) {
			$model->setStripeCustomer( $stripeCustomer );
			$paymentMethods = \StripeWPFS\PaymentMethod::all(
				array(
					'customer' => $stripeCustomer->id,
					'type'     => 'card'
				)
			);
			if ( isset( $paymentMethods ) && isset( $paymentMethods->data ) ) {
				foreach ( $paymentMethods->data as $paymentMethod ) {
					if ( is_null( $defaultPaymentMethod ) ) {
						if ( 'card' === $paymentMethod->type && $paymentMethod->id == $stripeCustomer->invoice_settings->default_payment_method ) {
							$defaultPaymentMethod = $paymentMethod;
						}
					}
				}
			}
			if ( isset( $stripeCustomer->sources ) && isset( $stripeCustomer->sources->data ) ) {
				foreach ( $stripeCustomer->sources->data as $source ) {
					if ( is_null( $defaultSource ) ) {
						if ( $source->object == 'card' && $source->id == $stripeCustomer->default_source ) {
							$defaultSource = $source;
						}
					}
				}
			}
		}
		if ( isset( $defaultPaymentMethod ) ) {
			$model->setDefaultPaymentMethod( $defaultPaymentMethod );
		}
		if ( isset( $defaultSource ) ) {
			$model->setDefaultSource( $defaultSource );
		}
		$card = $this->getCurrentCard( $model );
		$this->updateModelWithCard( $model, $card );
		$model->setSubscriptions( $this->prepareSubscriptions( $stripeCustomer ) );
		$model->setInvoices( $this->prepareInvoices( $stripeCustomer ) );
	}

	/**
	 * @param MM_WPFS_CardUpdateModel $model
	 *
	 * @return mixed
	 */
	private function getCurrentCard( $model ) {
		$card = null;
		if ( ! is_null( $model->getDefaultPaymentMethod() ) ) {
			$card = $model->getDefaultPaymentMethod()->card;
		} elseif ( ! is_null( $model->getDefaultSource() ) ) {
			$card = $model->getDefaultSource();
		}

		return $card;

	}

	/**
	 * @param MM_WPFS_CardUpdateModel $model
	 * @param \StripeWPFS\Card|\StripeWPFS\Source $card
	 */
	private function updateModelWithCard( $model, $card ) {
		if ( ! is_null( $card ) ) {
			$model->setCardNumber( $card->last4 );
			if ( $this->isAmericanExpress( $card ) ) {
				$model->setCardName( self::CARD_AMERICAN_EXPRESS );
				$model->setCardImageUrl( MM_WPFS_Assets::images( 'amex.png' ) );
			} elseif ( $this->isDinersClub( $card ) ) {
				$model->setCardName( self::CARD_DINERS_CLUB );
				$model->setCardImageUrl( MM_WPFS_Assets::images( 'diners-club.png' ) );
			} elseif ( $this->isDiscover( $card ) ) {
				$model->setCardName( self::CARD_DISCOVER );
				$model->setCardImageUrl( MM_WPFS_Assets::images( 'discover.png' ) );
			} elseif ( $this->isJCB( $card ) ) {
				$model->setCardName( self::CARD_JCB );
				$model->setCardImageUrl( MM_WPFS_Assets::images( 'jcb.png' ) );
			} elseif ( $this->isMasterCard( $card ) ) {
				$model->setCardName( self::CARD_MASTERCARD );
				$model->setCardImageUrl( MM_WPFS_Assets::images( 'mastercard.png' ) );
			} elseif ( $this->isUnionPay( $card ) ) {
				$model->setCardName( self::CARD_UNIONPAY );
				$model->setCardImageUrl( MM_WPFS_Assets::images( 'unionpay.png' ) );
			} elseif ( $this->isVisa( $card ) ) {
				$model->setCardName( self::CARD_VISA );
				$model->setCardImageUrl( MM_WPFS_Assets::images( 'visa.png' ) );
			} elseif ( $this->isUnknownCard( $card ) ) {
				$model->setCardName( self::CARD_UNKNOWN );
				$model->setCardImageUrl( MM_WPFS_Assets::images( 'generic.png' ) );
			} else {
				$model->setCardName( self::CARD_UNKNOWN );
				$model->setCardImageUrl( MM_WPFS_Assets::images( 'generic.png' ) );
			}
		}
	}

	/**
	 * @param \StripeWPFS\Card $card
	 *
	 * @return bool
	 */
	private function isAmericanExpress( $card ) {
		return $this->isBrandOf( self::CARD_AMERICAN_EXPRESS, $card->brand );
	}

	/**
	 * Checks if a \StripeWPFS\Card's brand is matching a known brand stripped and lowercased.
	 *
	 * @param $knownBrand
	 * @param $currentBrand
	 *
	 * @return bool
	 */
	private function isBrandOf( $knownBrand, $currentBrand ) {
		$strippedKnownBrand   = trim( preg_replace( '/\s+/', ' ', $knownBrand ) );
		$strippedCurrentBrand = trim( preg_replace( '/\s+/', ' ', $currentBrand ) );

		return strtolower( $strippedKnownBrand ) === strtolower( $strippedCurrentBrand );
	}

	/**
	 * @param \StripeWPFS\Card $card
	 *
	 * @return bool
	 */
	private function isDinersClub( $card ) {
		return $this->isBrandOf( self::CARD_DINERS_CLUB, $card->brand );
	}

	/**
	 * @param \StripeWPFS\Card $card
	 *
	 * @return bool
	 */
	private function isDiscover( $card ) {
		return $this->isBrandOf( self::CARD_DISCOVER, $card->brand );
	}

	/**
	 * @param \StripeWPFS\Card $card
	 *
	 * @return bool
	 */
	private function isJCB( $card ) {
		return $this->isBrandOf( self::CARD_JCB, $card->brand );
	}

	/**
	 * @param \StripeWPFS\Card $card
	 *
	 * @return bool
	 */
	private function isMasterCard( $card ) {
		return $this->isBrandOf( self::CARD_MASTERCARD, $card->brand );
	}

	/**
	 * @param \StripeWPFS\Card $card
	 *
	 * @return bool
	 */
	private function isUnionPay( $card ) {
		return $this->isBrandOf( self::CARD_UNIONPAY, $card->brand );
	}

	/**
	 * @param \StripeWPFS\Card $card
	 *
	 * @return bool
	 */
	private function isVisa( $card ) {
		return $this->isBrandOf( self::CARD_VISA, $card->brand );
	}

	/**
	 * @param \StripeWPFS\Card $card
	 *
	 * @return bool
	 */
	private function isUnknownCard( $card ) {
		return $this->isBrandOf( self::CARD_UNKNOWN, $card->brand );
	}

	/**
	 * @param \StripeWPFS\Customer $stripeCustomer
	 *
	 * @return array
	 * @throws StripeWPFS\Exception\ApiErrorException
	 */
	private function prepareSubscriptions( $stripeCustomer ) {
		$subscriptions = array();
		if ( isset( $stripeCustomer ) ) {
			$customerSubscriptions = \StripeWPFS\Subscription::all(
				array(
					'customer' => $stripeCustomer->id,
					'expand'   => array(
						'data.plan.product'
					)
				)
			);
			$subscriptions         = $customerSubscriptions->data;
		}

		return $subscriptions;
	}

	/**
	 * @param \StripeWPFS\Customer $stripeCustomer
	 *
	 * @return array
	 * @throws StripeWPFS\Exception\ApiErrorException
	 */
	private function prepareInvoices( $stripeCustomer ) {
		$invoices = array();
		if ( isset( $stripeCustomer ) ) {
			$filter = array(
				'customer' => $stripeCustomer->id,
				'expand'   => array(
					'data.subscription.plan.product'
				)
			);

			$invoices = \StripeWPFS\Invoice::all( $filter );
		}

		return $invoices;
	}


    /**
     * @param $subscriptions array
     * @param $managedSubscriptions array
     */
	private function buildManagedSubscriptionsArray( $subscriptions, & $managedSubscriptions  ) {
        foreach ( $subscriptions as $subscription ) {
            $donationAmount = 1;
            if ( MM_WPFS_Utils::isDonationPlan( $subscription->plan )) {
                $donationRecord = $this->db->getDonationByStripeSubscriptionId( $subscription->id );
                if ( !is_null( $donationRecord )) {
                    $donationAmount = $donationRecord->amount;
                }
            }

            $managedSubscriptionEntry = new MM_WPFS_ManagedSubscriptionEntry( $subscription, $donationAmount );
            array_push( $managedSubscriptions, $managedSubscriptionEntry->toModel() );
        }
    }

	/**
	 * @param $cookieAction
	 * @param $cardUpdateSessionHash
	 * @param MM_WPFS_CardUpdateModel $model
	 */
	private function enqueueCardUpdateScript( $cookieAction, $cardUpdateSessionHash, $model ) {
		$options = get_option( 'fullstripe_options' );

		if ( $this->debugLog ) {
			MM_WPFS_Utils::log(
				'enqueueCardUpdateScript() CALLED, cookieAction=' . $cookieAction
				. ', cardUpdateSessionHash=' . $cardUpdateSessionHash
				. ', subscriptions=' . ( is_null( $model ) ? '0' : count( $model->getSubscriptions() ) )
			);
		}

		wp_register_style( MM_WPFS::HANDLE_STYLE_WPFS_VARIABLES, MM_WPFS_Assets::css( 'wpfs-variables.css' ), null, MM_WPFS::VERSION );
		wp_enqueue_style( self::HANDLE_MANAGE_SUBSCRIPTIONS_CSS, MM_WPFS_Assets::css( self::ASSET_WPFS_MANAGE_SUBSCRIPTIONS_CSS ), array( MM_WPFS::HANDLE_STYLE_WPFS_VARIABLES ), MM_WPFS::VERSION );

		wp_register_script( self::HANDLE_STRIPE_JS_V_3, 'https://js.stripe.com/v3/', array( 'jquery' ) );
		wp_register_script( MM_WPFS::HANDLE_SPRINTF_JS, MM_WPFS_Assets::scripts( 'sprintf.min.js' ), null, MM_WPFS::VERSION );

		if ( MM_WPFS_Utils::get_secure_subscription_update_with_google_recaptcha() ) {
			$source = add_query_arg(
				array(
					'render' => 'explicit'
				),
				MM_WPFS::SOURCE_GOOGLE_RECAPTCHA_V2_API_JS
			);
			wp_register_script( MM_WPFS::HANDLE_GOOGLE_RECAPTCHA_V_2, $source, null, MM_WPFS::VERSION, true /* in footer */ );
			$dependencies = array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-spinner',
				'underscore',
				'backbone',
				MM_WPFS::HANDLE_SPRINTF_JS,
				self::HANDLE_STRIPE_JS_V_3,
				MM_WPFS::HANDLE_GOOGLE_RECAPTCHA_V_2
			);
		} else {
			$dependencies = array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-spinner',
				'underscore',
				'backbone',
				MM_WPFS::HANDLE_SPRINTF_JS,
				self::HANDLE_STRIPE_JS_V_3
			);
		}

		wp_enqueue_script(
			self::HANDLE_MANAGE_SUBSCRIPTIONS_JS,
			MM_WPFS_Assets::scripts( self::ASSET_WPFS_MANAGE_SUBSCRIPTIONS_JS ),
			$dependencies,
			MM_WPFS::VERSION
		);

		wp_localize_script( self::HANDLE_MANAGE_SUBSCRIPTIONS_JS, self::JS_VARIABLE_AJAX_URL, admin_url( 'admin-ajax.php' ) );
		wp_localize_script( self::HANDLE_MANAGE_SUBSCRIPTIONS_JS, self::JS_VARIABLE_REST_URL, get_rest_url( null ) );
		if ( $options['apiMode'] === 'test' ) {
			wp_localize_script( self::HANDLE_MANAGE_SUBSCRIPTIONS_JS, self::JS_VARIABLE_STRIPE_KEY, $options['publishKey_test'] );
		} else {
			wp_localize_script( self::HANDLE_MANAGE_SUBSCRIPTIONS_JS, self::JS_VARIABLE_STRIPE_KEY, $options['publishKey_live'] );
		}

		$cardUpdateSessionData = array();
        $wpfsMyAccountOptions  = array();

		$cardUpdateSessionData['i18n'] = array(
			'confirmSubscriptionCancellationMessage'        => __( 'Are you sure you\'d like to cancel the selected subscriptions?', MM_WPFS::L10N_DOMAIN_PUBLIC ),
			'confirmSingleSubscriptionCancellationMessage'  => __( 'Are you sure you\'d like to cancel your subscription?', MM_WPFS::L10N_DOMAIN_PUBLIC ),
			'selectAtLeastOneSubscription'                  => __( 'Select at least one subscription!', MM_WPFS::L10N_DOMAIN_PUBLIC ),
			'cancelSubscriptionSubmitButtonCaptionDefault'  =>
			/* translators: Default button text for cancelling subscriptions - disabled state */
				__( 'Cancel subscription', MM_WPFS::L10N_DOMAIN_PUBLIC ),
			'cancelSubscriptionSubmitButtonCaptionSingular' =>
			/* translators: Button text for cancelling one subscription */
				__( 'Cancel 1 subscription', MM_WPFS::L10N_DOMAIN_PUBLIC ),
			'cancelSubscriptionSubmitButtonCaptionPlural'   =>
			/* translators: Button text for cancelling several subscriptions at once */
				__( 'Cancel %d subscriptions', MM_WPFS::L10N_DOMAIN_PUBLIC )
		);

		$stripeSubscriptions = array();
		if ( ! is_null( $model ) ) {
            $this->buildManagedSubscriptionsArray( $model->getSubscriptions(), $stripeSubscriptions );
		}
		$cardUpdateSessionData['stripe']['subscriptions'] = $stripeSubscriptions;

		if ( $options[ MM_WPFS::OPTION_MY_ACCOUNT_SHOW_INVOICES_SECTION ] ) {
			$stripeInvoices = array();
			if ( ! is_null( $model ) ) {
				foreach ( $model->getInvoices() as $invoice ) {
					$managedInvoiceEntry = new MM_WPFS_ManagedInvoiceEntry( $invoice );
					array_push( $stripeInvoices, $managedInvoiceEntry->toModel() );
				}
			}

			if ( count( $stripeInvoices ) <= self::INVOICE_DISPLAY_HEAD_LIMIT ) {
                $wpfsMyAccountOptions['invoiceDisplayMode'] = self::INVOICE_DISPLAY_MODE_FEW;
			} elseif ( $options[ MM_WPFS::OPTION_MY_ACCOUNT_SHOW_ALL_INVOICES ] ) {
                $wpfsMyAccountOptions['invoiceDisplayMode'] = self::INVOICE_DISPLAY_MODE_ALL;
			} else {
                $wpfsMyAccountOptions['invoiceDisplayMode'] = self::INVOICE_DISPLAY_MODE_HEAD;
				$stripeInvoices                             = array_slice( $stripeInvoices, 0, self::INVOICE_DISPLAY_HEAD_LIMIT );
			}

			$cardUpdateSessionData['stripe']['invoices'] = $stripeInvoices;
            $wpfsMyAccountOptions['showInvoicesSection'] = 1;
		} else {
            $wpfsMyAccountOptions['showInvoicesSection'] = 0;
		}

		// Converting the string ('0' or '1') to int, it makes dealing with it in javascript easier
        $wpfsMyAccountOptions['letSubscribersCancelSubscriptions'] = $options[ MM_WPFS::OPTION_MY_ACCOUNT_LET_SUBSCRIBERS_CANCEL_SUBSCRIPTIONS ] + 0;

		if ( self::COOKIE_ACTION_SET === $cookieAction ) {
			$cardUpdateSessionData['action']                = 'setCookie';
			$cardUpdateSessionData['cookieName']            = self::COOKIE_NAME_WPFS_CARD_UPDATE_SESSION_ID;
			$cardUpdateSessionData['cookieValidUntilHours'] = self::CARD_UPDATE_SESSION_VALID_UNTIL_HOURS;
			$cardUpdateSessionData['cookiePath']            = COOKIEPATH;
			$cardUpdateSessionData['cookieDomain']          = COOKIE_DOMAIN;
		} elseif ( self::COOKIE_ACTION_REMOVE === $cookieAction ) {
			$cardUpdateSessionData['cookieName'] = self::COOKIE_NAME_WPFS_CARD_UPDATE_SESSION_ID;
			$cardUpdateSessionData['action']     = 'removeCookie';
		}
		if ( ! is_null( $cardUpdateSessionHash ) ) {
			$cardUpdateSessionData['sessionId'] = $cardUpdateSessionHash;
		}
		wp_localize_script( self::HANDLE_MANAGE_SUBSCRIPTIONS_JS, 'wpfsCardUpdateSessionData', $cardUpdateSessionData );
        wp_localize_script( self::HANDLE_MANAGE_SUBSCRIPTIONS_JS, 'wpfsMyAccount', array(
            'options' => $wpfsMyAccountOptions
        ));
		wp_localize_script( self::HANDLE_MANAGE_SUBSCRIPTIONS_JS, self::JS_VARIABLE_GOOGLE_RECAPTCHA_SITE_KEY, MM_WPFS_Utils::get_google_recaptcha_site_key() );

	}

	/**
	 * @param $attributes
	 * @param MM_WPFS_CardUpdateModel $model
	 *
	 * @return string
	 */
	public function renderCardsAndSubscriptionsTable( $attributes, $model ) {
		ob_start();
		/** @noinspection PhpIncludeInspection */
		include MM_WPFS_Assets::templates( self::ASSET_DIR_MY_ACCOUNT . DIRECTORY_SEPARATOR . self::ASSET_MY_ACCOUNT_PHP );
		$content = ob_get_clean();

		return $content;
	}

	/**
	 * @param $cardUpdateSession
	 *
	 * @return bool
	 */
	private function isWaitingForConfirmation( $cardUpdateSession ) {
		if ( isset( $cardUpdateSession ) && isset( $cardUpdateSession->status ) ) {
			return self::SESSION_STATUS_WAITING_FOR_CONFIRMATION === $cardUpdateSession->status;
		} else {
			return false;
		}
	}

	/**
	 * @return null|string
	 */
	private function findSecurityCodeByRequest() {
		return isset( $_REQUEST[ self::PARAM_CARD_UPDATE_SECURITY_CODE ] ) ? sanitize_text_field( $_REQUEST[ self::PARAM_CARD_UPDATE_SECURITY_CODE ] ) : null;
	}

	/**
	 * @param $cardUpdateSession
	 *
	 * @return bool
	 */
	private function securityCodeInputExhausted( $cardUpdateSession ) {
		if ( isset( $cardUpdateSession ) && isset( $cardUpdateSession->securityCodeInput ) ) {
			return $cardUpdateSession->securityCodeInput >= self::SECURITY_CODE_INPUT_LIMIT;
		}

		return true;
	}

	/**
	 * @param $cardUpdateSession
	 */
	private function incrementSecurityCodeInput( $cardUpdateSession ) {
		$this->db->increment_security_code_input( $cardUpdateSession->id );
	}

	/**
	 * @param $cardUpdateSession
	 * @param $securityCode
	 *
	 * @return array
	 */
	public function validateSecurityCode( $cardUpdateSession, $securityCode ) {
		$valid                = false;
		$matchingSecurityCode = null;
		if ( isset( $cardUpdateSession ) && isset( $securityCode ) ) {
			$sanitizedSecurityCode = sanitize_text_field( $securityCode );
			$matchingSecurityCode  = $this->db->find_security_code_by_session_and_code( $cardUpdateSession->id, $sanitizedSecurityCode );
			if ( ! is_null( $matchingSecurityCode ) && $matchingSecurityCode->status !== self::SECURITY_CODE_STATUS_CONSUMED ) {
				$valid = true;
			}
		}

		if ( MM_WPFS_Utils::isDemoMode() ) {
			$valid = true;
		}

		return array( 'valid' => $valid, 'securityCode' => $matchingSecurityCode );
	}

	/**
	 * @param $cardUpdateSession
	 * @param $matchingSecurityCode
	 */
	private function confirmCardUpdateSessionWithSecurityCode( $cardUpdateSession, $matchingSecurityCode ) {
		$this->db->update_card_update_session( $cardUpdateSession->id, array( 'status' => self::SESSION_STATUS_CONFIRMED ) );
		$this->db->update_security_code( $matchingSecurityCode->id, array(
			'consumed' => current_time( 'mysql' ),
			'status'   => self::SECURITY_CODE_STATUS_CONSUMED
		) );
	}

	/**
	 * @return null|string
	 */
	private function findSessionHashByRequest() {
		return isset( $_REQUEST[ self::PARAM_CARD_UPDATE_SESSION ] ) ? sanitize_text_field( $_REQUEST[ self::PARAM_CARD_UPDATE_SESSION ] ) : null;
	}

	public function renderEmailForm( $attributes ) {

		ob_start();
		/** @noinspection PhpIncludeInspection */
		include MM_WPFS_Assets::templates( self::ASSET_DIR_MY_ACCOUNT . DIRECTORY_SEPARATOR . self::ASSET_ENTER_EMAIL_ADDRESS_PHP );
		$content = ob_get_clean();

		return $content;

	}

	public function renderSecurityCodeForm( $attributes ) {
		ob_start();
		/** @noinspection PhpIncludeInspection */
		include MM_WPFS_Assets::templates( self::ASSET_DIR_MY_ACCOUNT . DIRECTORY_SEPARATOR . self::ASSET_ENTER_SECURITY_CODE_PHP );
		$content = ob_get_clean();

		return $content;
	}

	public function renderInvalidCardUpdateSession( $attributes ) {
		ob_start();
		/** @noinspection PhpIncludeInspection */
		include MM_WPFS_Assets::templates( self::ASSET_DIR_MY_ACCOUNT . DIRECTORY_SEPARATOR . self::ASSET_INVALID_SESSION_PHP );
		$content = ob_get_clean();

		return $content;
	}

	public function handleCardUpdateSessionRequest() {

		$return = array();

		try {

			$stripeCustomerEmail     = isset( $_POST[ self::PARAM_EMAIL_ADDRESS ] ) ? sanitize_email( $_POST[ self::PARAM_EMAIL_ADDRESS ] ) : null;
			$googleReCAPTCHAResponse = isset( $_POST[ self::PARAM_GOOGLE_RE_CAPTCHA_RESPONSE ] ) ? sanitize_text_field( $_POST[ self::PARAM_GOOGLE_RE_CAPTCHA_RESPONSE ] ) : null;

			$validRequest = true;
			if ( is_null( $stripeCustomerEmail ) || ! filter_var( $stripeCustomerEmail, FILTER_VALIDATE_EMAIL ) ) {
				$return['success']    = false;
				$return['message']    = __( 'The entered email address is invalid.', MM_WPFS::L10N_DOMAIN_PUBLIC );
				$return['fieldError'] = self::PARAM_EMAIL_ADDRESS;
				$validRequest         = false;
			}
			$verifyReCAPTCHA = MM_WPFS_Utils::get_secure_subscription_update_with_google_recaptcha();
			if ( $verifyReCAPTCHA && $validRequest ) {
				if ( is_null( $googleReCAPTCHAResponse ) ) {
					$return['success']    = false;
					$return['message']    =
						/* translators: Captcha validation error message displayed when the form is submitted without completing the captcha challenge */
						__( 'Please prove that you are not a robot. ', MM_WPFS::L10N_DOMAIN_PUBLIC );
					$return['fieldError'] = self::PARAM_GOOGLE_RE_CAPTCHA_RESPONSE;
					$validRequest         = false;
				} else {
					$googleReCAPTCHVerificationResult = MM_WPFS_Utils::verifyReCAPTCHA( $googleReCAPTCHAResponse );
					// MM_WPFS_Utils::log( 'googleReCAPTCHVerificationResult=' . print_r( $googleReCAPTCHVerificationResult, true ) );
					if ( $googleReCAPTCHVerificationResult === false ) {
						$return['success']    = false;
						$return['message']    =
							/* translators: Captcha validation error message displayed when the form is submitted without completing the captcha challenge */
							__( 'Please prove that you are not a robot. ', MM_WPFS::L10N_DOMAIN_PUBLIC );
						$return['fieldError'] = self::PARAM_GOOGLE_RE_CAPTCHA_RESPONSE;
						$validRequest         = false;
					} elseif ( ! isset( $googleReCAPTCHVerificationResult->success ) || $googleReCAPTCHVerificationResult->success === false ) {
						$return['success']    = false;
						$return['message']    =
							/* translators: Captcha validation error message displayed when the form is submitted without completing the captcha challenge */
							__( 'Please prove that you are not a robot. ', MM_WPFS::L10N_DOMAIN_PUBLIC );
						$return['fieldError'] = self::PARAM_GOOGLE_RE_CAPTCHA_RESPONSE;
						if ( $this->debugLog ) {
							MM_WPFS_Utils::log( 'handleCardUpdateSessionRequest(): reCAPTCHA error response=' . print_r( $googleReCAPTCHVerificationResult, true ) );
						}
						$validRequest = false;
					}
				}
			}

			$stripeCustomer = null;
			if ( $validRequest ) {
				$stripeCustomer = MM_WPFS_Utils::find_existing_stripe_customer_anywhere_by_email(
					$this->db,
					$this->stripe,
					$stripeCustomerEmail
				);
				if ( is_null( $stripeCustomer ) ) {
					$return['success']    = false;
					$return['message']    = __( 'The entered email address is invalid.', MM_WPFS::L10N_DOMAIN_PUBLIC );
					$return['fieldError'] = self::PARAM_EMAIL_ADDRESS;
					$validRequest         = false;
				}
			}

			if ( $validRequest ) {

				$cardUpdateSession = $this->findValidCardUpdateSessionByEmailAndCustomer( $stripeCustomerEmail, $stripeCustomer->id );

				if ( ! is_null( $cardUpdateSession ) ) {
					$cardUpdateSessionCookieValue = $this->findCardUpdateSessionCookieValue();
					if ( $cardUpdateSession->hash !== $cardUpdateSessionCookieValue ) {
						$this->invalidate( $cardUpdateSession );
						$cardUpdateSession = null;
					}
				}

				if ( is_null( $cardUpdateSession ) || $this->isInvalidated( $cardUpdateSession ) ) {
					$options           = get_option( 'fullstripe_options' );
					$liveMode          = $options['apiMode'] === 'live';
					$cardUpdateSession = $this->createCardUpdateSession( $stripeCustomerEmail, $liveMode, $stripeCustomer->id );
				}

				$this->createCardUpdateSessionCookie( $cardUpdateSession );

				$this->createAndSendSecurityCodeAsEmail( $cardUpdateSession, $stripeCustomer );

				$return['success'] = true;
			}

		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$return['success']    = false;
			$return['ex_code']    = $e->getCode();
			$return['ex_message'] = $e->getMessage();
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;

	}

	private function findValidCardUpdateSessionByEmailAndCustomer( $stripeCustomerEmail, $stripeCustomerId ) {
		$cardUpdateSessions = $this->db->find_card_update_sessions_by_email_and_customer( $stripeCustomerEmail, $stripeCustomerId );

		$validCardUpdateSession = null;
		if ( isset( $cardUpdateSessions ) ) {
			foreach ( $cardUpdateSessions as $cardUpdateSession ) {
				if ( is_null( $validCardUpdateSession ) && ! $this->isInvalidated( $cardUpdateSession ) ) {
					$validCardUpdateSession = $cardUpdateSession;
				}
			}
		}

		return $validCardUpdateSession;
	}

	/**
	 * @param $cardUpdateSession
	 */
	private function createCardUpdateSessionCookie( $cardUpdateSession ) {
		if ( isset( $cardUpdateSession ) ) {
			setcookie( self::COOKIE_NAME_WPFS_CARD_UPDATE_SESSION_ID, $cardUpdateSession->hash, time() + HOUR_IN_SECONDS, "/", COOKIE_DOMAIN );
		}
	}

	private function createAndSendSecurityCodeAsEmail( $cardUpdateSession, $stripeCustomer ) {
		try {
			if ( isset( $cardUpdateSession ) && isset( $cardUpdateSession->status ) ) {
				if ( self::SESSION_STATUS_WAITING_FOR_CONFIRMATION === $cardUpdateSession->status ) {
					if ( ! $this->securityCodeRequestExhausted( $cardUpdateSession ) ) {
						$securityCode   = wp_generate_password( 8, false );
						$securityCodeId = $this->db->insert_security_code( $cardUpdateSession->id, $securityCode );
						if ( $securityCodeId !== - 1 ) {
							$this->incrementSecurityCodeRequest( $cardUpdateSession );
							if ( ! MM_WPFS_Utils::isDemoMode() ) {
								$this->mailer->send_card_update_confirmation_request( MM_WPFS_Utils::retrieve_customer_name( $stripeCustomer ), $cardUpdateSession->email, $cardUpdateSession->hash, $securityCode );
							}
							$this->db->update_security_code( $securityCodeId, array(
								'sent'   => current_time( 'mysql' ),
								'status' => self::SECURITY_CODE_STATUS_SENT
							) );
						}
					} else {
						$this->invalidate( $cardUpdateSession );
					}
				}
			}
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
		}
	}

	/**
	 * @param $cardUpdateSession
	 *
	 * @return bool
	 */
	private function securityCodeRequestExhausted( $cardUpdateSession ) {
		if ( isset( $cardUpdateSession ) && isset( $cardUpdateSession->securityCodeRequest ) ) {

			return $cardUpdateSession->securityCodeRequest >= self::SECURITY_CODE_REQUEST_LIMIT;
		}

		return true;
	}

	private function incrementSecurityCodeRequest( $cardUpdateSession ) {
		$this->db->increment_security_code_request( $cardUpdateSession->id );
	}

	public function handleResetCardUpdateSessionRequest() {
		$return = array();

		try {

			$cardUpdateSessionHash = $this->findCardUpdateSessionCookieValue();
			if ( ! is_null( $cardUpdateSessionHash ) ) {
				$cardUpdateSession = $this->findCardUpdateSessionByHash( $cardUpdateSessionHash );
				if ( ! is_null( $cardUpdateSession ) ) {
					if ( $this->isWaitingForConfirmation( $cardUpdateSession ) || $this->isConfirmed( $cardUpdateSession ) ) {
						$this->invalidate( $cardUpdateSession );
					}
				}
			}

			$return['success'] = true;
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$return['success']    = false;
			$return['ex_code']    = $e->getCode();
			$return['ex_message'] = $e->getMessage();
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;

	}

	public function handleSecurityCodeValidationRequest() {
		$return = array();

		try {

			$cardUpdateSessionHash = $this->findCardUpdateSessionCookieValue();
			$securityCode          = isset( $_POST['securityCode'] ) ? sanitize_text_field( $_POST['securityCode'] ) : null;
			if ( is_null( $securityCode ) || empty( $securityCode ) ) {
				$return['success'] = false;
				$return['message'] =
					/* translators: Login form validation error when no security code is entered */
					__( 'Enter a security code', MM_WPFS::L10N_DOMAIN_PUBLIC );
			} else {
				$success = false;
				if ( ! is_null( $cardUpdateSessionHash ) ) {
					$cardUpdateSession = $this->findCardUpdateSessionByHash( $cardUpdateSessionHash );
					if ( ! is_null( $cardUpdateSession ) && $this->isWaitingForConfirmation( $cardUpdateSession ) && ! $this->securityCodeInputExhausted( $cardUpdateSession ) ) {
						$this->incrementSecurityCodeInput( $cardUpdateSession );
						$validationResult     = $this->validateSecurityCode( $cardUpdateSession, $securityCode );
						$valid                = $validationResult['valid'];
						$matchingSecurityCode = $validationResult['securityCode'];
						if ( $valid ) {
							$this->confirmCardUpdateSessionWithSecurityCode( $cardUpdateSession, $matchingSecurityCode );
							$success = true;
						} else {
							$this->deleteCardUpdateSessionCookie();
						}
					}
				}

				if ( $success ) {
					$return['success'] = true;
				} else {
					$return['success'] = false;
					$return['message'] =
						/* translators: Login form validation error when an invalid security code is entered */
						__( 'Enter a valid security code', MM_WPFS::L10N_DOMAIN_PUBLIC );
				}
			}
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$return['success']    = false;
			$return['ex_code']    = $e->getCode();
			$return['ex_message'] = $e->getMessage();
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	private function deleteCardUpdateSessionCookie() {
		unset( $_COOKIE[ self::COOKIE_NAME_WPFS_CARD_UPDATE_SESSION_ID ] );
		setcookie( self::COOKIE_NAME_WPFS_CARD_UPDATE_SESSION_ID, '', time() - DAY_IN_SECONDS );
	}

	public function handleCardUpdateRequest() {
		$return = array();
		try {
			$stripePaymentMethodId = isset( $_POST['paymentMethodId'] ) ? sanitize_text_field( $_POST['paymentMethodId'] ) : null;
			$stripeSetupIntentId   = isset( $_POST['setupIntentId'] ) ? sanitize_text_field( $_POST['setupIntentId'] ) : null;
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'handleCardUpdateRequest(): stripePaymentMethodId=' . print_r( $stripePaymentMethodId, true ) );
			}
			$cardUpdateSessionHash = $this->findCardUpdateSessionCookieValue();
			if ( ! is_null( $stripePaymentMethodId ) && ! is_null( $cardUpdateSessionHash ) ) {
				$cardUpdateSession = $this->findCardUpdateSessionByHash( $cardUpdateSessionHash );
				if ( ! is_null( $cardUpdateSession ) && $this->isConfirmed( $cardUpdateSession ) ) {
					$stripeCustomer = $this->stripe->retrieve_customer( $cardUpdateSession->stripeCustomerId );
					if ( isset( $stripeCustomer ) ) {
						$stripePaymentMethod = $this->stripe->validatePaymentMethodCVCCheck( $stripePaymentMethodId );
						if ( isset( $stripePaymentMethod ) ) {
							$stripeSetupIntent = null;
							if ( is_null( $stripeSetupIntentId ) ) {
								$stripeSetupIntent   = $this->stripe->createSetupIntentWithPaymentMethod( $stripePaymentMethod->id );
								$stripeSetupIntentId = $stripeSetupIntent->id;
								$stripeSetupIntent->confirm();
							}
							$stripeSetupIntent = $this->stripe->retrieveSetupIntent( $stripeSetupIntentId );
							if ( $stripeSetupIntent instanceof \StripeWPFS\SetupIntent ) {
								$return['setupIntentId'] = $stripeSetupIntent->id;
								if (
									\StripeWPFS\SetupIntent::STATUS_REQUIRES_ACTION === $stripeSetupIntent->status
									&& 'use_stripe_sdk' === $stripeSetupIntent->next_action->type
								) {
									if ( $this->debugLog ) {
										MM_WPFS_Utils::log( __FUNCTION__ . '(): SetupIntent requires action...' );
									}
									$return['success']                 = false;
									$return['requiresAction']          = true;
									$return['setupIntentClientSecret'] = $stripeSetupIntent->client_secret;
									$return['message']                 =
										/* translators: Banner message of a pending card saving transaction requiring a second factor authentication (SCA/PSD2) */
										__( 'Saving this card requires additional action before completion!', MM_WPFS::L10N_DOMAIN_PUBLIC );
								} elseif ( \StripeWPFS\SetupIntent::STATUS_SUCCEEDED === $stripeSetupIntent->status ) {
									if ( $this->debugLog ) {
										MM_WPFS_Utils::log( __FUNCTION__ . '(): SetupIntent succeeded.' );
									}
									$this->stripe->attachPaymentMethodToCustomerIfMissing( $stripeCustomer, $stripePaymentMethod, true );
									$return['success'] = true;
									$return['message'] = __( 'The default credit card has been updated successfully!', MM_WPFS::L10N_DOMAIN_PUBLIC );
								} else {
									// This is an internal error, no need to localize it
									$errorMessage         = sprintf( "Unknown SetupIntent status '%s'!", $stripeSetupIntent->status );
									$return['success']    = false;
									$return['ex_message'] = $errorMessage;
									MM_WPFS_Utils::log( 'handleCardUpdateRequest(): ERROR: ' . $errorMessage );
								}
							} else {
								// This is an internal error, no need to localize it
								$errorMessage         = 'Invalid SetupIntent!';
								$return['success']    = false;
								$return['ex_message'] = $errorMessage;
								MM_WPFS_Utils::log( 'handleCardUpdateRequest(): ERROR: ' . $errorMessage );
							}
						} else {
							// This is an internal error, no need to localize it
							$errorMessage         = 'Stripe PaymentMethod not found!';
							$return['success']    = false;
							$return['ex_message'] = $errorMessage;
							MM_WPFS_Utils::log( 'handleCardUpdateRequest(): ERROR: ' . $errorMessage );
						}
					} else {
						// This is an internal error, no need to localize it
						$errorMessage         = 'Stripe Customer not found!';
						$return['success']    = false;
						$return['ex_message'] = $errorMessage;
						MM_WPFS_Utils::log( 'handleCardUpdateRequest(): ERROR: ' . $errorMessage );
					}
				} else {
					// This is an internal error, no need to localize it
					$errorMessage         = 'Invalid Card Update Session!';
					$return['success']    = false;
					$return['ex_message'] = $errorMessage;
					MM_WPFS_Utils::log( 'handleCardUpdateRequest(): ERROR: ' . $errorMessage );
				}
			} else {
				// This is an internal error, no need to localize it
				$errorMessage         = 'Invalid POST parameters!';
				$return['success']    = false;
				$return['ex_message'] = $errorMessage;
				MM_WPFS_Utils::log( 'handleCardUpdateRequest(): ERROR: ' . $errorMessage );
			}
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$return['success']    = false;
			$return['ex_code']    = $e->getCode();
			$return['ex_message'] = $e->getMessage();
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;

	}

	public function toggleInvoiceView() {
		$return = array();
		try {
			$cardUpdateSessionHash = $this->findCardUpdateSessionCookieValue();
			if ( ! is_null( $cardUpdateSessionHash ) ) {
				$cardUpdateSession = $this->findCardUpdateSessionByHash( $cardUpdateSessionHash );
				if ( ! is_null( $cardUpdateSession ) && $this->isConfirmed( $cardUpdateSession ) ) {
					$options = get_option( 'fullstripe_options' );

					$currentView = $options[ MM_WPFS::OPTION_MY_ACCOUNT_SHOW_ALL_INVOICES ];
					$currentView ^= 1;
					$options[ MM_WPFS::OPTION_MY_ACCOUNT_SHOW_ALL_INVOICES ] = $currentView;
					update_option( 'fullstripe_options', $options );

					$return['success'] = true;
				} else {
					$return['success'] = false;
					$return['message'] = 'Account session is not confirmed.';
				}
			} else {
				$return['success'] = false;
				$return['message'] = 'No valid account session found.';
			}
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$return['success']    = false;
			$return['message']    = 'Invoice view toggle failed.';
			$return['ex_code']    = $e->getCode();
			$return['ex_message'] = $e->getMessage();
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}


    /**
     * @param $subscriptionId string
     *
     * @throws Exception
     */
    private function cancelSubscriptionInDatabase( $subscriptionId ) {
        $subscription = $this->stripe->retrieveSubscriptionWithPlanExpanded( $subscriptionId );

        if ( MM_WPFS_Utils::isDonationPlan( $subscription->plan )) {
            $this->db->cancelDonationByStripeSubscriptionId( $subscriptionId );
        } else {
            $this->db->cancelSubscriptionByStripeSubscriptionId( $subscriptionId );
        }
    }


    public function handleSubscriptionCancellationRequest() {
		$return = array();
		try {
			$subscriptionIdsToCancel = isset( $_POST[ self::PARAM_WPFS_SUBSCRIPTION_ID ] ) ? $_POST[ self::PARAM_WPFS_SUBSCRIPTION_ID ] : null;
			if ( isset( $subscriptionIdsToCancel ) && count( $subscriptionIdsToCancel ) > 0 ) {
				$cardUpdateSessionHash = $this->findCardUpdateSessionCookieValue();
				if ( ! is_null( $subscriptionIdsToCancel ) && ! is_null( $cardUpdateSessionHash ) ) {
					$cardUpdateSession = $this->findCardUpdateSessionByHash( $cardUpdateSessionHash );
					if ( ! is_null( $cardUpdateSession ) && $this->isConfirmed( $cardUpdateSession ) ) {
						$stripeCustomer = $this->stripe->retrieve_customer( $cardUpdateSession->stripeCustomerId );
						if ( isset( $stripeCustomer ) ) {
							foreach ( $subscriptionIdsToCancel as $subscriptionId ) {
								$success = $this->stripe->cancel_subscription( $stripeCustomer->id, $subscriptionId );
								if ( $success ) {
								    $this->cancelSubscriptionInDatabase( $subscriptionId );
								}
							}
						}
					}
				}
				$return['success'] = true;
				$return['message'] = __( 'The subscriptions have been cancelled', MM_WPFS::L10N_DOMAIN_PUBLIC );
			} else {
				$return['success'] = false;
				$return['message'] = __( 'Select at least one subscription!', MM_WPFS::L10N_DOMAIN_PUBLIC );
			}

		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$return['success']    = false;
			$return['ex_code']    = $e->getCode();
			$return['ex_message'] = $e->getMessage();
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	/**
	 * Fetch Stripe Subscriptions for a given customer to supply data for a Backbone Collection
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function handleSubscriptionsFetchRequest( WP_REST_Request $request ) {
		$data = array();
		try {

			$cardUpdateSessionHash = $this->findCardUpdateSessionCookieValue();
			if ( ! is_null( $cardUpdateSessionHash ) ) {
				$cardUpdateSession = $this->findCardUpdateSessionByHash( $cardUpdateSessionHash );
				if ( ! is_null( $cardUpdateSession ) ) {
					$model = new MM_WPFS_CardUpdateModel();
					if ( is_user_logged_in() ) {
						$model->setAuthenticationType( MM_WPFS_CardUpdateModel::AUTHENTICATION_TYPE_WORDPRESS );
					} else {
						$model->setAuthenticationType( MM_WPFS_CardUpdateModel::AUTHENTICATION_TYPE_PLUGIN );
					}
					$stripeCustomer = MM_WPFS_Utils::find_existing_stripe_customer_anywhere_by_email(
						$this->db,
						$this->stripe,
						$cardUpdateSession->email
					);
					$this->fetchDataIntoCardUpdateModel( $model, $stripeCustomer );

                    $this->buildManagedSubscriptionsArray( $model->getSubscriptions(), $data );
				}
			}

		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );

			return new WP_Error( $e->getCode(), $e->getMessage() );
		}

		return new WP_REST_Response( $data, 200 );

	}

	/**
	 * Update subscription
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function handleSubscriptionUpdateRequest( WP_REST_Request $request ) {
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'handleSubscriptionUpdateRequest(): CALLED, json_params=' . print_r( $request->get_json_params(), true ) );
		}
		$data = array();
		try {
			$updatedSubscription = $request->get_json_params();
			if ( is_array( $updatedSubscription ) ) {
				if ( array_key_exists( 'id', $updatedSubscription ) && array_key_exists( 'action', $updatedSubscription ) ) {
					$stripeSubscriptionId = sanitize_text_field( $updatedSubscription['id'] );
					if ( 'cancel' === $updatedSubscription['action'] ) {
						$cardUpdateSessionHash = $this->findCardUpdateSessionCookieValue();
						if ( ! is_null( $stripeSubscriptionId ) && ! is_null( $cardUpdateSessionHash ) ) {
							$cardUpdateSession = $this->findCardUpdateSessionByHash( $cardUpdateSessionHash );
							if ( ! is_null( $cardUpdateSession ) && $this->isConfirmed( $cardUpdateSession ) ) {
								$stripeCustomer = $this->stripe->retrieve_customer( $cardUpdateSession->stripeCustomerId );
								if ( isset( $stripeCustomer ) ) {
									$success = $this->stripe->cancel_subscription( $stripeCustomer->id, $stripeSubscriptionId );
									if ( $success ) {
                                        $this->cancelSubscriptionInDatabase( $stripeSubscriptionId );
									}
								}
							}
						}
					}
				} elseif ( array_key_exists( 'id', $updatedSubscription ) && array_key_exists( 'planQuantity', $updatedSubscription ) ) {
					$stripeSubscriptionId = sanitize_text_field( $updatedSubscription['id'] );
					$newQuantity          = sanitize_text_field( $updatedSubscription['planQuantity'] );
					if ( isset( $stripeSubscriptionId ) && is_numeric( $newQuantity ) && $newQuantity > 0 ) {
						$cardUpdateSessionHash = $this->findCardUpdateSessionCookieValue();
						if ( ! is_null( $stripeSubscriptionId ) && ! is_null( $cardUpdateSessionHash ) ) {
							$cardUpdateSession = $this->findCardUpdateSessionByHash( $cardUpdateSessionHash );
							if ( ! is_null( $cardUpdateSession ) && $this->isConfirmed( $cardUpdateSession ) ) {
								$stripeCustomer = $this->stripe->retrieve_customer( $cardUpdateSession->stripeCustomerId );
								if ( isset( $stripeCustomer ) ) {
									$success = $this->stripe->update_subscription_quantity( $stripeCustomer->id, $stripeSubscriptionId, $newQuantity );
									if ( $success ) {
										$this->db->update_subscription_quantity_by_stripe_subscription_id( $stripeSubscriptionId, $newQuantity );
									}
								}
							}
						}
					}
				}
				$data['success'] = true;
				$data['message'] = __( 'The subscription has been updated successfully', MM_WPFS::L10N_DOMAIN_PUBLIC );
			}
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );

			return new WP_Error( $e->getCode(), $e->getMessage() );
		}

		return new WP_REST_Response( $data, 200 );

	}

	/**
	 * Adds Cache-Control HTTP header if a page is displayed with the "Manage Subscriptions" shortcode
	 *
	 * @param $theWPObject
	 */
	public function addCacheControlHeader( $theWPObject ) {
		$started = round( microtime( true ) * 1000 );
		if ( ! is_null( $theWPObject ) && isset( $theWPObject->request ) ) {
			$pageByPath = get_page_by_path( $theWPObject->request );
			if ( ! is_null( $pageByPath ) && isset( $pageByPath->post_content ) ) {
				if ( has_shortcode( $pageByPath->post_content, self::FULLSTRIPE_SHORTCODE_SUBSCRIPTION_UPDATE ) ||
				     has_shortcode( $pageByPath->post_content, self::FULLSTRIPE_SHORTCODE_MANAGE_SUBSCRIPTIONS )
				) {
					header( 'Cache-Control: no-store, no-cache, must-revalidate' );
				}
			}
		}
		$finished = round( microtime( true ) * 1000 ) - $started;
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'addCacheControlHeader(): finished in ' . $finished . 'ms' );
		}
	}

	public function addAsyncDeferAttributes( $tag, $handle ) {
		if ( MM_WPFS::HANDLE_GOOGLE_RECAPTCHA_V_2 !== $handle ) {
			return $tag;
		}

		return str_replace( ' src', ' async defer src', $tag );
	}

	/**
	 * Register WPFS Manage Subscriptions REST API routes
	 */
	public function registerRESTAPIRoutes() {
		register_rest_route( $this->getRESTAPINamespace(), $this->getBaseRoute(), array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'handleSubscriptionsFetchRequest' ),
				'args'     => array(),
                'permission_callback' => '__return_true'
			)
		) );
		register_rest_route( $this->getRESTAPINamespace(), $this->getItemRoute(), array(
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'handleSubscriptionUpdateRequest' ),
				'args'     => array(),
                'permission_callback' => '__return_true'
			)
		) );
	}

	private function getRESTAPINamespace() {
		return self::WPFS_PLUGIN_SLUG . '/' . self::WPFS_REST_API_VERSION;
	}

	private function getBaseRoute() {
		return '/' . self::WPFS_REST_ROUTE_MANAGE_SUBSCRIPTIONS_SUBSCRIPTION;
	}

	private function getItemRoute() {
		return $this->getBaseRoute() . '/' . '(?P<id>[\w]+)';
	}

}

class MM_WPFS_CardUpdateModel {

	const AUTHENTICATION_TYPE_PLUGIN = 'Plugin';
	const AUTHENTICATION_TYPE_WORDPRESS = 'Wordpress';

	/**
	 * @var \StripeWPFS\Customer
	 */
	private $stripeCustomer;
	/**
	 * @var \StripeWPFS\Card
	 */
	private $defaultSource;
	/**
	 * @var \StripeWPFS\PaymentMethod
	 */
	private $defaultPaymentMethod;
	/**
	 * @var string
	 */
	private $cardImageUrl;
	/**
	 * @var string
	 */
	private $cardName;
	/**
	 * @var string
	 */
	private $cardNumber;
	/**
	 * @var array
	 */
	private $subscriptions = array();
	/**
	 * @var array
	 */
	private $products = array();
	/**
	 * @var string
	 */
	private $authenticationType;
	/**
	 * @var array
	 */
	private $invoices = array();

	/**
	 * @return array
	 */
	public function getInvoices() {
		return $this->invoices;
	}

	/**
	 * @param array $invoices
	 */
	public function setInvoices( $invoices ) {
		$this->invoices = $invoices;
	}

	/**
	 * @return string
	 */
	public function getAuthenticationType() {
		return $this->authenticationType;
	}

	/**
	 * @param string $authenticationType
	 */
	public function setAuthenticationType( $authenticationType ) {
		$this->authenticationType = $authenticationType;
	}

	/**
	 * @return \StripeWPFS\Customer
	 */
	public function getStripeCustomer() {
		return $this->stripeCustomer;
	}

	/**
	 * @param \StripeWPFS\Customer $stripeCustomer
	 */
	public function setStripeCustomer( $stripeCustomer ) {
		$this->stripeCustomer = $stripeCustomer;
	}

	/**
	 * @return \StripeWPFS\Card
	 */
	public function getDefaultSource() {
		return $this->defaultSource;
	}

	/**
	 * @param \StripeWPFS\Card $defaultSource
	 */
	public function setDefaultSource( $defaultSource ) {
		$this->defaultSource = $defaultSource;
	}

	/**
	 * @return \StripeWPFS\PaymentMethod
	 */
	public function getDefaultPaymentMethod() {
		return $this->defaultPaymentMethod;
	}

	/**
	 * @param \StripeWPFS\PaymentMethod $defaultPaymentMethod
	 */
	public function setDefaultPaymentMethod( $defaultPaymentMethod ) {
		$this->defaultPaymentMethod = $defaultPaymentMethod;
	}

	/**
	 * @return string
	 */
	public function getCardImageUrl() {
		return $this->cardImageUrl;
	}

	/**
	 * @param string $cardImageUrl
	 */
	public function setCardImageUrl( $cardImageUrl ) {
		$this->cardImageUrl = $cardImageUrl;
	}

	/**
	 * @return string
	 */
	public function getCardName() {
		return $this->cardName;
	}

	/**
	 * @param string $cardName
	 */
	public function setCardName( $cardName ) {
		$this->cardName = $cardName;
	}

	/**
	 * @return string
	 */
	public function getCardNumber() {
		return $this->cardNumber;
	}

	/**
	 * @param string $cardNumber
	 */
	public function setCardNumber( $cardNumber ) {
		$this->cardNumber = $cardNumber;
	}

	/**
	 * @return string
	 */
	public function getFormattedCardNumber() {
		return sprintf( 'x-%s', $this->cardNumber );
	}

	public function getExpiration() {
		if ( isset( $this->defaultPaymentMethod ) ) {
			return sprintf( '%02d / %d', $this->defaultPaymentMethod->card->exp_month, $this->defaultPaymentMethod->card->exp_year );
		} elseif ( isset( $this->defaultSource ) ) {
			return sprintf( '%02d / %d', $this->defaultSource->exp_month, $this->defaultSource->exp_year );
		}

		return '';
	}

	/**
	 * @return array
	 */
	public function getSubscriptions() {
		return $this->subscriptions;
	}

	/**
	 * @param array $subscriptions
	 */
	public function setSubscriptions( $subscriptions ) {
		$this->subscriptions = $subscriptions;
	}

	/**
	 * @return array
	 */
	public function getProducts() {
		return $this->products;
	}

	/**
	 * @param array $products
	 */
	public function setProducts( $products ) {
		$this->products = $products;
	}

	/**
	 * @return null|string
	 */
	public function getCustomerEmail() {
		if ( isset( $this->stripeCustomer ) ) {
			return $this->stripeCustomer->email;
		}

		return null;
	}

}

class MM_WPFS_ManagedInvoiceEntry {
	/**
	 * @var \StripeWPFS\Invoice
	 */
	private $invoice;

	/**
	 * MM_WPFS_ManagedInvoiceEntry constructor.
	 *
	 * @param \StripeWPFS\Invoice $invoice
	 */
	public function __construct( $invoice ) {
		$this->invoice = $invoice;
	}

	/**
	 * Convert this object to a standard class instance for Backbone.js
	 */
	public function toModel() {
		$model                = new stdClass();
		$model->id            = $this->getValue();
		$model->priceLabel    = $this->getPriceLabel();
		$model->created       = $this->getCreated();
		$model->invoiceNumber = $this->getInvoiceNumber();
		$model->invoiceUrl    = $this->geInvoiceUrl();

		if ( isset( $this->invoice->subscription ) ) {
			$model->planName     = $this->getPlanName();
			$model->planQuantity = $this->getPlanQuantity();
		} else {
			$model->planName     = null;
			$model->planQuantity = 0;
		}

		return $model;
	}

	public function getValue() {
		return $this->invoice->id;
	}

	public function getPriceLabel() {
        $formattedAmount = MM_WPFS_Currencies::formatAndEscapeByMyAccount(
            $this->invoice->currency,
            $this->invoice->total,
            true,
            true );

        return $formattedAmount;
	}

	public function getCreated() {
		$dateFormat = get_option( 'date_format' );

		return date( $dateFormat, $this->invoice->created );
	}

	public function getInvoiceNumber() {
		$invoiceNumber = $this->invoice->number;

		return $invoiceNumber;
	}

	public function geInvoiceUrl() {
		$invoiceUrl = $this->invoice->invoice_pdf;

		return $invoiceUrl;
	}

	public function getPlanName() {
		$planName = null;
		$product  = $this->findProductOnInvoice();
		if ( $product instanceof \StripeWPFS\Product ) {
			$planName = $product->name;
		}

		return $planName;
	}

	/**
	 * @return null|\StripeWPFS\Product
	 */
	private function findProductOnInvoice() {
		$product = null;
		if ( isset( $this->invoice ) &&
		     isset( $this->invoice->subscription ) &&
		     isset( $this->invoice->subscription->plan ) &&
		     isset( $this->invoice->subscription->plan->product )
		) {
			if ( $this->invoice->subscription->plan->product instanceof \StripeWPFS\Product ) {
				$product = $this->invoice->subscription->plan->product;
			}
		}

		return $product;
	}

	public function getPlanQuantity() {
		$quantity = $this->invoice->subscription->quantity;

		return $quantity;
	}
}

class MM_WPFS_ManagedSubscriptionEntry {

	const PARAM_WPFS_SUBSCRIPTION_ID = 'wpfs-subscription-id[]';

	/**
	 * @var \StripeWPFS\Subscription
	 */
	private $subscription;
	private $donationAmount;

	/**
	 * MM_WPFS_ManagedSubscriptionEntry constructor.
	 *
	 * @param \StripeWPFS\Subscription $subscription
	 */
	public function __construct($subscription, $recurringDonationAmount = 0 ) {
		$this->subscription = $subscription;
		$this->donationAmount = $recurringDonationAmount;
	}

	/**
	 * Convert this object to a standard class instance for Backbone.js
	 */
	public function toModel() {
		$model                = new stdClass();
		$model->id            = $this->getValue();
		$model->idAttribute   = $this->getId();
		$model->nameAttribute = $this->getName();
		$model->status        = $this->getStatus();
		$model->statusClass   = $this->getClass();
		$model->created       = $this->getCreated();

		if ( isset( $this->subscription->plan ) ) {
			$model->planName                   = $this->getPlanName();
			$model->planQuantity               = $this->getPlanQuantity();
			$model->allowMultipleSubscriptions = $this->isAllowMultipleSubscriptions();
			$model->maximumPlanQuantity        = $this->getMaximumPlanQuantity();
			$model->planLabel                  = $this->getPlanLabel();
			$model->priceAndIntervalLabel      = $this->getPriceAndIntervalLabel();
			$model->summary                    = $this->getSummaryLabel();
		} else {
			$model->planName                   =
				/* translators: Displayed in the subscription list when a subscription is composed of more than one subscription plan */
				__( 'Multiple plans', MM_WPFS::L10N_DOMAIN_PUBLIC );
			$model->planQuantity               = 1;
			$model->allowMultipleSubscriptions = false;
			$model->maximumPlanQuantity        = 0;
			$model->planLabel                  = $model->planName;
			$model->priceAndIntervalLabel      = null;
			$model->summary                    = null;
		}

		return $model;
	}

	public function getValue() {
		return $this->subscription->id;
	}

	public function getId() {
		return sprintf( 'wpfs-subscription--%s', $this->subscription->id );
	}

	public function getName() {
		return self::PARAM_WPFS_SUBSCRIPTION_ID;
	}

	public function getStatus() {
		return MM_WPFS_Localization::translateLabel( ucfirst( $this->subscription->status ) );
	}

	public function getClass() {
		$clazz = '';
		if ( MM_WPFS::STRIPE_SUBSCRIPTION_STATUS_TRIALING === $this->subscription->status ) {
			$clazz = 'wpfs-subscription-status--trialing';
		} elseif ( MM_WPFS::STRIPE_SUBSCRIPTION_STATUS_ACTIVE === $this->subscription->status ) {
			$clazz = 'wpfs-subscription-status--active';
		} elseif ( MM_WPFS::STRIPE_SUBSCRIPTION_STATUS_PAST_DUE === $this->subscription->status ) {
			$clazz = 'wpfs-subscription-status--past-due';
		} elseif ( MM_WPFS::STRIPE_SUBSCRIPTION_STATUS_CANCELED === $this->subscription->status ) {
			$clazz = 'wpfs-subscription-status--past-due';
		} elseif ( MM_WPFS::STRIPE_SUBSCRIPTION_STATUS_UNPAID === $this->subscription->status ) {
			$clazz = 'wpfs-subscription-status--unpaid';
		}

		return $clazz;
	}

	public function getCreated() {
		$dateFormat = get_option( 'date_format' );

		return date( $dateFormat, $this->subscription->created );
	}

	public function getPlanName() {
		// This is an unwanted plan name, no need to localize it
		$planName = 'Unknown';
		$product  = $this->findProductInSubscription();
		if ( $product instanceof \StripeWPFS\Product ) {
			$planName = $product->name;
		}

		return $planName;
	}

	/**
	 * @return null|\StripeWPFS\Product
	 */
	private function findProductInSubscription() {
		$product = null;
		if ( isset( $this->subscription ) && isset( $this->subscription->plan ) && isset( $this->subscription->plan->product ) ) {
			if ( $this->subscription->plan->product instanceof \StripeWPFS\Product ) {
				$product = $this->subscription->plan->product;
			}
		}

		return $product;
	}

	public function getPlanQuantity() {
		$quantity = $this->subscription->quantity;

		return $quantity;
	}

	/**
	 * @return bool
	 */
	public function isAllowMultipleSubscriptions() {
		$allowMultipleSubscriptions = false;
		if ( isset( $this->subscription->metadata ) && isset( $this->subscription->metadata->allow_multiple_subscriptions ) ) {
			$allowMultipleSubscriptions = boolval( $this->subscription->metadata->allow_multiple_subscriptions );
		}

		return $allowMultipleSubscriptions;
	}

	/**
	 * @return bool|int
	 */
	public function getMaximumPlanQuantity() {
		$maximumPlanQuantity = 0;
		if ( isset( $this->subscription->metadata ) && isset( $this->subscription->metadata->maximum_quantity_of_subscriptions ) ) {
			$maximumPlanQuantity = intval( $this->subscription->metadata->maximum_quantity_of_subscriptions );
		}

		return $maximumPlanQuantity;
	}

	public function getPlanLabel() {
		$planName = $this->getPlanName();
		$quantity = $this->subscription->quantity;

		//todo: localize this
		return $quantity == 1 ? $planName : sprintf( '%d%s %s', $quantity, 'x', $planName );
	}

	public function getPriceAndIntervalLabel() {
		$currency      = $this->subscription->plan->currency;
		$interval      = $this->subscription->plan->interval;
		$intervalCount = $this->subscription->plan->interval_count;

		$amount = 1;
		if ( MM_WPFS_Utils::isDonationPlan( $this->subscription->plan ) ) {
		    $amount = $this->donationAmount;
        } else {
            $amount = $this->subscription->plan->amount;
        }

        $formattedAmount = MM_WPFS_Currencies::formatAndEscapeByMyAccount(
            $currency,
            $amount,
            true,
            true );

        return MM_WPFS_Localization::getPriceAndIntervalLabel( $interval, $intervalCount, $formattedAmount );
	}

	public function getSummaryLabel() {
		$formatStr     = null;
		$summaryLabel  = null;
		$intervalCount = $this->subscription->plan->interval_count;

		switch ( $this->subscription->plan->interval ) {
			case 'day':
                /* translators:
                 * p1: formatted recurring amount with currency symbol
                 * p2: interval count
                 */
				$formatStr = _n(
					'Subscription fee is %s / day',
					'Subscription fee is %1$s / %2$d days',
					$intervalCount, MM_WPFS::L10N_DOMAIN_PUBLIC
				);
				break;

			case 'week':
                /* translators:
                 * p1: formatted recurring amount with currency symbol
                 * p2: interval count
                 */
				$formatStr = _n(
					'Subscription fee is %s / week',
					'Subscription fee is %1$s / %2$d weeks',
					$intervalCount, MM_WPFS::L10N_DOMAIN_PUBLIC
				);
				break;

			case 'month':
                /* translators:
                 * p1: formatted recurring amount with currency symbol
                 * p2: interval count
                 */
				$formatStr = _n(
					'Subscription fee is %s / month',
					'Subscription fee is %1$s / %2$d months',
					$intervalCount, MM_WPFS::L10N_DOMAIN_PUBLIC
				);
				break;

			case 'year':
                /* translators:
                 * p1: formatted recurring amount with currency symbol
                 * p2: interval count
                 */
				$formatStr = _n(
					'Subscription fee is %s / year',
					'Subscription fee is %1$s / %2$d years',
					$intervalCount, MM_WPFS::L10N_DOMAIN_PUBLIC
				);
				break;

			default:
				throw new Exception( sprintf( '%s.%s(): Unknown plan interval \'%s\'.', __CLASS__, __FUNCTION__, $this->subscription->plan->interval ) );
				break;
		}

		$formattedAmount = MM_WPFS_Currencies::format( $this->subscription->plan->currency, $this->subscription->plan->amount, false );

		if ( $intervalCount == 1 ) {
			$summaryLabel = sprintf( $formatStr, $formattedAmount );
		} else {
			$summaryLabel = sprintf( $formatStr, $formattedAmount, $this->subscription->plan->interval_count );
		}

		return $summaryLabel;
	}
}