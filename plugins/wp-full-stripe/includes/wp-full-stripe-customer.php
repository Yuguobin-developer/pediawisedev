<?php

/**
 * Class MM_WPFS_Customer deals with customer front-end input i.e. payment forms submission
 */
class MM_WPFS_Customer {
    use MM_WPFS_DonationTools;

	const DEFAULT_CHECKOUT_LINE_ITEM_IMAGE = 'https://stripe.com/img/documentation/checkout/marketplace.png';

	/** @var $stripe MM_WPFS_Stripe */
	private $stripe = null;
	/** @var $db MM_WPFS_Database */
	private $db = null;
	/** @var $mailer MM_WPFS_Mailer */
	private $mailer = null;
	/** @var MM_WPFS_TransactionDataService */
	private $transactionDataService = null;
	/** @var MM_WPFS_CheckoutSubmissionService */
	private $checkoutSubmissionService = null;

	private $debugLog = false;

	public function __construct() {
		$this->setup();
		$this->hooks();
	}

	private function setup() {
		$this->stripe                    = new MM_WPFS_Stripe();
		$this->db                        = new MM_WPFS_Database();
		$this->mailer                    = new MM_WPFS_Mailer();
		$this->transactionDataService    = new MM_WPFS_TransactionDataService();
		$this->checkoutSubmissionService = new MM_WPFS_CheckoutSubmissionService();
	}

	private function hooks() {
		add_action( 'wp_ajax_wp_full_stripe_subscription_charge', array( $this, 'fullstripe_subscription_charge' ) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_subscription_charge', array(
			$this,
			'fullstripe_subscription_charge'
		) );
		add_action( 'wp_ajax_wp_full_stripe_check_coupon', array( $this, 'fullstripe_check_coupon' ) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_check_coupon', array( $this, 'fullstripe_check_coupon' ) );
		add_action( 'wp_ajax_wp_full_stripe_calculate_plan_amounts_and_setup_fees', array(
			$this,
			'calculate_plan_amounts_and_setup_fees'
		) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_calculate_plan_amounts_and_setup_fees', array(
			$this,
			'calculate_plan_amounts_and_setup_fees'
		) );
		add_action( 'wp_ajax_wp_full_stripe_inline_payment_charge', array(
			$this,
			'fullstripe_inline_payment_charge'
		) );
        add_action( 'wp_ajax_wp_full_stripe_inline_donation_charge', array(
            $this,
            'fullstripe_inline_donation_charge'
        ) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_inline_payment_charge', array(
			$this,
			'fullstripe_inline_payment_charge'
		) );
        add_action( 'wp_ajax_nopriv_wp_full_stripe_inline_donation_charge', array(
            $this,
            'fullstripe_inline_donation_charge'
        ) );
		add_action( 'wp_ajax_wp_full_stripe_inline_subscription_charge', array(
			$this,
			'fullstripe_inline_subscription_charge'
		) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_inline_subscription_charge', array(
			$this,
			'fullstripe_inline_subscription_charge'
		) );
		add_action( 'wp_ajax_wp_full_stripe_popup_payment_charge', array(
			$this,
			'fullstripe_popup_payment_charge'
		) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_popup_payment_charge', array(
			$this,
			'fullstripe_popup_payment_charge'
		) );
        add_action( 'wp_ajax_wp_full_stripe_popup_donation_charge', array(
            $this,
            'fullstripe_popup_donation_charge'
        ) );
        add_action( 'wp_ajax_nopriv_wp_full_stripe_popup_donation_charge', array(
            $this,
            'fullstripe_popup_donation_charge'
        ) );
		add_action( 'wp_ajax_wp_full_stripe_popup_subscription_charge', array(
			$this,
			'fullstripe_popup_subscription_charge'
		) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_popup_subscription_charge', array(
			$this,
			'fullstripe_popup_subscription_charge'
		) );
		add_action( 'wp_ajax_wp_full_stripe_handle_checkout_session', array(
			$this,
			'fullstripe_handle_checkout_session'
		) );
		add_action( 'wp_ajax_nopriv_wp_full_stripe_handle_checkout_session', array(
			$this,
			'fullstripe_handle_checkout_session'
		) );
	}

	/**
	 * @param $transactionResult MM_WPFS_TransactionResult
	 *
	 * @return array
	 */
	static function generate_return_value_from_transaction_result( $transactionResult ) {
		$returnValue = array(
			'success'      => $transactionResult->isSuccess(),
			'messageTitle' => $transactionResult->getMessageTitle(),
			'message'      => $transactionResult->getMessage(),
			'redirect'     => $transactionResult->isRedirect(),
			'redirectURL'  => $transactionResult->getRedirectURL()
		);

		return $returnValue;
	}

	function fullstripe_handle_checkout_session() {

		try {

			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'fullstripe_handle_checkout_session(): CALLED' );
			}

			$submitHash      = isset(
				$_GET[ MM_WPFS_CheckoutSubmissionService::STRIPE_CALLBACK_PARAM_WPFS_POPUP_FORM_SUBMIT_HASH ]
			) ? sanitize_text_field( $_GET[ MM_WPFS_CheckoutSubmissionService::STRIPE_CALLBACK_PARAM_WPFS_POPUP_FORM_SUBMIT_HASH ] ) : null;
			$submitStatus    = isset(
				$_GET[ MM_WPFS_CheckoutSubmissionService::STRIPE_CALLBACK_PARAM_WPFS_STATUS ]
			) ? sanitize_text_field( $_GET[ MM_WPFS_CheckoutSubmissionService::STRIPE_CALLBACK_PARAM_WPFS_STATUS ] ) : null;
			$popupFormSubmit = null;

			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( "fullstripe_handle_checkout_session(): submitHash=$submitHash" );
				MM_WPFS_Utils::log( "fullstripe_handle_checkout_session(): submitStatus=$submitStatus" );
			}

			if ( ! empty( $submitHash ) && ! empty( $submitStatus ) ) {

				$popupFormSubmit = $this->checkoutSubmissionService->retrieveSubmitEntry( $submitHash );

				if ( ! is_null( $popupFormSubmit ) && isset( $popupFormSubmit->checkoutSessionId ) ) {

					$checkoutSession = $this->checkoutSubmissionService->retrieveCheckoutSession( $popupFormSubmit->checkoutSessionId );

					if ( $this->debugLog ) {
						MM_WPFS_Utils::log( 'fullstripe_handle_checkout_session(): popupFormSubmit=' . print_r( $popupFormSubmit, true ) );
						MM_WPFS_Utils::log( 'fullstripe_handle_checkout_session(): checkoutSession=' . print_r( $checkoutSession, true ) );
					}

					if ( MM_WPFS_CheckoutSubmissionService::CHECKOUT_SESSION_STATUS_SUCCESS === $submitStatus ) {

						/**
						 * @var MM_WPFS_CheckoutChargeHandler
						 */
						$checkoutChargeHandler = null;
						$formModel             = null;
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
							if ( $this->debugLog ) {
								MM_WPFS_Utils::log( 'fullstripe_handle_checkout_session(): formModel=' . print_r( $formModel, true ) );
							}
							$chargeResult = $checkoutChargeHandler->handle( $formModel, $checkoutSession );
							if ( $chargeResult->isSuccess() ) {
								$this->checkoutSubmissionService->updateSubmitEntryWithSuccess( $popupFormSubmit, $chargeResult->getMessageTitle(), $chargeResult->getMessage() );
								$redirectURL = $popupFormSubmit->referrer;
								if ( $chargeResult->isRedirect() ) {
									$redirectURL = $chargeResult->getRedirectURL();
								}
								wp_redirect( $redirectURL );
								if ( $this->debugLog ) {
									MM_WPFS_Utils::log( 'fullstripe_handle_checkout_session(): Submit entry successfully processed, redirect to=' . $redirectURL );
								}
							} else {
								$this->checkoutSubmissionService->updateSubmitEntryWithFailed( $popupFormSubmit );
								wp_redirect( $popupFormSubmit->referrer );
								if ( $this->debugLog ) {
									MM_WPFS_Utils::log( 'fullstripe_handle_checkout_session(): Submit entry failed, redirect to=' . $popupFormSubmit->referrer );
								}
							}
						} else {
                            if ( $this->debugLog ) {
                                MM_WPFS_Utils::log( __CLASS__ . __FUNCTION__ . "(): Cannot find handler and form model for form type '" . $popupFormSubmit->formType . "'." );
                            }
                        }
					} else {
						// tnagy mark submission as failed
						$this->checkoutSubmissionService->updateSubmitEntryWithCancelled( $popupFormSubmit );
						wp_redirect( $popupFormSubmit->referrer );
						if ( $this->debugLog ) {
							MM_WPFS_Utils::log( 'fullstripe_handle_checkout_session(): Submit entry cancelled, redirect to=' . $popupFormSubmit->referrer );
						}
					}
				} else {
					// tnagy submit entry not found
					MM_WPFS_Utils::log( 'fullstripe_handle_checkout_session(): ERROR: Submit entry not found: submitHash=' . $submitHash . ', submitStatus=' . $submitStatus );
					status_header( 500 );
				}

			} else {
				// tnagy submit hash and/or submit status is empty
				MM_WPFS_Utils::log( 'fullstripe_handle_checkout_session(): ERROR: submitHash and/or submitStatus is empty: submitHash=' . $submitHash . ', submitStatus=' . $submitStatus );
				status_header( 500 );
			}

		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			if ( isset( $popupFormSubmit ) ) {
				$this->checkoutSubmissionService->updateSubmitEntryWithFailed( $popupFormSubmit, __( 'Internal Error', MM_WPFS::L10N_DOMAIN_PUBLIC ), MM_WPFS_Localization::translateLabel( $e->getMessage() ) );
				wp_redirect( $popupFormSubmit->referrer );
			} else {
				status_header( 500 );
			}
		}

		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'fullstripe_handle_checkout_session(): FINISHED' );
		}

		exit;
	}

	function fullstripe_inline_payment_charge() {

		try {

			$paymentFormModel = new MM_WPFS_Public_InlinePaymentFormModel();
			$bindingResult    = $paymentFormModel->bind();

			if ( $bindingResult->hasErrors() ) {
				$return = self::generate_return_value_from_bindings( $bindingResult );
			} else {
				if ( MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE === $paymentFormModel->getForm()->customAmount ) {
					$result = $this->processSetupIntent( $paymentFormModel );
				} else {
					$result = $this->processPaymentIntentCharge( $paymentFormModel );
				}
				$return = $this->generateReturnValueFromTransactionResult( $result );
			}

		} catch ( \StripeWPFS\Exception\CardException $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$messageTitle =
				/* translators: Banner title of error returned by Stripe */
				__( 'Stripe Error', MM_WPFS::L10N_DOMAIN_PUBLIC );
			$message      = $this->stripe->resolve_error_message_by_code( $e->getCode() );
			if ( is_null( $message ) ) {
				$message = MM_WPFS_Localization::translateLabel( $e->getMessage() );
			}
			$return = array(
				'success'          => false,
				'messageTitle'     => $messageTitle,
				'message'          => $message,
				'exceptionMessage' => $e->getMessage()
			);
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$return = array(
				'success'          => false,
				'messageTitle'     =>
				/* translators: Banner title of internal error */
					__( 'Internal Error', MM_WPFS::L10N_DOMAIN_PUBLIC ),
				'message'          => MM_WPFS_Localization::translateLabel( $e->getMessage() ),
				'exceptionMessage' => $e->getMessage()
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( apply_filters( 'fullstripe_inline_payment_charge_return_message', $return ) );
		exit;

	}

    function fullstripe_inline_donation_charge() {

        try {

            $donationFormModel = new MM_WPFS_Public_InlineDonationFormModel();
            $bindingResult    = $donationFormModel->bind();

            if ( $bindingResult->hasErrors() ) {
                $return = self::generate_return_value_from_bindings( $bindingResult );
            } else {
                $result = $this->processDonationPaymentIntentCharge( $donationFormModel );
                $return = $this->generateReturnValueFromTransactionResult( $result );
            }

        } catch ( \StripeWPFS\Exception\CardException $e ) {
            MM_WPFS_Utils::logException( $e, $this );
            $messageTitle =
                /* translators: Banner title of error returned by Stripe */
                __( 'Stripe Error', MM_WPFS::L10N_DOMAIN_PUBLIC );
            $message      = $this->stripe->resolve_error_message_by_code( $e->getCode() );
            if ( is_null( $message ) ) {
                $message = MM_WPFS_Localization::translateLabel( $e->getMessage() );
            }
            $return = array(
                'success'          => false,
                'messageTitle'     => $messageTitle,
                'message'          => $message,
                'exceptionMessage' => $e->getMessage()
            );
        } catch ( Exception $e ) {
            MM_WPFS_Utils::logException( $e, $this );
            $return = array(
                'success'          => false,
                'messageTitle'     =>
                /* translators: Banner title of internal error */
                    __( 'Internal Error', MM_WPFS::L10N_DOMAIN_PUBLIC ),
                'message'          => MM_WPFS_Localization::translateLabel( $e->getMessage() ),
                'exceptionMessage' => $e->getMessage()
            );
        }

        header( "Content-Type: application/json" );
        echo json_encode( apply_filters( 'fullstripe_inline_donation_charge_return_message', $return ) );
        exit;

    }

    /**
	 * @param $bindingResult MM_WPFS_BindingResult
	 *
	 * @return array
	 */
	static function generate_return_value_from_bindings( $bindingResult ) {
		return array(
			'success'       => false,
			'bindingResult' => array(
				'fieldErrors'  => array(
					'title'  =>
					/* translators: Banner title of a hidden field's validation error */
						__( 'Field validation error', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'errors' => $bindingResult->getFieldErrors()
				),
				'globalErrors' => array(
					'title'  =>
					/* translators: Banner title of a validation error which is not field specific */
						__( 'Form error', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'errors' => $bindingResult->getGlobalErrors()
				)
			)
		);
	}

	/**
	 * @param MM_WPFS_Public_PaymentFormModel $paymentFormModel
	 *
	 * @return MM_WPFS_ChargeResult
	 */
	private function processSetupIntent( $paymentFormModel ) {
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( __FUNCTION__ . '(): CALLED' );
		}

		$setupIntentResult = new MM_WPFS_SetupIntentResult();

		$sendPluginEmail = MM_WPFS_Utils::isSendingPluginEmail( $paymentFormModel );

		if ( empty( $paymentFormModel->getStripeSetupIntentId() ) ) {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( __FUNCTION__ . '(): Creating SetupIntent...' );
			}
			$setupIntent = $this->stripe->createSetupIntentWithPaymentMethod( $paymentFormModel->getStripePaymentMethodId() );
			$setupIntent->confirm();
		} else {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( __FUNCTION__ . '(): Retrieving SetupIntent...' );
			}

			$setupIntent = $this->stripe->retrieveSetupIntent( $paymentFormModel->getStripeSetupIntentId() );
		}

		$transactionData = null;
		if ( $setupIntent instanceof \StripeWPFS\SetupIntent ) {
			if (
				\StripeWPFS\SetupIntent::STATUS_REQUIRES_ACTION === $setupIntent->status
				&& 'use_stripe_sdk' === $setupIntent->next_action->type
			) {
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( __FUNCTION__ . '(): SetupIntent requires action...' );
				}
				$setupIntentResult->setSuccess( false );
				$setupIntentResult->setRequiresAction( true );
				$setupIntentResult->setSetupIntentClientSecret( $setupIntent->client_secret );
				$setupIntentResult->setMessageTitle(
				/* translators: Banner title of pending transaction requiring a second factor authentication (SCA/PSD2) */
					__( 'Action required', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
				$setupIntentResult->setMessage(
				/* translators: Banner message of a pending card saving transaction requiring a second factor authentication (SCA/PSD2) */
					__( 'Saving this card requires additional action before completion!', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
			} elseif ( \StripeWPFS\SetupIntent::STATUS_SUCCEEDED === $setupIntent->status ) {
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( __FUNCTION__ . '(): SetupIntent succeeded.' );
				}

				do_action( MM_WPFS::ACTION_NAME_BEFORE_CARD_CAPTURE, $paymentFormModel->getForm()->name );

				$billingName                    = is_null( $paymentFormModel->getBillingName() ) ? $paymentFormModel->getCardHolderName() : $paymentFormModel->getBillingName();
				$createOrRetrieveCustomerResult = $this->createOrRetrieveCustomer(
					$paymentFormModel->getStripePaymentMethodId(),
					$paymentFormModel->getStripePaymentIntentId(),
					$paymentFormModel->getCardHolderName(),
					$paymentFormModel->getCardHolderEmail(),
					$paymentFormModel->getCardHolderPhone(),
					$billingName,
					$paymentFormModel->getBillingAddress(),
					$paymentFormModel->getShippingName(),
					$paymentFormModel->getShippingAddress(),
					$paymentFormModel->getMetadata()
				);
				$paymentFormModel->setStripeCustomer( $createOrRetrieveCustomerResult->getCustomer() );
				$paymentFormModel->setStripePaymentMethod( $createOrRetrieveCustomerResult->getPaymentMethod() );

				$transactionData = MM_WPFS_TransactionDataService::createPaymentDataByPaymentMethod(
					$paymentFormModel->getFormName(),
					$paymentFormModel->getStripePaymentMethodId(),
					$paymentFormModel->getStripePaymentIntentId(),
					$paymentFormModel->getStripeCustomer()->id,
					$paymentFormModel->getCardHolderName(),
					$paymentFormModel->getCardHolderEmail(),
					$paymentFormModel->getCardHolderPhone(),
					$paymentFormModel->getForm()->currency,
					$paymentFormModel->getAmount(),
					$paymentFormModel->getProductName(),
					$billingName,
					$paymentFormModel->getBillingAddress(),
					$paymentFormModel->getShippingName(),
					$paymentFormModel->getShippingAddress(),
					$paymentFormModel->getCustomInputvalues()
				);

				$stripeCardSavedDescription  = MM_WPFS_Utils::prepareStripeCardSavedDescription( $paymentFormModel, $transactionData );
				$stripeCustomer              = $createOrRetrieveCustomerResult->getCustomer();
				$stripeCustomer->description = $stripeCardSavedDescription;
				$stripeCustomer->save();

				$formId   = $paymentFormModel->getForm()->paymentFormID;
				$formType = MM_WPFS::FORM_TYPE_PAYMENT;

				$this->db->fullstripe_insert_card_capture(
					$paymentFormModel->getStripeCustomer(),
					$paymentFormModel->getCardHolderName(),
					$paymentFormModel->getBillingName(),
					$paymentFormModel->getBillingAddress( false ),
					$paymentFormModel->getShippingName(),
					$paymentFormModel->getShippingAddress( false ),
					$formId,
					$formType,
					$paymentFormModel->getFormName()
				);
				$paymentFormModel->setTransactionId( $paymentFormModel->getStripeCustomer()->id );
				$transactionData->setTransactionId( $paymentFormModel->getTransactionId() );

				do_action( MM_WPFS::ACTION_NAME_AFTER_CARD_CAPTURE, $paymentFormModel->getStripeCustomer() );

				$setupIntentResult->setRequiresAction( false );
				$setupIntentResult->setSuccess( true );
				$setupIntentResult->setMessageTitle(
				/* translators: Banner title of successful transaction */
					__( 'Success', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
				$setupIntentResult->setMessage(
				/* translators: Banner message of saving card successfully */
					__( 'Saving Card Successful!', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
			} else {
				$setupIntentResult->setSuccess( false );
				$setupIntentResult->setMessageTitle(
				/* translators: Banner title of failed transaction */
					__( 'Failed', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
				$setupIntentResult->setMessage(
				// This is an internal error, no need to localize it
					sprintf( "Invalid SetupIntent status '%'.", $setupIntent->status ) );
			}
		}

		$this->handleRedirect( $paymentFormModel, $transactionData, $setupIntentResult );

		if ( $setupIntentResult->isSuccess() ) {
			if ( $sendPluginEmail && 1 == $paymentFormModel->getForm()->sendEmailReceipt ) {
				$this->mailer->send_card_captured_email_receipt(
					$paymentFormModel->getForm(),
					$paymentFormModel->getCardHolderName(),
					$paymentFormModel->getCardHolderEmail(),
					$paymentFormModel->getBillingName(),
					$paymentFormModel->getBillingAddress(),
					$paymentFormModel->getShippingName(),
					$paymentFormModel->getShippingAddress(),
					$paymentFormModel->getProductName(),
					$paymentFormModel->getCustomInputvalues(),
					$paymentFormModel->getFormName(),
					$paymentFormModel->getTransactionId()
				);
			}
		}

		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( __FUNCTION__ . '(): Returning paymentIntentResult=' . print_r( $setupIntentResult, true ) );
		}

		return $setupIntentResult;
	}

	/**
	 * This function creates or retrieves a Stripe Customer. As a first step it validates the given PaymentMethod's CVC
	 * check then tries to retrieve an existing Stripe Customer by the given email address.
	 * If no Stripe Customer has been found then it tries to retrieve a Customer stored by the PaymentMethod and if
	 * there is no Stripe Customer at all then creates one.
	 * If an existing Stripe Customer was found then it tries to attach the given PaymentMethod if the PaymentMethod's
	 * card fingerprint is not currently found in the list of PaymentMethods currently attached to this Customer to
	 * avoid the duplication of identical PaymentMethods.
	 *
	 * @param $paymentMethodId
	 * @param $paymentIntentId
	 * @param $cardHolderName
	 * @param $cardHolderEmail
	 * @param $cardHolderPhone
	 * @param $billingName
	 * @param $billingAddress
	 * @param $shippingName
	 * @param $shippingAddress
	 * @param $metadata
	 *
	 * @return MM_WPFS_CreateOrRetrieveCustomerResult
	 * @throws Exception
	 */
	private function createOrRetrieveCustomer( $paymentMethodId, $paymentIntentId, $cardHolderName, $cardHolderEmail, $cardHolderPhone, $billingName, $billingAddress, $shippingName, $shippingAddress, $metadata ) {
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( "createOrRetrieveCustomer(): CALLED, params: paymentMethodId=$paymentMethodId, paymentIntentId=$paymentIntentId, cardHolderEmail=$cardHolderEmail" );
		}
		$result        = new MM_WPFS_CreateOrRetrieveCustomerResult();
		$paymentMethod = $this->stripe->validatePaymentMethodCVCCheck( $paymentMethodId );
		$result->setPaymentMethod( $paymentMethod );
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'createOrRetrieveCustomer(): paymentMethod=' . print_r( $paymentMethod, true ) );
		}
		$stripeCustomer = MM_WPFS_Utils::find_existing_stripe_customer_anywhere_by_email( $this->db, $this->stripe, $cardHolderEmail );
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'createOrRetrieveCustomer(): stripeCustomer by email=' . print_r( $stripeCustomer, true ) );
		}
		if ( ! isset( $stripeCustomer ) && isset( $paymentMethod->customer ) ) {
			$stripeCustomer = $this->stripe->retrieve_customer( $paymentMethod->customer );
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'createOrRetrieveCustomer(): Stripe Customer by PaymentMethod=' . print_r( $stripeCustomer, true ) );
			}
		}
		if ( ! isset( $stripeCustomer ) ) {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'createOrRetrieveCustomer(): Creating Stripe Customer with PaymentMethod...' );
			}
			$customerName   = ! empty( $cardHolderName ) ? $cardHolderName : $billingName;
			$customerEmail  = $cardHolderEmail;
			$stripeCustomer = $this->stripe->createCustomerWithPaymentMethod( $paymentMethod->id, $customerName, $customerEmail, $metadata );
			$result->setCustomer( $stripeCustomer );
		} else {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'createOrRetrieveCustomer(): Attaching PaymentMethod to existing Stripe Customer...' );
			}
			// update existing customer to charge
			$attachedPaymentMethod = $this->stripe->attachPaymentMethodToCustomerIfMissing(
				$stripeCustomer,
				$paymentMethod,
				/* set to default */
				true
			);
			$result->setPaymentMethod( $attachedPaymentMethod );

			$stripeCustomer->metadata = $metadata;
			$stripeCustomer->save();
		}
		if ( ! is_null( $billingAddress ) ) {
			$this->stripe->update_customer_billing_address( $stripeCustomer, $billingName, $billingAddress );
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'createOrRetrieveCustomer(): Stripe customer\'s billing address updated with=' . print_r( $billingAddress, true ) );
			}
		}
		if ( ! is_null( $shippingAddress ) ) {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'createOrRetrieveCustomer(): Stripe Customer\'s shipping address will be updated with=' . print_r( $shippingAddress, true ) );
			}
			$this->stripe->update_customer_shipping_address( $stripeCustomer, $shippingName, $cardHolderPhone, $shippingAddress );
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'createOrRetrieveCustomer(): Stripe Customer\'s shipping address updated with=' . print_r( $shippingAddress, true ) );
			}
		}

		$stripeCustomer = $this->stripe->retrieve_customer( $stripeCustomer->id );
		$result->setCustomer( $stripeCustomer );

		return $result;
	}

	/**
	 * @param MM_WPFS_Public_FormModel $formModel
	 * @param MM_WPFS_TransactionData $transactionData
	 * @param MM_WPFS_TransactionResult $transactionResult
	 */
	private function handleRedirect( $formModel, $transactionData, $transactionResult ) {
		if ( $transactionResult->isSuccess() ) {
			if ( 1 == $formModel->getForm()->redirectOnSuccess ) {
				if ( 1 == $formModel->getForm()->redirectToPageOrPost ) {
					if ( 0 != $formModel->getForm()->redirectPostID ) {
						$pageOrPostUrl = get_page_link( $formModel->getForm()->redirectPostID );
						if ( 1 == $formModel->getForm()->showDetailedSuccessPage ) {
							$transactionDataKey = $this->transactionDataService->store( $transactionData );
							$pageOrPostUrl      = add_query_arg(
								array(
									MM_WPFS_TransactionDataService::REQUEST_PARAM_NAME_WPFS_TRANSACTION_DATA_KEY => $transactionDataKey
								),
								$pageOrPostUrl
							);
						}
						$transactionResult->setRedirect( true );
						$transactionResult->setRedirectURL( $pageOrPostUrl );
					} else {
						MM_WPFS_Utils::log( "handleRedirect(): Inconsistent form data: formName={$formModel->getFormName()}, doRedirect={$formModel->getForm()->redirectOnSuccess}, redirectPostID={$formModel->getForm()->redirectPostID}" );
					}
				} else {
					$transactionResult->setRedirect( true );
					$transactionResult->setRedirectURL( $formModel->getForm()->redirectUrl );
				}
			}
		}
	}

    /**
     * @param $paymentIntentResult MM_WPFS_DonationPaymentIntentResult
     * @param $paymentIntent \StripeWPFS\PaymentIntent
     * @param $title string
     * @param $message string
     *
     * @return MM_WPFS_DonationPaymentIntentResult
     */
    protected function createPaymentIntentResultActionRequired( & $paymentIntentResult, $paymentIntent, $title, $message ) {
        $paymentIntentResult->setSuccess( false );
        $paymentIntentResult->setRequiresAction( true );
        $paymentIntentResult->setPaymentIntentClientSecret( $paymentIntent->client_secret );
        $paymentIntentResult->setMessageTitle( $title );
        $paymentIntentResult->setMessage( $message );

        return $paymentIntentResult;
    }

    /**
     * @param $paymentIntentResult MM_WPFS_DonationPaymentIntentResult
     * @param $paymentIntent \StripeWPFS\PaymentIntent
     * @param $title string
     * @param $message string
     *
     * @return MM_WPFS_DonationPaymentIntentResult
     */
    protected function createPaymentIntentResultSuccess( & $paymentIntentResult, $title, $message ) {
        $paymentIntentResult->setRequiresAction( false );
        $paymentIntentResult->setSuccess( true );
        $paymentIntentResult->setMessageTitle( $title );
        $paymentIntentResult->setMessage( $message );

        return $paymentIntentResult;
    }

    /**
     * @param $paymentIntentResult MM_WPFS_DonationPaymentIntentResult
     * @param $paymentIntent \StripeWPFS\PaymentIntent
     * @param $title string
     * @param $message string
     *
     * @return MM_WPFS_DonationPaymentIntentResult
     */
    protected function createPaymentIntentResultFailed( & $paymentIntentResult, $title, $message ) {
        $paymentIntentResult->setSuccess( false );
        $paymentIntentResult->setMessageTitle( $title );
        $paymentIntentResult->setMessage( $message );

        return $paymentIntentResult;
    }

    /**
     * @param $paymentIntent \StripeWPFS\PaymentIntent
     * @param $formName
     */
    protected function addFormNameToPaymentIntent( & $paymentIntent, $formName ) {
        $paymentIntent->wpfs_form = $formName;
    }

    /**
     * @param $formModel MM_WPFS_Public_FormModel
     *
     * @return boolean
     */
    protected function modelNeedsPaymentIntent( $formModel ) {
        return empty( $formModel->getStripePaymentIntentId() );
    }

    /**
     * @param $donationFormModel MM_WPFS_Public_DonationFormModel
     * @param $transactionData MM_WPFS_DonationTransactionData
     *
     * @return \StripeWPFS\PaymentIntent
     */
    protected function createPaymentIntentForDonation( $donationFormModel, $transactionData ) {
        $sendPluginEmail 	 = MM_WPFS_Utils::isSendingPluginEmail( $donationFormModel );
        $donationDescription = MM_WPFS_Utils::prepareStripeDonationDescription( $donationFormModel, $transactionData );

        $paymentIntent = $this->stripe->createPaymentIntent(
            $donationFormModel->getStripePaymentMethod()->id,
            $donationFormModel->getStripeCustomer()->id,
            $donationFormModel->getForm()->currency,
            $donationFormModel->getAmount(),
            true,
            $donationDescription,
            $donationFormModel->getMetadata(),
            ( $sendPluginEmail == false && $donationFormModel->getForm()->sendEmailReceipt == true ? $donationFormModel->getCardHolderEmail() : null )
        );

        return $paymentIntent;
    }

    /**
     * @param $donationFormModel MM_WPFS_Public_DonationFormModel
     * @param $transactionData MM_WPFS_DonationTransactionData
     *
     * @return \StripeWPFS\PaymentIntent
     * @throws Exception
     */
    protected function createOrRetrievePaymentIntent( $donationFormModel, $transactionData ) {
        $paymentIntent = null;

        if ( $this->modelNeedsPaymentIntent( $donationFormModel )) {
            $paymentIntent = $this->createPaymentIntentForDonation( $donationFormModel, $transactionData );

            $donationFormModel->setTransactionId( $paymentIntent->id );
            $transactionData->setTransactionId( $donationFormModel->getTransactionId() );
        } else {
            $paymentIntent = $this->stripe->retrievePaymentIntent( $donationFormModel->getStripePaymentIntentId() );

            if ( $paymentIntent instanceof \StripeWPFS\PaymentIntent ) {
                $paymentIntent->confirm();

                $donationFormModel->setTransactionId( $paymentIntent->id );
                $transactionData->setTransactionId( $donationFormModel->getTransactionId() );
            }
        }

        return $paymentIntent;
    }

    /**
     * @param $paymentIntent \StripeWPFS\PaymentIntent
     *
     * @return boolean
     */
    protected function paymentIntentRequiresAction( $paymentIntent ) {
        return \StripeWPFS\PaymentIntent::STATUS_REQUIRES_ACTION === $paymentIntent->status
            && 'use_stripe_sdk' === $paymentIntent->next_action->type;
    }

    /**
     * @param $paymentIntent \StripeWPFS\PaymentIntent
     *
     * @return boolean
     */
    protected function paymentIntentSucceeded( $paymentIntent ) {
        return \StripeWPFS\PaymentIntent::STATUS_SUCCEEDED === $paymentIntent->status
            || \StripeWPFS\PaymentIntent::STATUS_REQUIRES_CAPTURE === $paymentIntent->status;
    }

    /**
     * @param $formModel MM_WPFS_Public_FormModel
     *
     * @return string
     */
    protected function findBillingName( $formModel ) {
        return is_null( $formModel->getBillingName() ) ? $formModel->getCardHolderName() : $formModel->getBillingName();
    }

    /**
     * @param $donationFormModel MM_WPFS_Public_DonationFormModel
     *
     * @return MM_WPFS_CreateOrRetrieveCustomerResult
     * @throws Exception
     */
    protected function createOrRetrieveCustomerForDonation( $donationFormModel ) {
        $billingName  = $this->findBillingName( $donationFormModel );

        $createOrRetrieveResult = $this->createOrRetrieveCustomer(
            $donationFormModel->getStripePaymentMethodId(),
            $donationFormModel->getStripePaymentIntentId(),
            $donationFormModel->getCardHolderName(),
            $donationFormModel->getCardHolderEmail(),
            $donationFormModel->getCardHolderPhone(),
            $billingName,
            $donationFormModel->getBillingAddress(),
            $donationFormModel->getShippingName(),
            $donationFormModel->getShippingAddress(),
            null
        );

        return $createOrRetrieveResult;
    }

    /**
     * @param $donationFormModel MM_WPFS_Public_DonationFormModel
     */
    protected function fireBeforeInlineDonationAction ( $donationFormModel ) {
        do_action( MM_WPFS::ACTION_NAME_BEFORE_DONATION_CHARGE, $donationFormModel->getAmount() );
    }

    /**
     * @param $donationFormModel MM_WPFS_Public_DonationFormModel
     * @param $paymentIntent \StripeWPFS\PaymentIntent
     */
    protected function fireAfterInlineDonationAction ( $donationFormModel, $paymentIntent ) {
        do_action( MM_WPFS::ACTION_NAME_AFTER_DONATION_CHARGE, $paymentIntent );
    }

    /**
     * @param $donationFormModel MM_WPFS_Public_DonationFormModel
     *
     * @return MM_WPFS_ChargeResult
     * @throws Exception
     */
    private function processDonationPaymentIntentCharge( $donationFormModel ) {
        $paymentIntentResult = new MM_WPFS_DonationPaymentIntentResult();
        $paymentIntentResult->setNonce( $donationFormModel->getNonce() );

        $createOrRetrieveResult = $this->createOrRetrieveCustomerForDonation( $donationFormModel );
        $donationFormModel->setStripeCustomer( $createOrRetrieveResult->getCustomer() );
        $donationFormModel->setStripePaymentMethod( $createOrRetrieveResult->getPaymentMethod() );

        $transactionData = MM_WPFS_TransactionDataService::createDonationDataByFormModel( $donationFormModel );

        $this->fireBeforeInlineDonationAction( $donationFormModel );

        $paymentIntent = $this->createOrRetrievePaymentIntent( $donationFormModel, $transactionData );

        if ( $paymentIntent instanceof \StripeWPFS\PaymentIntent ) {
            if ( $this->paymentIntentRequiresAction( $paymentIntent )) {

                $this->createPaymentIntentResultActionRequired( $paymentIntentResult, $paymentIntent,
                    /* translators: Banner title of pending transaction requiring a second factor authentication (SCA/PSD2) */
                    __( 'Action required', MM_WPFS::L10N_DOMAIN_PUBLIC ),
                    /* translators: Banner message of a one-time payment requiring a second factor authentication (SCA/PSD2) */
                    __( 'The donation needs additional action before completion!', MM_WPFS::L10N_DOMAIN_PUBLIC ));

            } else if ( $this->paymentIntentSucceeded( $paymentIntent )) {

                $this->addFormNameToPaymentIntent( $paymentIntent, $donationFormModel->getFormName() );

                $subscription = null;
                if ( $this->isRecurringDonation( $donationFormModel )) {
                    $subscription = $this->createSubscriptionForDonation( $donationFormModel );
                }
                $this->db->insertInlineDonation( $donationFormModel, $paymentIntent, $subscription );

                $this->fireAfterInlineDonationAction( $donationFormModel, $paymentIntent );

                $this->createPaymentIntentResultSuccess( $paymentIntentResult,
                    /* translators: Banner title of successful transaction */
                    __( 'Success', MM_WPFS::L10N_DOMAIN_PUBLIC ),
                    /* translators: Banner message of successful payment */
                    __( 'Donation Successful!', MM_WPFS::L10N_DOMAIN_PUBLIC ));

            } else {

                $this->createPaymentIntentResultFailed( $paymentIntentResult,
                    /* translators: Banner title of failed transaction */
                    __( 'Failed', MM_WPFS::L10N_DOMAIN_PUBLIC ),
                    // This is an internal error, no need to localize it
                    sprintf( "Invalid PaymentIntent status '%s'.", $paymentIntent->status ));
            }
        } else {
            $this->createPaymentIntentResultFailed( $paymentIntentResult,
                /* translators: Banner title of failed transaction */
                __( 'Failed', MM_WPFS::L10N_DOMAIN_PUBLIC ),
                // This is an internal error, no need to localize it
                "PaymentIntent was neither created nor retrieved." );
        }

        $this->handleRedirect( $donationFormModel, $transactionData, $paymentIntentResult );

        if ( $paymentIntentResult->isSuccess() ) {
            if ( MM_WPFS_Utils::isSendingPluginEmail( $donationFormModel )) {
                $this->sendDonationEmailReceipt( $donationFormModel, $transactionData );
            }
        }

        return $paymentIntentResult;
    }

    /**
	 * @param MM_WPFS_Public_PaymentFormModel $paymentFormModel
	 *
	 * @return MM_WPFS_ChargeResult
     * @throws Exception
	 */
	private function processPaymentIntentCharge( $paymentFormModel ) {
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'processPaymentIntentCharge(): ' . 'CALLED' );
		}

		$paymentIntentResult = new MM_WPFS_PaymentIntentResult();
		$paymentIntentResult->setNonce( $paymentFormModel->getNonce() );

		$sendPluginEmail = MM_WPFS_Utils::isSendingPluginEmail( $paymentFormModel );

		$billingName            = is_null( $paymentFormModel->getBillingName() ) ? $paymentFormModel->getCardHolderName() : $paymentFormModel->getBillingName();
		$createOrRetrieveResult = $this->createOrRetrieveCustomer(
			$paymentFormModel->getStripePaymentMethodId(),
			$paymentFormModel->getStripePaymentIntentId(),
			$paymentFormModel->getCardHolderName(),
			$paymentFormModel->getCardHolderEmail(),
			$paymentFormModel->getCardHolderPhone(),
			$billingName,
			$paymentFormModel->getBillingAddress(),
			$paymentFormModel->getShippingName(),
			$paymentFormModel->getShippingAddress(),
			null
		);
		$paymentFormModel->setStripeCustomer( $createOrRetrieveResult->getCustomer() );
		$paymentFormModel->setStripePaymentMethod( $createOrRetrieveResult->getPaymentMethod() );

		$transactionData = MM_WPFS_TransactionDataService::createPaymentDataByPaymentMethod(
			$paymentFormModel->getFormName(),
			$paymentFormModel->getStripePaymentMethodId(),
			$paymentFormModel->getStripePaymentIntentId(),
			$paymentFormModel->getStripeCustomer()->id,
			$paymentFormModel->getCardHolderName(),
			$paymentFormModel->getCardHolderEmail(),
			$paymentFormModel->getCardHolderPhone(),
			$paymentFormModel->getForm()->currency,
			$paymentFormModel->getAmount(),
			$paymentFormModel->getProductName(),
			$billingName,
			$paymentFormModel->getBillingAddress(),
			$paymentFormModel->getShippingName(),
			$paymentFormModel->getShippingAddress(),
			$paymentFormModel->getCustomInputvalues()
		);

		$stripeChargeDescription = MM_WPFS_Utils::prepareStripeChargeDescription( $paymentFormModel, $transactionData );

		$formId   = $paymentFormModel->getForm()->paymentFormID;
		$formType = MM_WPFS::FORM_TYPE_PAYMENT;

		do_action( MM_WPFS::ACTION_NAME_BEFORE_PAYMENT_CHARGE, $paymentFormModel->getAmount() );
		$captureAmount = MM_WPFS_Utils::prepareCaptureIntentByFormModel( $paymentFormModel );
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'processPaymentIntentCharge(): paymentMethodId=' . $paymentFormModel->getStripePaymentMethodId() );
			MM_WPFS_Utils::log( 'processPaymentIntentCharge(): paymentIntentId=' . $paymentFormModel->getStripePaymentIntentId() );
		}
		$paymentIntent = null;
		if ( empty( $paymentFormModel->getStripePaymentIntentId() ) ) {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'processPaymentIntentCharge(): Creating PaymentIntent...' );
			}
			$paymentIntent = $this->stripe->createPaymentIntent(
				$paymentFormModel->getStripePaymentMethod()->id,
				$paymentFormModel->getStripeCustomer()->id,
				$paymentFormModel->getForm()->currency,
				$paymentFormModel->getAmount(),
				$captureAmount,
				$stripeChargeDescription,
				$paymentFormModel->getMetadata(),
				( $sendPluginEmail == false && $paymentFormModel->getForm()->sendEmailReceipt == true ? $paymentFormModel->getCardHolderEmail() : null )
			);
			$paymentFormModel->setTransactionId( $paymentIntent->id );
			$transactionData->setTransactionId( $paymentFormModel->getTransactionId() );
		} else {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'processPaymentIntentCharge(): Retrieving PaymentIntent...' );
			}
			$paymentIntent = $this->stripe->retrievePaymentIntent( $paymentFormModel->getStripePaymentIntentId() );
			if ( $paymentIntent instanceof \StripeWPFS\PaymentIntent ) {
				$paymentIntent->confirm();
				$paymentFormModel->setTransactionId( $paymentIntent->id );
				$transactionData->setTransactionId( $paymentFormModel->getTransactionId() );
			}
		}
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'processPaymentIntentCharge(): paymentIntent=' . print_r( $paymentIntent, true ) );
		}

		if ( $paymentIntent instanceof \StripeWPFS\PaymentIntent ) {
			if (
				\StripeWPFS\PaymentIntent::STATUS_REQUIRES_ACTION === $paymentIntent->status
				&& 'use_stripe_sdk' === $paymentIntent->next_action->type
			) {
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( 'processPaymentIntentCharge(): PaymentIntent requires action...' );
				}
				$paymentIntentResult->setSuccess( false );
				$paymentIntentResult->setRequiresAction( true );
				$paymentIntentResult->setPaymentIntentClientSecret( $paymentIntent->client_secret );
				$paymentIntentResult->setMessageTitle(
				/* translators: Banner title of pending transaction requiring a second factor authentication (SCA/PSD2) */
					__( 'Action required', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
				$paymentIntentResult->setMessage(
				/* translators: Banner message of a one-time payment requiring a second factor authentication (SCA/PSD2) */
					__( 'The payment needs additional action before completion!', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
			} elseif (
				\StripeWPFS\PaymentIntent::STATUS_SUCCEEDED === $paymentIntent->status
				|| \StripeWPFS\PaymentIntent::STATUS_REQUIRES_CAPTURE === $paymentIntent->status
			) {
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( 'processPaymentIntentCharge(): PaymentIntent succeeded.' );
				}

				$paymentIntent->wpfs_form = $paymentFormModel->getFormName();
				$this->db->insertPayment(
					$paymentIntent,
					$paymentFormModel->getBillingName(),
					$paymentFormModel->getBillingAddress( false ),
					$paymentFormModel->getShippingName(),
					$paymentFormModel->getShippingAddress( false ),
					$paymentFormModel->getStripeCustomer()->id,
					$paymentFormModel->getCardHolderName(),
					$paymentFormModel->getCardHolderEmail(),
					$formId,
					$formType,
					$paymentFormModel->getFormName()
				);
				/*
				 * todo: This code snippet doesn't make sense because a PaymentIntent doesn't have a "billing_details" property.
				 * The Charge object does.
				 * 
								if ( isset( $paymentIntent->billing_details ) && isset( $paymentIntent->billing_details->name ) && ! empty( $paymentIntent->billing_details->name ) ) {
									$paymentFormModel->setBillingName( $paymentIntent->billing_details->name );
								} else {
									$paymentFormModel->setBillingName( $paymentFormModel->getCardHolderName() );
								}
				*/
				do_action( MM_WPFS::ACTION_NAME_AFTER_PAYMENT_CHARGE, $paymentIntent );

				$paymentIntentResult->setRequiresAction( false );
				$paymentIntentResult->setSuccess( true );
				$paymentIntentResult->setMessageTitle(
				/* translators: Banner title of successful transaction */
					__( 'Success', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
				$paymentIntentResult->setMessage(
				/* translators: Banner message of successful payment */
					__( 'Payment Successful!', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
			} else {
				$paymentIntentResult->setSuccess( false );
				$paymentIntentResult->setMessageTitle(
				/* translators: Banner title of failed transaction */
					__( 'Failed', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
				$paymentIntentResult->setMessage(
				// This is an internal error, no need to localize it
					sprintf( "Invalid PaymentIntent status '%s'.", $paymentIntent->status ) );
			}
		}

		$this->handleRedirect( $paymentFormModel, $transactionData, $paymentIntentResult );

		if ( $paymentIntentResult->isSuccess() ) {
			if ( $sendPluginEmail && 1 == $paymentFormModel->getForm()->sendEmailReceipt ) {
				$this->mailer->send_payment_email_receipt(
					$paymentFormModel->getForm(),
					$paymentFormModel->getCardHolderName(),
					$paymentFormModel->getCardHolderEmail(),
					$paymentFormModel->getForm()->currency,
					$paymentFormModel->getAmount(),
					$paymentFormModel->getBillingName(),
					$paymentFormModel->getBillingAddress(),
					$paymentFormModel->getShippingName(),
					$paymentFormModel->getShippingAddress(),
					$paymentFormModel->getProductName(),
					$paymentFormModel->getCustomInputvalues(),
					$paymentFormModel->getFormName(),
					$paymentFormModel->getTransactionId()
				);
			}
		}

		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'processPaymentIntentCharge(): Returning paymentIntentResult=' . print_r( $paymentIntentResult, true ) );
		}

		return $paymentIntentResult;
	}

	/**
	 * @param MM_WPFS_TransactionResult $transactionResult
	 *
	 * @return array
	 */
	private function generateReturnValueFromTransactionResult( $transactionResult ) {
		$returnValue = array(
			'success'                   => $transactionResult->isSuccess(),
			'messageTitle'              => $transactionResult->getMessageTitle(),
			'message'                   => $transactionResult->getMessage(),
			'redirect'                  => $transactionResult->isRedirect(),
			'redirectURL'               => $transactionResult->getRedirectURL(),
			'requiresAction'            => $transactionResult->isRequiresAction(),
			'paymentIntentClientSecret' => $transactionResult->getPaymentIntentClientSecret(),
			'setupIntentClientSecret'   => $transactionResult->getSetupIntentClientSecret(),
			'formType'                  => $transactionResult->getFormType(),
			'nonce'                     => $transactionResult->getNonce()
		);

		return $returnValue;
	}

	function fullstripe_inline_subscription_charge() {

		try {

			$subscriptionFormModel = new MM_WPFS_Public_InlineSubscriptionFormModel();
			$bindingResult         = $subscriptionFormModel->bind();

			if ( $bindingResult->hasErrors() ) {
				$return = self::generate_return_value_from_bindings( $bindingResult );
			} else {
				$subscriptionResult = $this->processSubscription( $subscriptionFormModel );
				$return             = self::generateReturnValueFromTransactionResult( $subscriptionResult );
			}

		} catch ( \StripeWPFS\Exception\CardException $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$messageTitle =
				/* translators: Banner title of error returned by Stripe */
				__( 'Stripe Error', MM_WPFS::L10N_DOMAIN_PUBLIC );
			$message      = $this->stripe->resolve_error_message_by_code( $e->getCode() );
			if ( is_null( $message ) ) {
				$message = MM_WPFS_Localization::translateLabel( $e->getMessage() );
			}
			$return = array(
				'success'          => false,
				'messageTitle'     => $messageTitle,
				'message'          => $message,
				'exceptionMessage' => $e->getMessage()
			);
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$return = array(
				'success'          => false,
				'messageTitle'     =>
				/* Banner title of internal error */
					__( 'Internal Error', MM_WPFS::L10N_DOMAIN_PUBLIC ),
				'message'          => MM_WPFS_Localization::translateLabel( $e->getMessage() ),
				'exceptionMessage' => $e->getMessage()
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( apply_filters( 'fullstripe_inline_subscription_charge_return_message', $return ) );
		exit;

	}

	/**
	 * @param MM_WPFS_Public_SubscriptionFormModel $subscriptionFormModel
	 *
	 * @return MM_WPFS_SubscriptionResult
	 */
	private function processSubscription( $subscriptionFormModel ) {

		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'processSubscription(): CALLED, subscriptionFormModel=' . print_r( $subscriptionFormModel, true ) );
		}

		$subscriptionResult = new MM_WPFS_SubscriptionResult();
		$subscriptionResult->setNonce( $subscriptionFormModel->getNonce() );

		$vatPercent              = $this->get_vat_percent(
			$subscriptionFormModel->getForm()->subscriptionFormID,
			MM_WPFS::FORM_TYPE_INLINE_SUBSCRIPTION,
			$subscriptionFormModel->getStripePlan(),
			$subscriptionFormModel->getBillingAddress(),
			$subscriptionFormModel->getDecodedCustomInputLabels(),
			$subscriptionFormModel->getCustomInputvalues()
		);
		$subscriptionDescription = sprintf(
		/* translators: Default payment description for initial subscription payments.
		 * p1: Cardholder's name
		 */
			__( 'Subscriber: %s', MM_WPFS::L10N_DOMAIN_PUBLIC ), $subscriptionFormModel->getCardHolderName() );
		$transactionData         = MM_WPFS_TransactionDataService::createSubscriptionDataByPaymentMethod(
			$subscriptionFormModel->getFormName(),
			$subscriptionFormModel->getStripePaymentMethodId(),
			null,
			$subscriptionFormModel->getCardHolderName(),
			$subscriptionFormModel->getCardHolderEmail(),
			$subscriptionFormModel->getCardHolderPhone(),
			$subscriptionFormModel->getStripePlan()->id,
			$subscriptionFormModel->getStripePlan()->product->name,
			$subscriptionFormModel->getStripePlan()->currency,
			$subscriptionFormModel->getStripePlanAmount(),
			$subscriptionFormModel->getStripePlanSetupFee(),
			$subscriptionFormModel->getStripePlanQuantity(),
			$subscriptionFormModel->getProductName(),
			$subscriptionFormModel->getBillingName(),
			1 == $subscriptionFormModel->getForm()->showAddress ? $subscriptionFormModel->getBillingAddress() : null,
			$subscriptionFormModel->getShippingName(),
			1 == $subscriptionFormModel->getForm()->showAddress ? $subscriptionFormModel->getShippingAddress() : null,
			$subscriptionFormModel->getCustomInputvalues(),
			$vatPercent,
			$subscriptionDescription,
			$subscriptionFormModel->getCouponCode(),
			$subscriptionFormModel->getMetadata(),
			$subscriptionFormModel->getForm()->anchorBillingCycle ? $subscriptionFormModel->getForm()->billingCycleAnchorDay : 0,
			$subscriptionFormModel->getForm()->prorateUntilAnchorDay == '1' ? 1 : 0
		);

		$stripeSubscription  = null;
		$stripePaymentIntent = null;
		$stripeSetupIntent   = null;
		if (
			empty( $subscriptionFormModel->getStripePaymentIntentId() )
			&& empty( $subscriptionFormModel->getStripeSetupIntentId() )
		) {

			do_action(
				MM_WPFS::ACTION_NAME_BEFORE_SUBSCRIPTION_CHARGE,
				array(
					'plan'     => $subscriptionFormModel->getStripePlan()->id,
					'quantity' => $subscriptionFormModel->getStripePlanQuantity()
				)
			);

			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'processSubscription(): Creating Subscription...' );
			}
			$stripeCustomer = MM_WPFS_Utils::find_existing_stripe_customer_by_email( $this->db, $this->stripe, $subscriptionFormModel->getCardHolderEmail() );

			if ( $stripeCustomer && $stripeCustomer['stripeCustomerID'] ) {
				$stripeCustomer = $this->stripe->retrieve_customer( $stripeCustomer['stripeCustomerID'] );
				$transactionData->setStripeCustomerId( $stripeCustomer->id );
				/** @noinspection PhpUnusedLocalVariableInspection */
				$stripeSubscription = $this->stripe->alternativeSubscribeExisting( $transactionData );
				$stripeCustomer     = $this->stripe->retrieve_customer( $stripeSubscription->customer );
				$subscriptionFormModel->setStripeCustomer( $stripeCustomer );
				$subscriptionFormModel->setTransactionId( $stripeSubscription->id );
				$transactionData->setTransactionId( $stripeSubscription->id );
				$transactionData->setInvoiceUrl( $stripeSubscription->latest_invoice->invoice_pdf );
			} else {
				$stripeSubscription = $this->stripe->alternativeSubscribe( $transactionData );
				$stripeCustomer     = $this->stripe->retrieve_customer( $stripeSubscription->customer );
				$subscriptionFormModel->setStripeCustomer( $stripeCustomer );
				$subscriptionFormModel->setTransactionId( $stripeSubscription->id );
				$transactionData->setTransactionId( $stripeSubscription->id );
				if ( isset( $stripeSubscription->latest_invoice ) ) {
					$transactionData->setInvoiceUrl( $stripeSubscription->latest_invoice->invoice_pdf );
				}
				$transactionData->setStripeCustomerId( $stripeCustomer->id );
			}

			// tnagy retrieve PaymentIntent
			if ( isset( $stripeSubscription ) ) {
				if ( isset( $stripeSubscription->latest_invoice ) && isset( $stripeSubscription->latest_invoice ) ) {
					if ( $stripeSubscription->latest_invoice instanceof \StripeWPFS\Invoice ) {
						$stripePaymentIntent = $stripeSubscription->latest_invoice->payment_intent;
						// tnagy update transaction id
						$subscriptionFormModel->setTransactionId( $stripeSubscription->id );
						$transactionData->setTransactionId( $subscriptionFormModel->getTransactionId() );
					} else {
						// todo tnagy retrieve if not expanded
					}
				}
				if ( isset( $stripeSubscription->pending_setup_intent ) ) {
					$stripeSetupIntent = $stripeSubscription->pending_setup_intent;
					// tnagy update transaction id
					$subscriptionFormModel->setTransactionId( $stripeSubscription->id );
					$transactionData->setTransactionId( $subscriptionFormModel->getTransactionId() );
				}
			}

			// tnagy insert subscriber
			$this->db->insertSubscriber(
				$stripeCustomer,
				$stripeSubscription,
				$stripePaymentIntent,
				$stripeSetupIntent,
				$subscriptionFormModel->getCardHolderName(),
				$subscriptionFormModel->getBillingName(),
				$subscriptionFormModel->getBillingAddress( false ),
				$subscriptionFormModel->getShippingName(),
				$subscriptionFormModel->getShippingAddress( false ),
				$subscriptionFormModel->getForm()->subscriptionFormID,
				$subscriptionFormModel->getForm()->name,
				$vatPercent
			);

		} else {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'processSubscription(): Retrieving Subscription...' );
			}
			if ( ! empty( $subscriptionFormModel->getStripePaymentIntentId() ) ) {
				$stripePaymentIntent = $this->stripe->retrievePaymentIntent( $subscriptionFormModel->getStripePaymentIntentId() );
				if ( $stripePaymentIntent instanceof \StripeWPFS\PaymentIntent ) {
					$stripeCustomer = $this->stripe->retrieve_customer( $stripePaymentIntent->customer );
					$subscriptionFormModel->setStripeCustomer( $stripeCustomer );
					// tnagy update transaction id
					$wpfsSubscriber = $this->db->find_subscriber_by_payment_intent_id( $stripePaymentIntent->id );
					if ( isset( $wpfsSubscriber ) && isset( $wpfsSubscriber->stripeSubscriptionID ) ) {
						$subscriptionFormModel->setTransactionId( $wpfsSubscriber->stripeSubscriptionID );
						$transactionData->setTransactionId( $subscriptionFormModel->getTransactionId() );
					}
				}
			}
			if ( ! empty( $subscriptionFormModel->getStripeSetupIntentId() ) ) {
				$stripeSetupIntent = $this->stripe->retrieveSetupIntent( $subscriptionFormModel->getStripeSetupIntentId() );
				if ( $stripeSetupIntent instanceof \StripeWPFS\SetupIntent ) {
					$stripeCustomer = $this->stripe->retrieve_customer( $stripeSetupIntent->customer );
					$subscriptionFormModel->setStripeCustomer( $stripeCustomer );
					// tnagy update transaction id
					$wpfsSubscriber = $this->db->find_subscriber_by_setup_intent_id( $stripeSetupIntent->id );
					if ( isset( $wpfsSubscriber ) && isset( $wpfsSubscriber->stripeSubscriptionID ) ) {
						$subscriptionFormModel->setTransactionId( $wpfsSubscriber->stripeSubscriptionID );
						$transactionData->setTransactionId( $subscriptionFormModel->getTransactionId() );
					}
				}
			}
		}
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'processSubscription(): paymentIntent=' . print_r( $stripePaymentIntent, true ) );
			MM_WPFS_Utils::log( 'processSubscription(): setupIntent=' . print_r( $stripeSetupIntent, true ) );
		}
		$this->handleIntent( $subscriptionResult, $stripeSubscription, $stripePaymentIntent, $stripeSetupIntent );
		$this->handleRedirect( $subscriptionFormModel, $transactionData, $subscriptionResult );
		if ( $subscriptionResult->isSuccess() ) {
			// tnagy prepare macros
			$macros      = MM_WPFS_Utils::get_subscription_macros();
			$macroValues = MM_WPFS_Utils::getSubscriptionMacroValues( $subscriptionFormModel->getForm(), $transactionData, MM_WPFS_Utils::ESCAPE_TYPE_NONE );
			if ( ! is_null( $transactionData->getCustomInputValues() ) && is_array( $transactionData->getCustomInputValues() ) ) {
				$customFieldMacros      = MM_WPFS_Utils::get_custom_field_macros();
				$customFieldMacroValues = MM_WPFS_Utils::get_custom_field_macro_values(
					count( $customFieldMacros ),
					$transactionData->getCustomInputValues()
				);
				$macros                 = array_merge( $macros, $customFieldMacros );
				$macroValues            = array_merge( $macroValues, $customFieldMacroValues );
			}
			// tnagy call action
			$additionalData = MM_WPFS_Utils::prepare_additional_data_for_subscription_charge(
				MM_WPFS::ACTION_NAME_AFTER_SUBSCRIPTION_CHARGE,
				$subscriptionFormModel->getStripeCustomer(),
				$macros,
				$macroValues
			);
			do_action(
				MM_WPFS::ACTION_NAME_AFTER_SUBSCRIPTION_CHARGE,
				$subscriptionFormModel->getStripeCustomer(),
				$additionalData
			);
			// tnagy send confirmation email
			$sendPluginEmail = MM_WPFS_Utils::isSendingPluginEmail( $subscriptionFormModel );
			if ( $sendPluginEmail && 1 == $subscriptionFormModel->getForm()->sendEmailReceipt ) {
				$this->mailer->sendSubscriptionStartedEmailReceipt( $subscriptionFormModel->getForm(), $transactionData );
			}
		}

		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'processSubscription(): FINISHED, result=' . print_r( $subscriptionResult, true ) );
		}

		return $subscriptionResult;
	}

	/**
	 * @param $formId
	 * @param $formType
	 * @param $stripePlan
	 * @param $billingAddress
	 * @param $customInputLabels
	 * @param $customInputValues
	 *
	 * @return float
	 * @throws Exception
	 */
	private function get_vat_percent( $formId, $formType, $stripePlan, $billingAddress, $customInputLabels, $customInputValues ) {

		$form = $this->get_subscription_form_by_id( $formId, $formType );

		if ( isset( $form ) ) {
			if ( MM_WPFS::VAT_RATE_TYPE_NO_VAT === $form->vatRateType ) {
				$vatPercent = MM_WPFS::NO_VAT_PERCENT;
			} elseif ( MM_WPFS::VAT_RATE_TYPE_FIXED_VAT === $form->vatRateType ) {
				$vatPercent = $form->vatPercent;
			} elseif ( MM_WPFS::VAT_RATE_TYPE_CUSTOM_VAT === $form->vatRateType ) {
				$fromCountry = $form->defaultBillingCountry;
				$toCountry   = $billingAddress['country_code'];
				$vatPercent  = apply_filters(
					MM_WPFS::FILTER_NAME_GET_VAT_PERCENT,
					MM_WPFS::NO_VAT_PERCENT,
					$fromCountry,
					$toCountry,
					MM_WPFS_Utils::prepare_vat_filter_arguments(
						$formId,
						$formType,
						$stripePlan,
						$billingAddress,
						MM_WPFS_Utils::prepare_custom_input_data(
							$customInputLabels,
							$customInputValues
						)
					)
				);
			} else {
				/* This is an internal error, no need to localize it. */
				throw new Exception( sprintf( 'Unknown tax rate type: %s', $form->vatRateType ) );
			}
		} else {
			/* This is an internal error, no need to localize it. */
			throw new Exception( sprintf( 'Cannot find \'%s\' form with id=%s', $formType, $formId ) );
		}

		return $vatPercent;
	}

	/**
	 * @param $formId
	 * @param $formType
	 *
	 * @return array|null|object|void
	 * @throws Exception
	 */
	private function get_subscription_form_by_id( $formId, $formType ) {
		switch ( $formType ) {
			case MM_WPFS::FORM_TYPE_SUBSCRIPTION:
			case MM_WPFS::FORM_TYPE_INLINE_SUBSCRIPTION:
				$form = $this->db->get_subscription_form_by_id( $formId );
				break;
			case MM_WPFS::FORM_TYPE_CHECKOUT_SUBSCRIPTION:
			case MM_WPFS::FORM_TYPE_POPUP_SUBSCRIPTION:
				$form = $this->db->get_checkout_subscription_form_by_id( $formId );
				break;
			default:
				// Internal error message, no need to localize it
				throw new Exception( sprintf( 'Unknown form type: %s', $formType ) );
		}

		return $form;
	}

	/**
	 * Updates the given result by the given PaymentIntent or SetupIntent. When no PaymentIntent nor
	 * SetupIntent are given, we consider the subscription as successful.
	 *
	 * @param MM_WPFS_SubscriptionResult $subscriptionResult
	 * @param \StripeWPFS\Subscription $subscription
	 * @param \StripeWPFS\PaymentIntent $paymentIntent
	 * @param \StripeWPFS\SetupIntent $setupIntent
	 */
	private function handleIntent( $subscriptionResult, $subscription, $paymentIntent, $setupIntent ) {
		if ( $paymentIntent instanceof \StripeWPFS\PaymentIntent ) {
			if (
				\StripeWPFS\PaymentIntent::STATUS_REQUIRES_ACTION === $paymentIntent->status
				&& 'use_stripe_sdk' === $paymentIntent->next_action->type
			) {
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( 'handleIntent(): PaymentIntent requires action...' );
				}
				$subscriptionResult->setSuccess( false );
				$subscriptionResult->setRequiresAction( true );
				$subscriptionResult->setPaymentIntentClientSecret( $paymentIntent->client_secret );
				$subscriptionResult->setMessageTitle(
				/* translators: Banner title of pending transaction requiring a second factor authentication (SCA/PSD2) */
					__( 'Action required', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
				$subscriptionResult->setMessage(
				/* translators: Banner message of a one-time payment requiring a second factor authentication (SCA/PSD2) */
					__( 'The payment needs additional action before completion!', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
			} elseif (
				\StripeWPFS\PaymentIntent::STATUS_SUCCEEDED === $paymentIntent->status
				|| \StripeWPFS\PaymentIntent::STATUS_REQUIRES_CAPTURE === $paymentIntent->status
			) {
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( 'handleIntent(): PaymentIntent succeeded.' );
				}
				$this->db->updateSubscriptionByPaymentIntentToRunning( $paymentIntent->id );
				$subscriptionResult->setRequiresAction( false );
				$subscriptionResult->setSuccess( true );
				$subscriptionResult->setMessageTitle(
				/* translators: Banner title of successful transaction */
					__( 'Success', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
				$subscriptionResult->setMessage(
				/* translators: Banner message of successful payment */
					__( 'Payment Successful!', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
			} else {
				$subscriptionResult->setSuccess( false );
				$subscriptionResult->setMessageTitle(
				/* translators: Banner title of failed transaction */
					__( 'Failed', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
				$subscriptionResult->setMessage(
				// This is an internal error, no need to localize it
					sprintf( "Invalid PaymentIntent status '%s'.", $paymentIntent->status ) );
			}
		} elseif ( $setupIntent instanceof \StripeWPFS\SetupIntent ) {
			if (
				\StripeWPFS\SetupIntent::STATUS_REQUIRES_ACTION === $setupIntent->status
				&& 'use_stripe_sdk' === $setupIntent->next_action->type
			) {
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( 'handleIntent(): SetupIntent requires action...' );
				}
				$subscriptionResult->setSuccess( false );
				$subscriptionResult->setRequiresAction( true );
				$subscriptionResult->setSetupIntentClientSecret( $setupIntent->client_secret );
				$subscriptionResult->setMessageTitle(
				/* translators: Banner title of pending transaction requiring a second factor authentication (SCA/PSD2) */
					__( 'Action required', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
				$subscriptionResult->setMessage(
				/* translators: Banner message of a one-time payment requiring a second factor authentication (SCA/PSD2) */
					__( 'The payment needs additional action before completion!', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
			} elseif (
				\StripeWPFS\SetupIntent::STATUS_SUCCEEDED === $setupIntent->status
			) {
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( 'handleIntent(): SetupIntent succeeded.' );
				}
				$this->db->updateSubscriptionBySetupIntentToRunning( $setupIntent->id );
				$subscriptionResult->setRequiresAction( false );
				$subscriptionResult->setSuccess( true );
				$subscriptionResult->setMessageTitle(
				/* translators: Banner title of successful transaction */
					__( 'Success', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
				$subscriptionResult->setMessage(
				/* translators: Banner message of successful payment */
					__( 'Payment Successful!', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
			} else {
				$subscriptionResult->setSuccess( false );
				$subscriptionResult->setMessageTitle(
				/* translators: Banner title of failed transaction */
					__( 'Failed', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
				$subscriptionResult->setMessage(
				// This is an internal error, no need to localize it
					sprintf( "Invalid PaymentIntent status '%s'.", $setupIntent->status ) );
			}
		} else {
			/*
			 * WPFS-1012: When a Subscription has a trial period without a setup fee then the Invoice has no
			 * PaymentIntent. When SCA is not triggered then the pending SetupIntent is also missing.
			 * In these cases the PaymentIntent and SetupIntent are both null.
			 * We consider these subscriptions as successful.
			 */
			$this->db->updateSubscriptionToRunning( $subscription->id );
			$subscriptionResult->setRequiresAction( false );
			$subscriptionResult->setSuccess( true );
			$subscriptionResult->setMessageTitle(
			/* translators: Banner title of successful transaction */
				__( 'Success', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
			$subscriptionResult->setMessage(
			/* translators: Banner message of successful payment */
				__( 'Payment Successful!', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
		}
	}

	function fullstripe_popup_payment_charge() {
		try {

			$paymentFormModel = new MM_WPFS_Public_PopupPaymentFormModel();
			$bindingResult    = $paymentFormModel->bind();
			if ( $bindingResult->hasErrors() ) {
				$return = self::generate_return_value_from_bindings( $bindingResult );
			} else {
				$action = MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE === $paymentFormModel->getForm()->customAmount ?
					MM_WPFS::ACTION_NAME_BEFORE_CHECKOUT_CARD_CAPTURE : MM_WPFS::ACTION_NAME_BEFORE_CHECKOUT_PAYMENT_CHARGE;
				do_action( $action, $paymentFormModel->getAmount() );

				$checkoutSession = $this->checkoutSubmissionService->createCheckoutSessionByPaymentForm( $paymentFormModel );
				$return          = $this->generateReturnValueFromCheckoutSession( $checkoutSession );
			}

		} catch ( \StripeWPFS\Exception\CardException $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$messageTitle =
				/* translators: Banner title of error returned by Stripe */
				__( 'Stripe Error', MM_WPFS::L10N_DOMAIN_PUBLIC );
			$message      = $this->stripe->resolve_error_message_by_code( $e->getCode() );
			if ( is_null( $message ) ) {
				$message = MM_WPFS_Localization::translateLabel( $e->getMessage() );
			}
			$return = array(
				'success'          => false,
				'messageTitle'     => $messageTitle,
				'message'          => $message,
				'exceptionMessage' => $e->getMessage()
			);
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$return = array(
				'success'          => false,
				'messageTitle'     =>
				/* translators: Banner title of internal error */
					__( 'Internal Error', MM_WPFS::L10N_DOMAIN_PUBLIC ),
				'message'          => MM_WPFS_Localization::translateLabel( $e->getMessage() ),
				'exceptionMessage' => $e->getMessage()
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( apply_filters( 'fullstripe_popup_payment_charge_return_message', $return ) );
		exit;

	}

    function fullstripe_popup_donation_charge() {
        try {

            $donationFormModel = new MM_WPFS_Public_PopupDonationFormModel();
            $bindingResult    = $donationFormModel->bind();
            if ( $bindingResult->hasErrors() ) {
                $return = self::generate_return_value_from_bindings( $bindingResult );
            } else {
                do_action( MM_WPFS::ACTION_NAME_BEFORE_CHECKOUT_DONATION_CHARGE, $donationFormModel->getAmount() );

                $checkoutSession = $this->checkoutSubmissionService->createCheckoutSessionByDonationForm( $donationFormModel );
                $return          = $this->generateReturnValueFromCheckoutSession( $checkoutSession );
            }

        } catch ( \StripeWPFS\Exception\CardException $e ) {
            MM_WPFS_Utils::logException( $e, $this );
            $messageTitle =
                /* translators: Banner title of error returned by Stripe */
                __( 'Stripe Error', MM_WPFS::L10N_DOMAIN_PUBLIC );
            $message      = $this->stripe->resolve_error_message_by_code( $e->getCode() );
            if ( is_null( $message ) ) {
                $message = MM_WPFS_Localization::translateLabel( $e->getMessage() );
            }
            $return = array(
                'success'          => false,
                'messageTitle'     => $messageTitle,
                'message'          => $message,
                'exceptionMessage' => $e->getMessage()
            );
        } catch ( Exception $e ) {
            MM_WPFS_Utils::logException( $e, $this );
            $return = array(
                'success'          => false,
                'messageTitle'     =>
                /* translators: Banner title of internal error */
                    __( 'Internal Error', MM_WPFS::L10N_DOMAIN_PUBLIC ),
                'message'          => MM_WPFS_Localization::translateLabel( $e->getMessage() ),
                'exceptionMessage' => $e->getMessage()
            );
        }

        header( "Content-Type: application/json" );
        echo json_encode( apply_filters( 'fullstripe_popup_donation_charge_return_message', $return ) );
        exit;

    }

    /**
	 * @param \StripeWPFS\Checkout\Session $checkoutSession
	 *
	 * @return array
	 */
	private function generateReturnValueFromCheckoutSession( $checkoutSession ) {
		return array(
			'success'           => true,
			'checkoutSessionId' => $checkoutSession->id
		);
	}

	function fullstripe_popup_subscription_charge() {

		try {

			$subscriptionFormModel = new MM_WPFS_Public_PopupSubscriptionFormModel();
			$bindingResult         = $subscriptionFormModel->bind();

			if ( $bindingResult->hasErrors() ) {
				$return = self::generate_return_value_from_bindings( $bindingResult );
			} else {
				do_action(
					MM_WPFS::ACTION_NAME_BEFORE_CHECKOUT_SUBSCRIPTION_CHARGE,
					array(
						'plan'     => $subscriptionFormModel->getStripePlan()->id,
						'quantity' => $subscriptionFormModel->getStripePlanQuantity()
					)
				);
				$checkoutSession = $this->checkoutSubmissionService->createCheckoutSessionBySubscriptionForm( $subscriptionFormModel );
				$return          = $this->generateReturnValueFromCheckoutSession( $checkoutSession );
			}

		} catch ( \StripeWPFS\Exception\CardException $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$messageTitle =
				/* translators: Banner title of error returned by Stripe */
				__( 'Stripe Error', MM_WPFS::L10N_DOMAIN_PUBLIC );
			$message      = $this->stripe->resolve_error_message_by_code( $e->getCode() );
			if ( is_null( $message ) ) {
				$message = MM_WPFS_Localization::translateLabel( $e->getMessage() );
			}
			$return = array(
				'success'          => false,
				'messageTitle'     => $messageTitle,
				'message'          => $message,
				'exceptionMessage' => $e->getMessage()
			);
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$return = array(
				'success'          => false,
				'messageTitle'     =>
				/* translators: Banner title of internal error */
					__( 'Internal Error', MM_WPFS::L10N_DOMAIN_PUBLIC ),
				'message'          => MM_WPFS_Localization::translateLabel( $e->getMessage() ),
				'exceptionMessage' => $e->getMessage()
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( apply_filters( 'fullstripe_popup_subscription_charge_return_message', $return ) );
		exit;

	}

	function fullstripe_check_coupon() {
		$code = $_POST['code'];

		try {
			$coupon = $this->stripe->get_coupon( $code );
			if ( false == $coupon->valid ) {
				$return = array(
					'msg_title' =>
					/* translators: Banner title for messages related to applying a coupon */
						__( 'Coupon redemption', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'msg'       =>
					/* translators: Banner message of expired coupon */
						__( 'This coupon has expired.', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'valid'     => false
				);
			} else {
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( 'coupon=' . print_r( $coupon, true ) );
				}
				$return = array(
					'msg_title' =>
					/* translators: Banner title for messages related to applying a coupon */
						__( 'Coupon redemption', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'msg'       =>
					/* translators: Banner message of successfully applying a coupon */
						__( 'The coupon has been applied successfully.', MM_WPFS::L10N_DOMAIN_PUBLIC ),
					'coupon'    => array(
						'name'        => $coupon->id,
						'currency'    => $coupon->currency,
						'percent_off' => $coupon->percent_off,
						'amount_off'  => $coupon->amount_off
					),
					'valid'     => true
				);
			}
		} catch ( Exception $e ) {
			MM_WPFS_Utils::log( sprintf( 'Message=%s, Stack=%s ', $e->getMessage(), $e->getTraceAsString() ) );
			$return = array(
				'msg'   =>
				/* translators: Field error of non-existing or otherwise non-valid coupon */
					__( 'Entered coupon code is not valid anymore. Try with another one.', MM_WPFS::L10N_DOMAIN_PUBLIC ),
				'valid' => false
			);
		}

		header( "Content-Type: application/json" );
		echo json_encode( $return );
		exit;
	}

	function calculate_plan_amounts_and_setup_fees() {
		$formNameAsIdentifier = sanitize_text_field( $_POST['formId'] );
		$formType             = sanitize_text_field( $_POST['formType'] );
		$toCountry            = sanitize_text_field( $_POST['selectedCountry'] );
		$customInputValues    = isset( $_POST['customInputValues'] ) ? $_POST['customInputValues'] : null;

		$response = array(
			'formId'   => $formNameAsIdentifier,
			'formType' => $formType
		);

		try {
			$form = $this->get_subscription_form_by_name( $formNameAsIdentifier, $formType );
			if ( isset( $form ) && $form->vatRateType == MM_WPFS::VAT_RATE_TYPE_CUSTOM_VAT ) {
				$fromCountry  = $form->defaultBillingCountry;
				$customInputs = null;
				if ( $form->showCustomInput == 1 ) {
					$customInputLabels = MM_WPFS_Utils::decode_custom_input_labels( $form->customInputs );
					$customInputs      = MM_WPFS_Utils::prepare_custom_input_data( $customInputLabels, $customInputValues );
				}
				$vatPercent             = apply_filters( MM_WPFS::FILTER_NAME_GET_VAT_PERCENT, MM_WPFS::NO_VAT_PERCENT, $fromCountry, $toCountry, MM_WPFS_Utils::prepare_vat_filter_arguments( $formNameAsIdentifier, $formType, null, array( 'country' => $toCountry ), $customInputs ) );
				$response['vatPercent'] = $vatPercent;
				$stripePlans            = MM_WPFS::getInstance()->get_plans();
				$formPlans              = MM_WPFS_Utils::get_sorted_form_plans( $stripePlans, $form->plans );
				$plans                  = array();
				foreach ( $formPlans as $plan ) {
					$planSetupFee = MM_WPFS_Utils::get_setup_fee_for_plan( $plan );
					$planAmount   = $plan->amount;
					$aPlan        = array(
						'id'                                   => esc_attr( $plan->id ),
						'name'                                 => $plan->product->name,
						'planAmount'                           => MM_WPFS_Currencies::formatByForm( $form, $plan->currency, $planAmount ),
						'planAmountInSmallestCommonCurrency'   => $planAmount,
						'planSetupFee'                         => MM_WPFS_Currencies::formatByForm( $form, $plan->currency, $planSetupFee ),
						'planSetupFeeInSmallestCommonCurrency' => $planSetupFee,
						'vatPercent'                           => $vatPercent
					);
					array_push( $plans, $aPlan );
				}
				$response['plans']   = $plans;
				$response['success'] = true;
			} else {
				$response['success'] = false;
				// No need to localize it, this is an internal error
				$response['error'] = 'Form not found or form do not use custom VAT rates!';
			}
		} catch ( Exception $e ) {
			$response['success'] = false;
			$response['error']   = $e->getMessage();
		}

		header( "Content-Type: application/json" );
		echo json_encode( $response );
		exit;
	}

	/**
	 * @param $formName
	 * @param $formType
	 *
	 * @return array|mixed|null|object|void
	 * @throws Exception
	 */
	private function get_subscription_form_by_name( $formName, $formType ) {
		switch ( $formType ) {
			case MM_WPFS::FORM_TYPE_SUBSCRIPTION:
			case MM_WPFS::FORM_TYPE_INLINE_SUBSCRIPTION:
				$form = $this->db->getSubscriptionFormByName( $formName );
				break;
			case MM_WPFS::FORM_TYPE_CHECKOUT_SUBSCRIPTION:
			case MM_WPFS::FORM_TYPE_POPUP_SUBSCRIPTION:
				$form = $this->db->getCheckoutSubscriptionFormByName( $formName );
				break;
			default:
				// Internal error message, no need to localize it
				throw new Exception( sprintf( 'Unknown form type: %s', $formType ) );
		}

		return $form;
	}

}

class MM_WPFS_TransactionResult {

	/**
	 * @var boolean
	 */
	protected $success = false;
	/**
	 * @var string
	 */
	protected $messageTitle;
	/**
	 * @var string
	 */
	protected $message;
	/**
	 * @var boolean
	 */
	protected $redirect = false;
	/**
	 * @var string
	 */
	protected $redirectURL;
	/**
	 * @var boolean
	 */
	protected $requiresAction = false;
	/**
	 * @var string
	 */
	protected $paymentIntentClientSecret;
	/**
	 * @var string
	 */
	protected $setupIntentClientSecret;
	/**
	 * @var string
	 */
	protected $formType;
	/**
	 * @var string
	 */
	protected $nonce;

	/**
	 * @return boolean
	 */
	public function isSuccess() {
		return $this->success;
	}

	/**
	 * @param boolean $success
	 */
	public function setSuccess( $success ) {
		$this->success = $success;
	}

	/**
	 * @return string
	 */
	public function getMessageTitle() {
		return $this->messageTitle;
	}

	/**
	 * @param string $messageTitle
	 */
	public function setMessageTitle( $messageTitle ) {
		$this->messageTitle = $messageTitle;
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

	/**
	 * @return boolean
	 */
	public function isRedirect() {
		return $this->redirect;
	}

	/**
	 * @param boolean $redirect
	 */
	public function setRedirect( $redirect ) {
		$this->redirect = $redirect;
	}

	/**
	 * @return string
	 */
	public function getRedirectURL() {
		return $this->redirectURL;
	}

	/**
	 * @param string $redirectURL
	 */
	public function setRedirectURL( $redirectURL ) {
		$this->redirectURL = $redirectURL;
	}

	/**
	 * @return boolean
	 */
	public function isRequiresAction() {
		return $this->requiresAction;
	}

	/**
	 * @param boolean $requiresAction
	 */
	public function setRequiresAction( $requiresAction ) {
		$this->requiresAction = $requiresAction;
	}

	/**
	 * @return mixed
	 */
	public function getPaymentIntentClientSecret() {
		return $this->paymentIntentClientSecret;
	}

	/**
	 * @param mixed $paymentIntentClientSecret
	 */
	public function setPaymentIntentClientSecret( $paymentIntentClientSecret ) {
		$this->paymentIntentClientSecret = $paymentIntentClientSecret;
	}

	/**
	 * @return string
	 */
	public function getSetupIntentClientSecret() {
		return $this->setupIntentClientSecret;
	}

	/**
	 * @param string $setupIntentClientSecret
	 */
	public function setSetupIntentClientSecret( $setupIntentClientSecret ) {
		$this->setupIntentClientSecret = $setupIntentClientSecret;
	}

	/**
	 * @return string
	 */
	public function getFormType() {
		return $this->formType;
	}

	/**
	 * @param string $formType
	 */
	public function setFormType( $formType ) {
		$this->formType = $formType;
	}

	/**
	 * @return string
	 */
	public function getNonce() {
		return $this->nonce;
	}

	/**
	 * @param string $nonce
	 */
	public function setNonce( $nonce ) {
		$this->nonce = $nonce;
	}

}

class MM_WPFS_PaymentIntentResult extends MM_WPFS_ChargeResult {

	/**
	 * MM_WPFS_PaymentIntentResult constructor.
	 */
	public function __construct() {
		$this->formType = MM_WPFS::FORM_TYPE_INLINE_PAYMENT;
	}
}

class MM_WPFS_DonationPaymentIntentResult extends MM_WPFS_ChargeResult {

    /**
     * MM_WPFS_DonationPaymentIntentResult constructor.
     */
    public function __construct() {
        $this->formType = MM_WPFS::FORM_TYPE_INLINE_DONATION;
    }
}

class MM_WPFS_DonationCheckoutResult extends MM_WPFS_ChargeResult {

    /**
     * MM_WPFS_DonationCheckoutResult constructor.
     */
    public function __construct() {
        $this->formType = MM_WPFS::FORM_TYPE_POPUP_DONATION;
    }
}

class MM_WPFS_SetupIntentResult extends MM_WPFS_ChargeResult {

	/**
	 * MM_WPFS_PaymentIntentResult constructor.
	 */
	public function __construct() {
		$this->formType = MM_WPFS::FORM_TYPE_INLINE_SAVE_CARD;
	}
}


class MM_WPFS_ChargeResult extends MM_WPFS_TransactionResult {

	/**
	 * @var string
	 */
	protected $paymentType;

	/**
	 * @return string
	 */
	public function getPaymentType() {
		return $this->paymentType;
	}

	/**
	 * @param string $paymentType
	 */
	public function setPaymentType( $paymentType ) {
		$this->paymentType = $paymentType;
	}

}

class MM_WPFS_SubscriptionResult extends MM_WPFS_TransactionResult {

	/**
	 * MM_WPFS_SubscriptionResult constructor.
	 */
	public function __construct() {
		$this->formType = MM_WPFS::FORM_TYPE_INLINE_SUBSCRIPTION;
	}
}

class MM_WPFS_CreateOrRetrieveCustomerResult {

	/**
	 * @var \StripeWPFS\Customer
	 */
	private $customer;
	/**
	 * @var \StripeWPFS\PaymentMethod
	 */
	private $paymentMethod;

	/**
	 * @return \StripeWPFS\Customer
	 */
	public function getCustomer() {
		return $this->customer;
	}

	/**
	 * @param \StripeWPFS\Customer $customer
	 */
	public function setCustomer( $customer ) {
		$this->customer = $customer;
	}

	/**
	 * @return \StripeWPFS\PaymentMethod
	 */
	public function getPaymentMethod() {
		return $this->paymentMethod;
	}

	/**
	 * @param \StripeWPFS\PaymentMethod $paymentMethod
	 */
	public function setPaymentMethod( $paymentMethod ) {
		$this->paymentMethod = $paymentMethod;
	}

}
