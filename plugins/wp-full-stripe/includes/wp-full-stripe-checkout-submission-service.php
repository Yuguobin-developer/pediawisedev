<?php

/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2019.08.06.
 * Time: 11:09
 */
class MM_WPFS_CheckoutSubmissionService {

	const POPUP_FORM_SUBMIT_STATUS_CREATED = 'created';
	const POPUP_FORM_SUBMIT_STATUS_PENDING = 'pending';
	const POPUP_FORM_SUBMIT_STATUS_FAILED = 'failed';
	const POPUP_FORM_SUBMIT_STATUS_CANCELLED = 'cancelled';
	const POPUP_FORM_SUBMIT_STATUS_SUCCESS = 'success';
	const POPUP_FORM_SUBMIT_STATUS_COMPLETE = 'complete';
	const POPUP_FORM_SUBMIT_STATUS_INTERNAL_ERROR = 'internal_error';
	const CHECKOUT_SESSION_STATUS_SUCCESS = 'success';
	const CHECKOUT_SESSION_STATUS_CANCELLED = 'cancelled';
	const PROCESS_RESULT_SET_TO_SUCCESS = 1;
	const PROCESS_RESULT_SET_TO_FAILED = 2;
	const PROCESS_RESULT_EXPIRED = 3;
	const PROCESS_RESULT_WAIT_FOR_STATUS_CHANGE = 4;
	const PROCESS_RESULT_INTERNAL_ERROR = 20;
	const ACTION_FULLSTRIPE_PROCESS_CHECKOUT_SUBMISSIONS = 'fullstripe_process_checkout_submissions';
	const STRIPE_CALLBACK_PARAM_WPFS_POPUP_FORM_SUBMIT_HASH = 'wpfs-sid';
	const STRIPE_CALLBACK_PARAM_WPFS_CHECKOUT_SESSION_ID = 'wpfs-csid';
	const STRIPE_CALLBACK_PARAM_WPFS_STATUS = 'wpfs-status';

	/** @var bool */
	private static $running = false;
	/* @var MM_WPFS_LoggerService */
	private $loggerService = null;
	/* @var MM_WPFS_Logger */
	private $logger = null;
	/** @var $db MM_WPFS_Database */
	private $db = null;
	/** @var int Iteration count for processing entries in the scheduled function */
	private $iterationCount = 5;
	/** @var int Entry count processed in one iteration */
	private $entryCount = 50;
	/** @var int how many times can be an entry processed with error before putting it in INTERNAL_ERROR status */
	private $processErrorLimit = 3;

	/**
	 * MM_WPFS_CheckoutSubmissionService constructor.
	 */
	public function __construct() {
		$this->setup();
		$this->hooks();
		if ( $this->logger->isDebugEnabled() ) {
			$this->logger->debug( __FUNCTION__, 'CALLED, running=' . ( $this->isRunning() ? 'true' : 'false' ) );
		}
	}

	private function setup() {
		$this->db            = new MM_WPFS_Database();
		$this->loggerService = new MM_WPFS_LoggerService();
		$this->logger        = $this->loggerService->createCheckoutSubmissionLogger( MM_WPFS_CheckoutSubmissionService::class );
		// $this->logger->setLevel( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	private function hooks() {
		add_action(
			self::ACTION_FULLSTRIPE_PROCESS_CHECKOUT_SUBMISSIONS,
			array(
				$this,
				'processCheckoutSubmissions'
			)
		);
	}

	/**
	 * @return bool
	 */
	private function isRunning() {
		return self::$running;
	}

	public static function onActivation() {
		if ( ! wp_next_scheduled( self::ACTION_FULLSTRIPE_PROCESS_CHECKOUT_SUBMISSIONS ) ) {
			wp_schedule_event( time(), WP_FULL_STRIPE_CRON_SCHEDULES_KEY_15_MIN, self::ACTION_FULLSTRIPE_PROCESS_CHECKOUT_SUBMISSIONS );
			MM_WPFS_Utils::log( 'MM_WPFS_CheckoutSubmissionService->onActivation(): Event scheduled.' );
		}
	}

	public static function onDeactivation() {
		wp_clear_scheduled_hook( self::ACTION_FULLSTRIPE_PROCESS_CHECKOUT_SUBMISSIONS );
		MM_WPFS_Utils::log( 'MM_WPFS_CheckoutSubmissionService->onDeactivation(): Scheduled event cleared.' );
	}

	/**
	 * @param MM_WPFS_Public_PopupSubscriptionFormModel $subscriptionFormModel
	 *
	 * @return \StripeWPFS\Checkout\Session
	 */
	public function createCheckoutSessionBySubscriptionForm( $subscriptionFormModel ) {
		if ( $this->logger->isDebugEnabled() ) {
			$this->logger->debug( __FUNCTION__, 'CALLED, params: subscriptionFormModel=' . print_r( $subscriptionFormModel, true ) );
		}

		$submitHash = $this->createSubmitEntry( $subscriptionFormModel );

		$checkoutSessionParameters = $this->prepareCheckoutSessionParameterDataForSubscription( $submitHash, $subscriptionFormModel );

		$stripeCheckoutSession = \StripeWPFS\Checkout\Session::create( $checkoutSessionParameters );

		$this->updateSubmitEntryWithSessionIdToPending( $submitHash, $stripeCheckoutSession->id );

		return $stripeCheckoutSession;

	}

	/**
	 * @param MM_WPFS_Public_FormModel $formModel
	 *
	 * @return string
     * @throws Exception
	 */
	private function createSubmitEntry( $formModel ) {
		$options    = get_option( 'fullstripe_options' );
		$liveMode   = $options['apiMode'] === 'live';
		$salt       = wp_generate_password( 16, false );
		$submitId   = time() . '|' . $formModel->getFormHash() . '|' . $liveMode . '|' . $salt;
		$submitHash = hash( 'sha256', $submitId );
		$referrer   = esc_url(
			add_query_arg(
				array(
					self::STRIPE_CALLBACK_PARAM_WPFS_POPUP_FORM_SUBMIT_HASH => $submitHash
				),
				$formModel->getReferrer()
			) . '#' . \MM_WPFS_FormViewConstants::ATTR_ID_VALUE_PREFIX . $formModel->getFormHash()
		);
		$this->db->insert_popup_form_submit(
			$submitHash,
			$formModel->getFormHash(),
			MM_WPFS_Utils::getFormType( $formModel->getForm() ),
			$referrer,
			json_encode( $formModel->getPostData(), JSON_UNESCAPED_UNICODE ),
			$liveMode
		);

		return $submitHash;
	}

	/**
	 * @param string $submitHash
	 * @param MM_WPFS_Public_PopupSubscriptionFormModel $subscriptionFormModel
	 *
	 * @return array
	 */
	private function prepareCheckoutSessionParameterDataForSubscription( $submitHash, $subscriptionFormModel ) {
		$collectBillingAddress = isset( $subscriptionFormModel->getForm()->showBillingAddress ) && 1 == $subscriptionFormModel->getForm()->showBillingAddress ? 'required' : 'auto';
		$subscriptionDataArray = array(
			'items'    => array(
				array(
					'plan'     => $subscriptionFormModel->getStripePlan()->id,
					'quantity' => $subscriptionFormModel->getStripePlanQuantity()
				)
			),
			'metadata' => array(
				'client_reference_id' => $submitHash
			)
		);
		$trialPeriodDays       = MM_WPFS_Utils::get_trial_period_days_for_plan( $subscriptionFormModel->getStripePlan() );
		if ( ! is_null( $trialPeriodDays ) ) {
			$subscriptionDataArray['trial_period_days'] = $trialPeriodDays;
		}
		$checkoutSessionParameters = array(
			'payment_method_types'       => array( 'card' ),
			'mode'                       => 'subscription',
			'subscription_data'          => $subscriptionDataArray,
			'client_reference_id'        => $submitHash,
			'billing_address_collection' => $collectBillingAddress,
			'success_url'                => $this->buildCheckoutSessionSuccessURL( $submitHash ),
			'cancel_url'                 => $this->buildCheckoutSessionCancelURL( $submitHash )
		);
		$options                   = get_option( 'fullstripe_options' );
		$lock_email                = $options['lock_email_field_for_logged_in_users'];
		$email_address             = '';
		$is_user_logged_in         = is_user_logged_in();
		if ( '1' == $lock_email && $is_user_logged_in ) {
			$current_user  = wp_get_current_user();
			$email_address = $current_user->user_email;
		}
		if ( ! empty( $email_address ) ) {
			$checkoutSessionParameters['customer_email'] = $email_address;
		}
		if ( isset( $subscriptionFormModel->getForm()->preferredLanguage ) ) {
			$checkoutSessionParameters['locale'] = $subscriptionFormModel->getForm()->preferredLanguage;
		}
		$setupFee = MM_WPFS_Utils::get_setup_fee_for_plan( $subscriptionFormModel->getStripePlan() );
		if ( $setupFee > 0 ) {
			$checkoutSessionParameters['line_items'] = array(
				array(
					'amount'      => $setupFee,
					'currency'    => $subscriptionFormModel->getStripePlan()->currency,
					'name'        => sprintf(
					    /* translators: It's a line item for the initial payment of a subscription  */
						__( 'One-time setup fee (plan: %s)', MM_WPFS::L10N_DOMAIN_PUBLIC ),
						$subscriptionFormModel->getStripePlan()->id
					),
					'description' => sprintf(
					    // It's an internal description, no need to localize it
						'Subscription plan: %s, quantity: %d',
						$subscriptionFormModel->getStripePlan()->id,
						$subscriptionFormModel->getStripePlanQuantity()
					),
					'quantity'    => $subscriptionFormModel->getStripePlanQuantity()
				)
			);
		}

		return $checkoutSessionParameters;
	}

	/**
	 * @param $submitHash
	 *
	 * @return string
	 */
	private function buildCheckoutSessionSuccessURL( $submitHash ) {
		return $this->buildCheckoutSessionStatusURL( $submitHash, self::CHECKOUT_SESSION_STATUS_SUCCESS );
	}

	/**
	 * @param $submitHash
	 * @param $status
	 *
	 * @return string
	 */
	private function buildCheckoutSessionStatusURL( $submitHash, $status ) {
		return add_query_arg(
			array(
				'action'                                                => 'wp_full_stripe_handle_checkout_session',
				self::STRIPE_CALLBACK_PARAM_WPFS_POPUP_FORM_SUBMIT_HASH => $submitHash,
				// self::STRIPE_CALLBACK_PARAM_WPFS_CHECKOUT_SESSION_ID    => '{CHECKOUT_SESSION_ID}',
				self::STRIPE_CALLBACK_PARAM_WPFS_STATUS                 => $status
			),
			admin_url( 'admin-ajax.php' )
		);
	}

	/**
	 * @param $submitHash
	 *
	 * @return string
	 */
	protected function buildCheckoutSessionCancelURL( $submitHash ) {
		return $this->buildCheckoutSessionStatusURL( $submitHash, self::CHECKOUT_SESSION_STATUS_CANCELLED );
	}

	/**
	 * @param $submitHash
	 * @param $stripeCheckoutSessionId
	 *
	 * @return false|int
	 */
	public function updateSubmitEntryWithSessionIdToPending( $submitHash, $stripeCheckoutSessionId ) {
		return $this->db->update_popup_form_submit_by_hash(
			$submitHash,
			array(
				'checkoutSessionId' => $stripeCheckoutSessionId,
				'status'            => self::POPUP_FORM_SUBMIT_STATUS_PENDING
			)
		);
	}

	/**
	 * @param MM_WPFS_Public_PopupPaymentFormModel $paymentFormModel
	 *
	 * @return \StripeWPFS\Checkout\Session
	 */
	public function createCheckoutSessionByPaymentForm( $paymentFormModel ) {
		if ( $this->logger->isDebugEnabled() ) {
			$this->logger->debug( __FUNCTION__, 'CALLED, params: paymentFormModel=' . print_r( $paymentFormModel, true ) );
		}

		$submitHash = $this->createSubmitEntry( $paymentFormModel );

		$checkoutSessionParameters = $this->prepareCheckoutSessionParameterDataForPayment( $submitHash, $paymentFormModel );

		$stripeCheckoutSession = \StripeWPFS\Checkout\Session::create( $checkoutSessionParameters );

		$this->updateSubmitEntryWithSessionIdToPending( $submitHash, $stripeCheckoutSession->id );

		return $stripeCheckoutSession;
	}

    /**
     * @param MM_WPFS_Public_PopupDonationFormModel $donationFormModel
     *
     * @return \StripeWPFS\Checkout\Session
     * @throws Exception
     */
    public function createCheckoutSessionByDonationForm($donationFormModel ) {
        if ( $this->logger->isDebugEnabled() ) {
            $this->logger->debug( __FUNCTION__, 'CALLED, params: donationFormModel=' . print_r( $donationFormModel, true ) );
        }

        $submitHash = $this->createSubmitEntry( $donationFormModel );

        $checkoutSessionParameters = $this->prepareCheckoutSessionParameterDataForDonation( $submitHash, $donationFormModel );

        $stripeCheckoutSession = \StripeWPFS\Checkout\Session::create( $checkoutSessionParameters );

        $this->updateSubmitEntryWithSessionIdToPending( $submitHash, $stripeCheckoutSession->id );

        return $stripeCheckoutSession;
    }

    /**
	 * @param string $submitHash
	 * @param MM_WPFS_Public_PopupPaymentFormModel $paymentFormModel
	 *
	 * @return array
	 */
	private function prepareCheckoutSessionParameterDataForPayment( $submitHash, $paymentFormModel ) {
		$collectBillingAddress     = isset( $paymentFormModel->getForm()->showBillingAddress ) && 1 == $paymentFormModel->getForm()->showBillingAddress ? 'required' : 'auto';
		$checkoutSessionParameters = array(
			'payment_method_types'       => array( 'card' ),
			'client_reference_id'        => $submitHash,
			'billing_address_collection' => $collectBillingAddress,
			'success_url'                => $this->buildCheckoutSessionSuccessURL( $submitHash ),
			'cancel_url'                 => $this->buildCheckoutSessionCancelURL( $submitHash )
		);
		if ( MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE === $paymentFormModel->getForm()->customAmount ) {
			$checkoutSessionParameters['mode'] = 'setup';
		} else {
			$checkoutSessionParameters['mode'] = 'payment';
			$lineItem                          = array(
				'amount'   => $paymentFormModel->getAmount(),
				'currency' => $paymentFormModel->getForm()->currency,
				'quantity' => 1,
				'name'     => $paymentFormModel->getProductName()
			);
			if ( isset( $paymentFormModel->getForm()->companyName ) && ! empty( $paymentFormModel->getForm()->companyName ) ) {
				$lineItem['description'] = $paymentFormModel->getForm()->companyName;
			}
			$imagesValueArray = $this->prepareImagesValueArray( $paymentFormModel->getForm() );
			if ( count( $imagesValueArray ) > 0 ) {
				$lineItem['images'] = $imagesValueArray;
			}
			$checkoutSessionParameters['line_items'] = array( $lineItem );
			$captureAmount                           = MM_WPFS_Utils::prepareCaptureIntentByFormModel( $paymentFormModel );
			if ( false === $captureAmount ) {
				$checkoutSessionParameters['payment_intent_data'] = array(
					'capture_method' => 'manual'
				);
			}
		}
		// tnagy get logged in user's email address
		$options        = get_option( 'fullstripe_options' );
		$lockEmail      = $options['lock_email_field_for_logged_in_users'];
		$emailAddress   = '';
		$isUserLoggedIn = is_user_logged_in();
		if ( '1' == $lockEmail && $isUserLoggedIn ) {
			$currentUser  = wp_get_current_user();
			$emailAddress = $currentUser->user_email;
		}
		if ( ! empty( $emailAddress ) ) {
			$checkoutSessionParameters['customer_email'] = $emailAddress;
		}
		if ( isset( $paymentFormModel->getForm()->preferredLanguage ) ) {
			$checkoutSessionParameters['locale'] = $paymentFormModel->getForm()->preferredLanguage;
		}

		return $checkoutSessionParameters;
	}

    /**
     * @param string $submitHash
     * @param MM_WPFS_Public_PopupDonationFormModel $donationFormModel
     *
     * @return array
     */
    private function prepareCheckoutSessionParameterDataForDonation($submitHash, $donationFormModel ) {
        $collectBillingAddress     = isset( $donationFormModel->getForm()->showBillingAddress ) && 1 == $donationFormModel->getForm()->showBillingAddress ? 'required' : 'auto';
        $checkoutSessionParameters = array(
            'payment_method_types'       => array( 'card' ),
            'client_reference_id'        => $submitHash,
            'billing_address_collection' => $collectBillingAddress,
            'success_url'                => $this->buildCheckoutSessionSuccessURL( $submitHash ),
            'cancel_url'                 => $this->buildCheckoutSessionCancelURL( $submitHash )
        );

        $checkoutSessionParameters['mode'] = 'payment';
        $lineItem                          = array(
            'amount'   => $donationFormModel->getAmount(),
            'currency' => $donationFormModel->getForm()->currency,
            'quantity' => 1,
            'name'     => $donationFormModel->getProductName()
        );
        if ( isset( $donationFormModel->getForm()->companyName ) && ! empty( $donationFormModel->getForm()->companyName ) ) {
            $lineItem['description'] = $donationFormModel->getForm()->companyName;
        }
        $imagesValueArray = $this->prepareImagesValueArray( $donationFormModel->getForm() );
        if ( count( $imagesValueArray ) > 0 ) {
            $lineItem['images'] = $imagesValueArray;
        }
        $checkoutSessionParameters['line_items'] = array( $lineItem );

        // tnagy get logged in user's email address
        $options        = get_option( 'fullstripe_options' );
        $lockEmail      = $options['lock_email_field_for_logged_in_users'];
        $emailAddress   = '';
        $isUserLoggedIn = is_user_logged_in();
        if ( '1' == $lockEmail && $isUserLoggedIn ) {
            $currentUser  = wp_get_current_user();
            $emailAddress = $currentUser->user_email;
        }
        if ( ! empty( $emailAddress ) ) {
            $checkoutSessionParameters['customer_email'] = $emailAddress;
        }
        if ( isset( $donationFormModel->getForm()->preferredLanguage ) ) {
            $checkoutSessionParameters['locale'] = $donationFormModel->getForm()->preferredLanguage;
        }

        return $checkoutSessionParameters;
    }

    /**
	 * @param $popupPaymentForm
	 *
	 * @return array
	 */
	private function prepareImagesValueArray( $popupPaymentForm ) {
		$imagesValue = array();
		if ( empty( $popupPaymentForm->image ) ) {
			// $imagesValue = [ self::DEFAULT_CHECKOUT_LINE_ITEM_IMAGE ];
		} else {
			array_push( $imagesValue, $popupPaymentForm->image );
		}

		return $imagesValue;
	}

	/**
	 * @param $submitHash
	 *
	 * @return array|null|object|void
	 */
	public function retrieveSubmitEntry( $submitHash ) {
		return $this->db->find_popup_form_submit_by_hash( $submitHash );
	}

	public function processCheckoutSubmissions() {
		if ( $this->logger->isDebugEnabled() ) {
			$this->logger->debug( __FUNCTION__, 'CALLED' );
		}
		try {

			if ( ! $this->isRunning() ) {
				$this->start();
				$this->findAndProcessSubmissions();
				$this->stop();
			}

		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$this->logger->error( __FUNCTION__, $e->getMessage(), $e );
			$this->stop();
		}
		if ( $this->logger->isDebugEnabled() ) {
			$this->logger->debug( __FUNCTION__, 'FINISHED' );
		}
	}

	private function start() {
		self::$running = true;
	}

	private function findAndProcessSubmissions() {
		$iteration                  = 0;
		$popupFormSubmitIdsToFaulty = array();
		$popupFormSubmitsToProcess  = array();
		$popupFormSubmitsToComplete = array();
		$popupFormSubmitIdsToDelete = array();
		$popupFormSubmitsTouched    = array();

		$popupFormSubmits = $this->findPopupEntries();
		if ( isset( $popupFormSubmits ) ) {

			if ( $this->logger->isDebugEnabled() ) {
				$this->logger->debug( __FUNCTION__, 'Found ' . count( $popupFormSubmits ) . ' record(s) to process.' );
			}

			while ( $iteration < $this->iterationCount && count( $popupFormSubmits ) > 0 ) {
				$iteration ++;

				// tnagy prepare array of submits
				if ( ! is_array( $popupFormSubmits ) ) {
					$popupFormSubmits = array( $popupFormSubmits );
				}

				// tnagy sort out submits by status
				foreach ( $popupFormSubmits as $popupFormSubmit ) {

					if ( ! array_key_exists( $popupFormSubmit->id, $popupFormSubmitsTouched ) ) {

						if ( $this->logger->isDebugEnabled() ) {
							$this->logger->debug( __FUNCTION__, 'Processing Checkout Form Submission=' . print_r( $popupFormSubmit, true ) );
						}

						// tnagy mark record as touched
						$popupFormSubmitsTouched[ $popupFormSubmit->id ] = $popupFormSubmit;

						if ( $popupFormSubmit->processedWithError > $this->processErrorLimit ) {
							array_push( $popupFormSubmitIdsToFaulty, $popupFormSubmit->id );
							if ( $this->logger->isDebugEnabled() ) {
								$this->logger->debug( __FUNCTION__, 'Proposed to FAULTY.' );
							}
						} elseif ( self::POPUP_FORM_SUBMIT_STATUS_CREATED === $popupFormSubmit->status ) {
							array_push( $popupFormSubmitsToProcess, $popupFormSubmit );
							if ( $this->logger->isDebugEnabled() ) {
								$this->logger->debug( __FUNCTION__, 'Proposed to PROCESS.' );
							}
						} elseif ( self::POPUP_FORM_SUBMIT_STATUS_PENDING === $popupFormSubmit->status ) {
							array_push( $popupFormSubmitsToProcess, $popupFormSubmit );
							if ( $this->logger->isDebugEnabled() ) {
								$this->logger->debug( __FUNCTION__, 'Proposed to PROCESS.' );
							}
						} elseif ( self::POPUP_FORM_SUBMIT_STATUS_SUCCESS === $popupFormSubmit->status ) {
							array_push( $popupFormSubmitsToComplete, $popupFormSubmit );
							if ( $this->logger->isDebugEnabled() ) {
								$this->logger->debug( __FUNCTION__, 'Proposed to PROCESS.' );
							}
						} elseif ( self::POPUP_FORM_SUBMIT_STATUS_FAILED === $popupFormSubmit->status ) {
							array_push( $popupFormSubmitIdsToDelete, $popupFormSubmit->id );
							if ( $this->logger->isDebugEnabled() ) {
								$this->logger->debug( __FUNCTION__, 'Proposed to DELETE.' );
							}
						} elseif ( self::POPUP_FORM_SUBMIT_STATUS_COMPLETE === $popupFormSubmit->status ) {
							array_push( $popupFormSubmitIdsToDelete, $popupFormSubmit->id );
							if ( $this->logger->isDebugEnabled() ) {
								$this->logger->debug( __FUNCTION__, 'Proposed to DELETE.' );
							}
						}
					}

				}

				// tnagy process submits
				foreach ( $popupFormSubmitsToProcess as $popupFormSubmit ) {
					$result = $this->processSinglePopupFormSubmit( $popupFormSubmit );
					if ( self::PROCESS_RESULT_SET_TO_SUCCESS === $result ) {
						if ( $this->logger->isDebugEnabled() ) {
							$this->logger->debug( __FUNCTION__, 'Checkout Form Submission successfully processed.' );
						}
						array_push( $popupFormSubmitsToComplete, $popupFormSubmit );
						if ( $this->logger->isDebugEnabled() ) {
							$this->logger->debug( __FUNCTION__, 'Proposed to COMPLETE.' );
						}
					} elseif ( self::PROCESS_RESULT_SET_TO_FAILED === $result ) {
						if ( $this->logger->isDebugEnabled() ) {
							$this->logger->debug( __FUNCTION__, 'Checkout Form Submission processing failed.' );
						}
						array_push( $popupFormSubmitIdsToDelete, $popupFormSubmit->id );
						if ( $this->logger->isDebugEnabled() ) {
							$this->logger->debug( __FUNCTION__, 'Proposed to DELETE.' );
						}
					} elseif ( self::PROCESS_RESULT_EXPIRED === $result ) {
						if ( $this->logger->isDebugEnabled() ) {
							$this->logger->debug( __FUNCTION__, 'CheckoutSession expired for Checkout Form Submission.' );
						}
						$this->updateSubmitEntryWithCancelled( $popupFormSubmit );
						array_push( $popupFormSubmitIdsToDelete, $popupFormSubmit->id );
						if ( $this->logger->isDebugEnabled() ) {
							$this->logger->debug( __FUNCTION__, 'Proposed to DELETE.' );
						}
					} elseif ( self::PROCESS_RESULT_WAIT_FOR_STATUS_CHANGE === $result ) {
						if ( $this->logger->isDebugEnabled() ) {
							$this->logger->debug( __FUNCTION__, 'Checkout Form Submission skipped, waiting for status change.' );
						}
					} elseif ( self::PROCESS_RESULT_INTERNAL_ERROR === $result ) {
						$this->logger->error( __FUNCTION__, 'Internal error occurred during Checkout Form Submission.' );
					}
				}

				// tnagy complete submits
				foreach ( $popupFormSubmitsToComplete as $popupFormSubmit ) {
					$this->updateSubmitEntryWithComplete( $popupFormSubmit );
					array_push( $popupFormSubmitIdsToDelete, $popupFormSubmit->id );
					if ( $this->logger->isDebugEnabled() ) {
						$this->logger->debug( __FUNCTION__, 'Proposed to DELETE.' );
					}
				}

				// tnagy delete submits
				$deleted = $this->deleteSubmitEntriesById( $popupFormSubmitIdsToDelete );
				if ( $this->logger->isDebugEnabled() ) {
					$this->logger->debug( __FUNCTION__, 'Deleted ' . $deleted . ' Checkout Form Submission(s).' );
				}

				// tnagy mark submits as faulty
				$faulty = $this->updateSubmitEntriesWithInternalError( $popupFormSubmitIdsToFaulty );
				if ( $this->logger->isDebugEnabled() ) {
					$this->logger->debug( __FUNCTION__, 'Marked as FAULTY ' . $faulty . ' Checkout Form Submission(s).' );
				}

				// tnagy clear arrays
				$popupFormSubmitIdsToFaulty = array();
				$popupFormSubmitsToProcess  = array();
				$popupFormSubmitsToComplete = array();
				$popupFormSubmitIdsToDelete = array();

				// tnagy load next fragment of submits
				$popupFormSubmits = $this->findPopupEntries();
			}
		}

	}

	/**
	 * @return array|null|object
	 */
	private function findPopupEntries() {
		$options  = get_option( 'fullstripe_options' );
		$liveMode = $options['apiMode'] === 'live';

		return $this->db->find_popup_form_submits( $liveMode, $this->entryCount );
	}

	/**
	 * @param $popupFormSubmit
	 *
	 * @return int
	 */
	private function processSinglePopupFormSubmit( $popupFormSubmit ) {
		try {
			if ( isset( $popupFormSubmit->checkoutSessionId ) ) {
				$checkoutSession = $this->retrieveCheckoutSession( $popupFormSubmit->checkoutSessionId );
				$paymentIntent   = $this->findPaymentIntentInCheckoutSession( $checkoutSession );
				if ( isset( $paymentIntent ) && \StripeWPFS\PaymentIntent::STATUS_SUCCEEDED === $paymentIntent->status ) {
					$formModel             = null;
					$checkoutChargeHandler = null;
					if (
						MM_WPFS::FORM_TYPE_POPUP_PAYMENT === $popupFormSubmit->formType
						|| MM_WPFS::FORM_TYPE_POPUP_SAVE_CARD === $popupFormSubmit->formType
					) {
						$formModel             = new MM_WPFS_Public_PopupPaymentFormModel();
						$checkoutChargeHandler = new MM_WPFS_CheckoutPaymentChargeHandler();
					} elseif ( MM_WPFS::FORM_TYPE_POPUP_SUBSCRIPTION === $popupFormSubmit->formType ) {
						$formModel             = new MM_WPFS_Public_PopupSubscriptionFormModel();
						$checkoutChargeHandler = new MM_WPFS_CheckoutSubscriptionChargeHandler();
					} elseif ( MM_WPFS::FORM_TYPE_POPUP_DONATION === $popupFormSubmit->formType ) {
                        $formModel             = new MM_WPFS_Public_PopupDonationFormModel();
                        $checkoutChargeHandler = new MM_WPFS_CheckoutDonationChargeHandler();
                    }
					if ( ! is_null( $formModel ) && ! is_null( $checkoutChargeHandler ) ) {
						$postData            = $formModel->extractFormModelDataFromPopupFormSubmit( $popupFormSubmit );
						$checkoutSessionData = $formModel->extractFormModelDataFromCheckoutSession( $checkoutSession );
						$postData            = array_merge( $postData, $checkoutSessionData );
						$formModel->bindByArray(
							$postData
						);
						if ( $this->logger->isDebugEnabled() ) {
							$this->logger->debug( __FUNCTION__, 'formModel=' . print_r( $formModel, true ) );
						}
						$chargeResult = $checkoutChargeHandler->handle( $formModel, $checkoutSession );
						if ( $chargeResult->isSuccess() ) {
							$this->updateSubmitEntryWithSuccess( $popupFormSubmit, $chargeResult->getMessageTitle(), $chargeResult->getMessage() );

							return self::PROCESS_RESULT_SET_TO_SUCCESS;
						} else {
							$this->updateSubmitEntryWithFailed( $popupFormSubmit );

							return self::PROCESS_RESULT_SET_TO_FAILED;
						}
					} else {
						$errorMessage = 'Unknown formType=' . $popupFormSubmit->formType;
						MM_WPFS_Utils::log( 'processSinglePopupFormSubmit(): ' . $errorMessage );
						$this->updateSubmitEntryWithErrorCount( $popupFormSubmit, $errorMessage );
						$this->logger->error( __FUNCTION__, $errorMessage );

						return self::PROCESS_RESULT_INTERNAL_ERROR;
					}
				}
			}

			// tnagy check expiration
			$expirationDate = time() - 24 * 60 * 60;
			$creationDate   = strtotime( $popupFormSubmit->created );

			if ( $creationDate < $expirationDate ) {
				return self::PROCESS_RESULT_EXPIRED;
			}

		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$this->updateSubmitEntryWithErrorCount( $popupFormSubmit, $e->getMessage() );
			$this->logger->error( __FUNCTION__, $e->getMessage(), $e );

			return self::PROCESS_RESULT_INTERNAL_ERROR;
		}

		return self::PROCESS_RESULT_WAIT_FOR_STATUS_CHANGE;
	}

	/**
	 * @param $checkoutSessionId
	 *
	 * @return \StripeWPFS\Checkout\Session
	 */
	public function retrieveCheckoutSession( $checkoutSessionId ) {
		$checkoutSession = \StripeWPFS\Checkout\Session::retrieve(
			array(
				'id'     => $checkoutSessionId,
				'expand' => array(
					'customer',
					'payment_intent',
					'payment_intent.payment_method',
					'setup_intent',
					'setup_intent.payment_method',
					'subscription',
					'subscription.latest_invoice.payment_intent',
					'subscription.pending_setup_intent',
				)
			)
		);

		return $checkoutSession;
	}

	/**
	 * @param \StripeWPFS\Checkout\Session $checkoutSession
	 *
	 * @return null|\StripeWPFS\PaymentIntent
	 */
	public function findPaymentIntentInCheckoutSession( $checkoutSession ) {
		$paymentIntent = null;
		if ( isset( $checkoutSession ) ) {
			$paymentIntent = $this->retrieveStripePaymentIntentByCheckoutSession( $checkoutSession );
			if ( is_null( $paymentIntent ) ) {
				$stripeSubscription = $this->retrieveStripeSubscriptionByCheckoutSession( $checkoutSession );
				if ( $this->logger->isDebugEnabled() ) {
					$this->logger->debug( __FUNCTION__, 'subscription=' . print_r( $stripeSubscription, true ) );
				}
				$paymentIntent = $this->findPaymentIntentInSubscription( $stripeSubscription );
				if ( $this->logger->isDebugEnabled() ) {
					$this->logger->debug( __FUNCTION__, 'paymentIntent=' . print_r( $paymentIntent, true ) );
				}
			}
		}

		return $paymentIntent;
	}

	/**
	 * @param \StripeWPFS\Checkout\Session $checkoutSession
	 *
	 * @return \StripeWPFS\PaymentIntent
	 */
	public function retrieveStripePaymentIntentByCheckoutSession( $checkoutSession ) {
		$stripePaymentIntent = null;
		if ( isset( $checkoutSession ) ) {
			if ( isset( $checkoutSession->payment_intent ) ) {
				if ( $checkoutSession->payment_intent instanceof \StripeWPFS\PaymentIntent ) {
					$stripePaymentIntent = $checkoutSession->payment_intent;
				} else {
					$stripePaymentIntent = \StripeWPFS\PaymentIntent::retrieve( $checkoutSession->payment_intent );
				}
			}
		}

		return $stripePaymentIntent;
	}

	/**
	 * @param \StripeWPFS\Checkout\Session $checkoutSession
	 *
	 * @return \StripeWPFS\Subscription
	 */
	public function retrieveStripeSubscriptionByCheckoutSession( $checkoutSession ) {
		$stripeSubscription = null;
		if ( isset( $checkoutSession ) ) {
			if ( isset( $checkoutSession->subscription ) ) {
			    $subscriptionId = null;
				if ( $checkoutSession->subscription instanceof \StripeWPFS\Subscription ) {
                    $subscriptionId = $checkoutSession->subscription->id;
				} else {
                    $subscriptionId = $checkoutSession->subscription;
                }

                $stripeSubscription = \StripeWPFS\Subscription::retrieve(
                    array(
                        'id'     => $subscriptionId,
                        'expand' => array(
                            'latest_invoice',
                            'latest_invoice.payment_intent',
                            'pending_setup_intent'
                        )
                    )
                );
			}
		}

		return $stripeSubscription;
	}

	/**
	 * @param \StripeWPFS\Subscription $stripeSubscription
	 *
	 * @return null|\StripeWPFS\PaymentIntent
	 */
	public function findPaymentIntentInSubscription( $stripeSubscription ) {
		$paymentIntent = null;
		if ( isset( $stripeSubscription ) ) {
			if ( isset( $stripeSubscription->latest_invoice ) ) {
				$stripeInvoice = null;
				if ( $stripeSubscription->latest_invoice instanceof \StripeWPFS\Invoice ) {
					$stripeInvoice = $stripeSubscription->latest_invoice;
				} else {
					$stripeInvoice = \StripeWPFS\Invoice::retrieve(
						$stripeSubscription->latest_invoice,
						array(
							'expand' => array(
								'payment_intent'
							)
						)
					);
				}
				if ( isset( $stripeInvoice->payment_intent ) ) {
					if ( $stripeInvoice->payment_intent instanceof \StripeWPFS\PaymentIntent ) {
						$paymentIntent = $stripeInvoice->payment_intent;
					} else {
						$paymentIntent = \StripeWPFS\PaymentIntent::retrieve( $stripeInvoice->payment_intent );
					}
				}
			}
		}

		return $paymentIntent;
	}

	/**
	 * @param $popupFormSubmit
	 * @param string $lastMessageTitle
	 * @param string $lastMessage
	 *
	 * @return false|int
	 */
	public function updateSubmitEntryWithSuccess( $popupFormSubmit, $lastMessageTitle, $lastMessage ) {
		if ( is_null( $popupFormSubmit ) ) {
			return false;
		}

		return $this->db->update_popup_form_submit_by_hash(
			$popupFormSubmit->hash,
			array(
				'status'           => self::POPUP_FORM_SUBMIT_STATUS_SUCCESS,
				'lastMessageTitle' => $lastMessageTitle,
				'lastMessage'      => $lastMessage
			)
		);
	}

	/**
	 * @param $popupFormSubmit
	 * @param $lastMessageTitle
	 * @param $lastMessage
	 *
	 * @return false|int
	 */
	public function updateSubmitEntryWithFailed( $popupFormSubmit, $lastMessageTitle = null, $lastMessage = null ) {
		if ( is_null( $popupFormSubmit ) ) {
			return false;
		}

		if ( is_null( $lastMessageTitle ) ) {
            // It's an internal message, we don't localize it
			$lastMessageTitle = 'Failed';
		}
		if ( is_null( $lastMessage ) ) {
            // It's an internal message, we don't localize it
			$lastMessage = 'Payment failed!';
		}

		return $this->db->update_popup_form_submit_by_hash(
			$popupFormSubmit->hash,
			array(
				'status'           => self::POPUP_FORM_SUBMIT_STATUS_FAILED,
				'lastMessageTitle' => $lastMessageTitle,
				'lastMessage'      => $lastMessage
			)
		);
	}

	/**
	 * @param $popupFormSubmit
	 * @param null $errorMessage
	 *
	 * @return bool|false|int
	 */
	public function updateSubmitEntryWithErrorCount( $popupFormSubmit, $errorMessage = null ) {
		if ( is_null( $popupFormSubmit ) ) {
			return false;
		}

		return $this->db->update_popup_form_submit_by_hash(
			$popupFormSubmit->hash,
			array(
				'processedWithError' => $popupFormSubmit->processedWithError + 1,
				'errorMessage'       => $errorMessage
			)
		);
	}

	/**
	 * @param $popupFormSubmit
	 *
	 * @return false|int
     * @throws Exception
	 */
	public function updateSubmitEntryWithCancelled( $popupFormSubmit ) {
		if ( is_null( $popupFormSubmit ) ) {
			return false;
		}

		return $this->db->update_popup_form_submit_by_hash(
			$popupFormSubmit->hash,
			array(
				'status'           => self::CHECKOUT_SESSION_STATUS_CANCELLED,
				'lastMessageTitle' =>
                    /* translators: Banner title of cancelled transaction */
                    __( 'Cancelled', MM_WPFS::L10N_DOMAIN_PUBLIC ),
				'lastMessage'      =>
                    /* translators: Banner message of cancelled transaction */
                    __( 'The customer has cancelled the payment.', MM_WPFS::L10N_DOMAIN_PUBLIC )
			)
		);
	}

    /**
	 * @param $popupFormSubmit
	 *
	 * @return false|int
	 */
	public function updateSubmitEntryWithComplete( $popupFormSubmit ) {
		if ( $this->logger->isDebugEnabled() ) {
			$this->logger->debug( __FUNCTION__, 'CALLED, popupFormSubmit=' . print_r( $popupFormSubmit, true ) );
		}

		if ( is_null( $popupFormSubmit ) ) {
			return false;
		}

		return $this->db->update_popup_form_submit_by_hash(
			$popupFormSubmit->hash,
			array(
				'status' => self::POPUP_FORM_SUBMIT_STATUS_COMPLETE
			)
		);
	}

	/**
	 * @param $popupFormSubmitIdsToDelete
	 *
	 * @return int
	 */
	private function deleteSubmitEntriesById( $popupFormSubmitIdsToDelete ) {
		$deleted = 0;
		if (
			isset( $popupFormSubmitIdsToDelete )
			&& is_array( $popupFormSubmitIdsToDelete )
			&& sizeof( $popupFormSubmitIdsToDelete ) > 0
		) {
			$deleted = $this->db->delete_popup_form_submits_by_id( $popupFormSubmitIdsToDelete );
		}

		return $deleted;
	}

	/**
	 * @param $popupFormSubmitIdsToInternalError
	 *
	 * @return int
	 */
	private function updateSubmitEntriesWithInternalError( $popupFormSubmitIdsToInternalError ) {
		$updated = 0;
		if (
			isset( $popupFormSubmitIdsToInternalError )
			&& is_array( $popupFormSubmitIdsToInternalError )
			&& sizeof( $popupFormSubmitIdsToInternalError ) > 0
		) {
			$updated = $this->db->update_popup_form_submits_with_status_by_id(
				MM_WPFS_CheckoutSubmissionService::POPUP_FORM_SUBMIT_STATUS_INTERNAL_ERROR,
				$popupFormSubmitIdsToInternalError
			);
		}

		return $updated;
	}

	private function stop() {
		self::$running = false;
	}

	/**
	 * @param \StripeWPFS\Checkout\Session $checkoutSession
	 *
	 * @return null|\StripeWPFS\SetupIntent
	 */
	public function findSetupIntentInCheckoutSession( $checkoutSession ) {
		$setupIntent = null;
		if ( isset( $checkoutSession ) ) {
			$setupIntent = $this->retrieveStripeSetupIntentByCheckoutSession( $checkoutSession );
			if ( is_null( $setupIntent ) ) {
				$stripeSubscription = $this->retrieveStripeSubscriptionByCheckoutSession( $checkoutSession );
				if ( $this->logger->isDebugEnabled() ) {
					$this->logger->debug( __FUNCTION__, 'subscription=' . print_r( $stripeSubscription, true ) );
				}
				$setupIntent = $this->findSetupIntentInSubscription( $stripeSubscription );
				if ( $this->logger->isDebugEnabled() ) {
					$this->logger->debug( __FUNCTION__, 'setupIntent=' . print_r( $setupIntent, true ) );
				}
			}
		}

		return $setupIntent;
	}

	/**
	 * @param \StripeWPFS\Checkout\Session $checkoutSession
	 *
	 * @return \StripeWPFS\SetupIntent
	 */
	public function retrieveStripeSetupIntentByCheckoutSession( $checkoutSession ) {
		$stripeSetupIntent = null;
		if ( isset( $checkoutSession ) ) {
			if ( isset( $checkoutSession->setup_intent ) ) {
				if ( $checkoutSession->setup_intent instanceof \StripeWPFS\SetupIntent ) {
					$stripeSetupIntent = $checkoutSession->setup_intent;
				} else {
					$stripeSetupIntent = \StripeWPFS\SetupIntent::retrieve( $checkoutSession->setup_intent );
				}
			}
		}

		return $stripeSetupIntent;
	}

	/**
	 * @param \StripeWPFS\Subscription $stripeSubscription
	 *
	 * @return null|\StripeWPFS\PaymentIntent
	 */
	public function findSetupIntentInSubscription( $stripeSubscription ) {
		$setupIntent = null;
		if ( isset( $stripeSubscription ) ) {
			if ( isset( $stripeSubscription->pending_setup_intent ) ) {
				if ( $stripeSubscription->pending_setup_intent instanceof \StripeWPFS\SetupIntent ) {
					$setupIntent = $stripeSubscription->pending_setup_intent;
				} else {
					$setupIntent = \StripeWPFS\SetupIntent::retrieve( $stripeSubscription->pending_setup_intent );
				}
			}
		}

		return $setupIntent;
	}

	/**
	 * @param \StripeWPFS\Checkout\Session $checkoutSession
	 *
	 * @return \StripeWPFS\Customer
	 */
	public function retrieveStripeCustomerByCheckoutSession( $checkoutSession ) {
		$stripeCustomer = null;
		if ( isset( $checkoutSession ) ) {
			if ( isset( $checkoutSession->customer ) ) {
				if ( $checkoutSession->customer instanceof \StripeWPFS\Customer ) {
					$stripeCustomer = $checkoutSession->customer;
				} else {
					$stripeCustomer = \StripeWPFS\Customer::retrieve( $checkoutSession->customer );
				}
			}
		}

		return $stripeCustomer;
	}

	/**
	 * @param \StripeWPFS\SetupIntent $setupIntent
	 *
	 * @return null|\StripeWPFS\PaymentMethod
	 */
	public function retrieveStripePaymentMethodBySetupIntent( $setupIntent ) {
		$stripePaymentMethod = null;
		if ( isset( $setupIntent ) && $setupIntent instanceof \StripeWPFS\SetupIntent ) {
			if ( isset( $setupIntent->payment_method ) ) {
				if ( $setupIntent->payment_method instanceof \StripeWPFS\PaymentMethod ) {
					$stripePaymentMethod = $setupIntent->payment_method;
				} else {
					$stripePaymentMethod = \StripeWPFS\PaymentMethod::retrieve( $setupIntent->payment_method );
				}
			}
		}

		return $stripePaymentMethod;
	}

	/**
	 * @param \StripeWPFS\PaymentIntent $paymentIntent
	 *
	 * @return null|\StripeWPFS\PaymentMethod
	 */
	public function retrieveStripePaymentMethodByPaymentIntent( $paymentIntent ) {
		$stripePaymentMethod = null;
		if ( isset( $paymentIntent ) && $paymentIntent instanceof \StripeWPFS\PaymentIntent ) {
			if ( isset( $paymentIntent->payment_method ) ) {
				if ( $paymentIntent->payment_method instanceof \StripeWPFS\PaymentMethod ) {
					$stripePaymentMethod = $paymentIntent->payment_method;
				} else {
					$stripePaymentMethod = \StripeWPFS\PaymentMethod::retrieve( $paymentIntent->payment_method );
				}
			}
		}

		return $stripePaymentMethod;
	}

	/**
	 * @param \StripeWPFS\PaymentMethod $paymentMethod
	 *
	 * @return null|\StripeWPFS\Customer
	 */
	public function retrieveStripeCustomerByPaymentMethod( $paymentMethod ) {
		$stripeCustomer = null;
		if ( isset( $paymentMethod ) ) {
			if ( $paymentMethod instanceof \StripeWPFS\PaymentMethod ) {
				if ( isset( $paymentMethod->customer ) ) {
					if ( $paymentMethod->customer instanceof \StripeWPFS\Customer ) {
						$stripeCustomer = $paymentMethod->customer;
					} else {
						$stripeCustomer = \StripeWPFS\Customer::retrieve( $paymentMethod->customer );
					}
				}
			}
		}

		return $stripeCustomer;
	}

}