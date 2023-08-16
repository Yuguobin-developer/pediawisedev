<?php

/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2019.08.16.
 * Time: 14:50
 */

trait MM_WPFS_DonationTools {
    /* @var stripe MM_WPFS_Database */
    /* @var mailer MM_WPFS_Mailer */

    /**
     * @param $donationFormModel MM_WPFS_Public_DonationFormModel
     *
     * @return boolean
     */
    private function isRecurringDonation( $donationFormModel ) {
        return $donationFormModel->getDonationFrequency() != MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_ONE_TIME ? true : false;
    }

    /**
     * @param $donationFormModel MM_WPFS_Public_DonationFormModel
     * @param $transactionData MM_WPFS_DonationTransactionData
     */
    protected function sendDonationEmailReceipt ( $donationFormModel, $transactionData ) {
        $this->mailer->sendDonationEmailReceipt( $donationFormModel, $transactionData );
    }

    /**
     * @param $currency string
     * @param $donationFrequency string
     *
     * @return string
     */
    protected function constructDonationPlanID( $currency, $donationFrequency ) {
        return MM_WPFS::DONATION_PLAN_ID_PREFIX . ucfirst( $currency ) . ucfirst( $donationFrequency );
    }

    /**
     * @param $donationInterval string
     *
     * @return string
     */
    protected function translateFrequencyToInterval( $donationFrequency ) {
        $res = 'month';

        switch( $donationFrequency ) {
            case MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_DAILY:
                $res = 'day';
                break;

            case MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_WEEKLY:
                $res = 'week';
                break;

            case MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_MONTHLY:
                $res = 'month';
                break;

            case MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_ANNUAL:
                $res = 'year';
                break;
        }

        return $res;
    }

    /**
     * @param $planID string
     * @param $currency string
     * @param $donationFrequency string
     *
     * @return \StripeWPFS\Plan
     * @throws Exception
     */
    protected function createDonationPlan( $planID, $currency, $donationFrequency ) {
        $planName = sprintf( __( "Recurring donation (%s)", MM_WPFS::L10N_DOMAIN_PUBLIC ), strtoupper( $currency ));
        $interval = $this->translateFrequencyToInterval( $donationFrequency );

        $plan = $this->stripe->createRecurringDonationPlan( $planID, $planName, $currency, $interval, 1 );

        return $plan;
    }

    /**
     * @param $currency string
     * @param $donationFrequency string
     *
     * @return \StripeWPFS\Plan
     * @throws Exception
     */
    protected function createOrRetrieveDonationPlan( $currency, $donationFrequency ) {
        $planID = $this->constructDonationPlanID( $currency, $donationFrequency );

        $plan = $this->stripe->retrieve_plan( $planID );
        if (is_null( $plan )) {
            $plan = $this->createDonationPlan( $planID, $currency, $donationFrequency );
        }

        return $plan;
    }

    /**
     * @param $donationFormModel MM_WPFS_Public_DonationFormModel
     *
     * @return \StripeWPFS\Subscription
     * @throws Exception
     */
    protected function createSubscriptionForDonation( $donationFormModel ) {
        $plan = $this->createOrRetrieveDonationPlan( $donationFormModel->getForm()->currency, $donationFormModel->getDonationFrequency() );
        $subscription = $this->stripe->subscribeCustomerToPlan( $donationFormModel->getStripeCustomer()->id, $plan->id );

        $this->stripe->createUsageRecordForSubscription( $subscription, $donationFormModel->getAmount() );

        return $subscription;
    }
}

abstract class MM_WPFS_CheckoutChargeHandler {

	/**
	 * @var bool
	 */
	protected $debugLog = false;
	/**
	 * @var MM_WPFS_Database
	 */
	protected $db;
	/**
	 * @var MM_WPFS_Stripe
	 */
	protected $stripe;
	/**
	 * @var MM_WPFS_CheckoutSubmissionService
	 */
	protected $checkoutSubmissionService;
	/**
	 * @var MM_WPFS_TransactionDataService
	 */
	protected $transactionDataService;
	/**
	 * @var MM_WPFS_Mailer
	 */
	protected $mailer;
	/**
	 * @var MM_WPFS_EventHandler
	 */
	protected $eventHandler;
	/** @var MM_WPFS_LoggerService */
	private $loggerService = null;

	/**
	 * MM_WPFS_CheckoutChargeHandler constructor.
	 */
	public function __construct() {
		$this->db                        = new MM_WPFS_Database();
		$this->stripe                    = new MM_WPFS_Stripe();
		$this->checkoutSubmissionService = new MM_WPFS_CheckoutSubmissionService();
		$this->transactionDataService    = new MM_WPFS_TransactionDataService();
		$this->mailer                    = new MM_WPFS_Mailer();
		$this->loggerService             = new MM_WPFS_LoggerService();
		$this->eventHandler              = new MM_WPFS_EventHandler(
			$this->db,
			$this->stripe,
			$this->mailer,
			$this->loggerService
		);
	}

	/**
	 * @param MM_WPFS_Public_PopupPaymentFormModel|MM_WPFS_Public_PopupSubscriptionFormModel $formModel
	 * @param \StripeWPFS\Checkout\Session $checkoutSession
	 *
	 * @return MM_WPFS_ChargeResult
	 */
	public abstract function handle( $formModel, $checkoutSession );

	/**
	 * @param MM_WPFS_Public_FormModel $formModel
	 * @param MM_WPFS_TransactionData $transactionData
	 * @param MM_WPFS_TransactionResult $transactionResult
	 */
	protected function handleRedirect( $formModel, $transactionData, $transactionResult ) {
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

}

class MM_WPFS_CheckoutPaymentChargeHandler extends MM_WPFS_CheckoutChargeHandler {

	public function handle( $formModel, $checkoutSession ) {

		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'MM_WPFS_CheckoutPaymentChargeHandler.handle(): CALLED' );
			MM_WPFS_Utils::log( 'MM_WPFS_CheckoutPaymentChargeHandler.handle(): formModel=' . print_r( $formModel, true ) );
			MM_WPFS_Utils::log( 'MM_WPFS_CheckoutPaymentChargeHandler.handle(): checkoutSession=' . print_r( $checkoutSession, true ) );
		}
		$chargeResult = new MM_WPFS_ChargeResult();
		$formId       = MM_WPFS_Utils::getFormId( $formModel->getForm() );
		$formType     = MM_WPFS::FORM_TYPE_PAYMENT;
		// tnagy create default TransactionData
		$transactionData = MM_WPFS_TransactionDataService::createPaymentDataByModel( $formModel );
		if ( MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE === $formModel->getForm()->customAmount ) {
			// tnagy update result with payment type
			$chargeResult->setPaymentType( MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE );
			// tnagy retrieve Stripe SetupIntent and update model
			$setupIntent = $this->checkoutSubmissionService->retrieveStripeSetupIntentByCheckoutSession( $checkoutSession );
			// tnagy retrieve Stripe PaymentMethod and update form model
			$paymentMethod = $this->checkoutSubmissionService->retrieveStripePaymentMethodBySetupIntent( $setupIntent );
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'MM_WPFS_CheckoutPaymentChargeHandler.handle(): paymentMethod=' . print_r( $paymentMethod, true ) );
			}
			if ( isset( $paymentMethod ) && $paymentMethod instanceof \StripeWPFS\PaymentMethod ) {
				if ( isset( $paymentMethod->billing_details ) ) {
					$formModel->setCardHolderName( $paymentMethod->billing_details->name );
					$formModel->setCardHolderEmail( $paymentMethod->billing_details->email );
					$formModel->setCardHolderPhone( $paymentMethod->billing_details->phone );
					$formModel->setBillingName( $paymentMethod->billing_details->name );
					if ( isset( $paymentMethod->billing_details->address ) ) {
						$formModel->updateBillingAddressByStripeAddressHash( $paymentMethod->billing_details->address );
					}
				}
				// tnagy find existing customer or create new customer
				$stripeCustomer = $this->checkoutSubmissionService->retrieveStripeCustomerByPaymentMethod( $paymentMethod );
				if ( is_null( $stripeCustomer ) ) {
					$stripeCustomer = MM_WPFS_Utils::find_existing_stripe_customer_anywhere_by_email(
						$this->db,
						$this->stripe,
						$formModel->getCardHolderEmail()
					);
				}
				if ( is_null( $stripeCustomer ) ) {
					$stripeCustomer = $this->stripe->createCustomerWithPaymentMethod(
						$paymentMethod->id,
						$formModel->getCardHolderName(),
						$formModel->getCardHolderEmail(),
						$formModel->getMetadata()
					);
				} else {
					$paymentMethod = $this->stripe->attachPaymentMethodToCustomerIfMissing(
						$stripeCustomer,
						$paymentMethod,
						/* set to default */
						true
					);
				}
				$formModel->setStripePaymentMethod( $paymentMethod );
				// tnagy update Stripe Customer's billing information based on PaymentMeethod
				$this->stripe->updateCustomerBillingAddressByPaymentMethod( $stripeCustomer, $paymentMethod );
				// $this->stripe->updateCustomerShippingAddressByPaymentMethod( $stripeCustomer, $paymentMethod );

				$formModel->setStripeCustomer( $stripeCustomer, true );
				$formModel->setTransactionId( $stripeCustomer->id );
				// tnagy create updated TransactionData for card capture
				$transactionData = MM_WPFS_TransactionDataService::createPaymentDataByModel( $formModel );
				// tnagy set metadata and description for customer
				$stripeCardSavedDescription  = MM_WPFS_Utils::prepareStripeCardSavedDescription( $formModel, $transactionData );
				$stripeCustomer->description = $stripeCardSavedDescription;
				$stripeCustomer->metadata    = $formModel->getMetadata();
				$stripeCustomer->save();
				// tnagy save capture event
				$this->insertCardCapture( $formModel, $stripeCustomer, $formId, $formType );
				do_action( MM_WPFS::ACTION_NAME_AFTER_CHECKOUT_CARD_CAPTURE, $stripeCustomer );
				// tnagy update result
				$chargeResult->setSuccess( true );
				$chargeResult->setMessageTitle(
				/* translators: Banner title of successful transaction */
					__( 'Success', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
				$chargeResult->setMessage(
				/* translators: Banner message of saving card successfully */
					__( 'Card saved successfully!', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
			} else {
				$chargeResult->setSuccess( true );
				$chargeResult->setMessageTitle(
				/* translators: Banner title of failed transaction */
					__( 'Failed', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
				$chargeResult->setMessage(
				/* It's an internal error, no need to localize it */
					'Cannot find PaymentMethod!' );
			}
		} else {
			// tnagy retrieve Stripe Customer and update form model
			$stripeCustomer = $this->checkoutSubmissionService->retrieveStripeCustomerByCheckoutSession( $checkoutSession );
			$formModel->setStripeCustomer( $stripeCustomer, true );
			// tnagy retrieve Stripe PaymentIntent and update model
			$paymentIntent = $this->checkoutSubmissionService->retrieveStripePaymentIntentByCheckoutSession( $checkoutSession );
			if ( isset( $paymentIntent ) && isset( $paymentIntent->id ) ) {
				$formModel->setTransactionId( $paymentIntent->id );
			}
			// tnagy retrieve Stripe PaymentMethod and update form model
			$paymentMethod = $this->checkoutSubmissionService->retrieveStripePaymentMethodByPaymentIntent( $paymentIntent );
			$formModel->setCardHolderName( $paymentMethod->billing_details->name );
			$formModel->setBillingName( $paymentMethod->billing_details->name );
			// tnagy create TransactionData for payment
			$transactionData = MM_WPFS_TransactionDataService::createPaymentDataByModel( $formModel );
			// tnagy assemble description for Stripe PaymentIntent
			$stripePaymentIntentDescription = MM_WPFS_Utils::prepareStripeChargeDescription( $formModel, $transactionData );
			// set customer's name
			$stripeCustomer->name = $formModel->getCardHolderName();
			$stripeCustomer->save();
			// tnagy update PaymentIntent with description and metadata
			$paymentIntent->description = empty( $stripePaymentIntentDescription ) ? null : $stripePaymentIntentDescription;
			$paymentIntent->metadata    = $formModel->getMetadata();
			$paymentIntent->save();
			$paymentIntent            = \StripeWPFS\PaymentIntent::retrieve( $paymentIntent->id );
			$paymentIntent->wpfs_form = $formModel->getFormName();
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'MM_WPFS_CheckoutPaymentProcessor.process(): paymentIntent=' . print_r( $paymentIntent, true ) );
			}
			do_action( MM_WPFS::ACTION_NAME_AFTER_CHECKOUT_PAYMENT_CHARGE, $paymentIntent );
			$this->insertPayment( $formModel, $paymentIntent, $stripeCustomer, $formId, $formType );
			// tnagy update result
			$chargeResult->setSuccess( true );
			$chargeResult->setMessageTitle(
			/* translators: Banner title of successful transaction */
				__( 'Success', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
			$chargeResult->setMessage(
			/* translators: Banner message of successful payment */
				__( 'Payment Successful!', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
		}

		$this->handleRedirect( $formModel, $transactionData, $chargeResult );

		if ( MM_WPFS_Utils::isSendingPluginEmail( $formModel ) && 1 == $formModel->getForm()->sendEmailReceipt ) {
			if ( MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE === $formModel->getForm()->customAmount ) {
				$this->mailer->sendCardCapturedEmailReceipt( $formModel );
			} else {
				$this->mailer->sendPaymentEmailReceipt( $formModel );
			}
		}

		return $chargeResult;
	}

    /**
	 * @param MM_WPFS_Public_PaymentFormModel $paymentFormModel
	 * @param \StripeWPFS\Customer $stripeCustomer
	 * @param $formId
	 * @param $formType
	 */
	private function insertCardCapture( $paymentFormModel, $stripeCustomer, $formId, $formType ) {
		$this->db->fullstripe_insert_card_capture(
			$stripeCustomer,
			$paymentFormModel->getCardHolderName(),
			$paymentFormModel->getBillingName(),
			$paymentFormModel->getBillingAddress( false ),
			$paymentFormModel->getShippingName(),
			$paymentFormModel->getShippingAddress( false ),
			$formId,
			$formType,
			$paymentFormModel->getFormName()
		);
	}

	/**
	 * @param MM_WPFS_Public_PaymentFormModel $paymentFormModel
	 * @param \StripeWPFS\PaymentIntent $paymentIntent
	 * @param \StripeWPFS\Customer $stripeCustomer
	 * @param $formId
	 * @param $formType
	 */
	private function insertPayment( $paymentFormModel, $paymentIntent, $stripeCustomer, $formId, $formType ) {
		$this->db->insertPayment(
			$paymentIntent,
			$paymentFormModel->getBillingName(),
			$paymentFormModel->getBillingAddress( false ),
			$paymentFormModel->getShippingName(),
			$paymentFormModel->getShippingAddress( false ),
			$stripeCustomer->id,
			$paymentFormModel->getCardHolderName(),
			$paymentFormModel->getCardHolderEmail(),
			$formId,
			$formType,
			$paymentFormModel->getFormName()
		);
	}

}


class MM_WPFS_CheckoutDonationChargeHandler extends MM_WPFS_CheckoutChargeHandler {
    use MM_WPFS_DonationTools;

    /**
     * @param $paymentIntent \StripeWPFS\PaymentIntent
     * @param $formModel MM_WPFS_Public_FormModel
     */
    private function copyCardholderDataFromPaymentIntentToFormModel( $paymentIntent, & $formModel ) {
        $paymentMethod = $this->checkoutSubmissionService->retrieveStripePaymentMethodByPaymentIntent($paymentIntent);
        $formModel->setCardHolderName($paymentMethod->billing_details->name);
        $formModel->setBillingName($paymentMethod->billing_details->name);
    }

    /**
     * @param $formModel MM_WPFS_Public_DonationFormModel
     * @param $stripeCustomer \StripeWPFS\Customer
     *
     * @throws Exception
     */
    private function updateStripeCustomer($formModel, & $stripeCustomer ) {
        $stripeCustomer->name = $formModel->getCardHolderName();
        $stripeCustomer->save();
    }

    /**
     * @param $formModel MM_WPFS_Public_DonationFormModel
     * @param $transactionData MM_WPFS_DonationTransactionData
     * @param $paymentIntent \StripeWPFS\PaymentIntent
     *
     * @throws Exception
     */
    private function updatePaymentIntent($formModel, $transactionData, & $paymentIntent ) {
        $paymentIntent->description = MM_WPFS_Utils::prepareStripeDonationDescription($formModel, $transactionData);
        $paymentIntent->metadata = $formModel->getMetadata();
        $paymentIntent->save();
        $paymentIntent = \StripeWPFS\PaymentIntent::retrieve($paymentIntent->id);
    }

    /**
     * @param $paymentIntent \StripeWPFS\PaymentIntent
     * @param $formName
     */
    protected function addFormNameToPaymentIntent( & $paymentIntent, $formName ) {
        $paymentIntent->wpfs_form = $formName;
    }

    /**
     * @param $donationFormModel MM_WPFS_Public_DonationFormModel
     * @param $paymentIntent \StripeWPFS\PaymentIntent
     */
    protected function fireAfterCheckoutDonationAction ( $donationFormModel, $paymentIntent ) {
        do_action( MM_WPFS::ACTION_NAME_AFTER_CHECKOUT_DONATION_CHARGE, $paymentIntent );
    }

    /**
     * @param $donationResult MM_WPFS_DonationCheckoutResult
     * @param $title string
     * @param $message string
     *
     * @return MM_WPFS_DonationCheckoutResult
     */
    protected function createDonationResultSuccess( & $donationResult, $title, $message ) {
        $donationResult->setSuccess( true );
        $donationResult->setMessageTitle( $title );
        $donationResult->setMessage( $message );

        return $donationResult;
    }


    /**
     * @param $paymentIntent \StripeWPFS\PaymentIntent
     * @param $stripeCustomer \StripeWPFS\Customer
     *
     * @return \StripeWPFS\PaymentMethod
     */
    protected function setDefaultPaymentMethodFromPaymentIntent( $paymentIntent, & $stripeCustomer ) {
        $paymentMethod = $this->checkoutSubmissionService->retrieveStripePaymentMethodByPaymentIntent( $paymentIntent );
        if ( ! is_null( $paymentMethod ) ) {
            $paymentMethod = $this->stripe->attachPaymentMethodToCustomerIfMissing(
                $stripeCustomer,
                $paymentMethod,
                /* set to default */
                true
            );
        }

        return $paymentMethod;
    }

    /**
     * @param MM_WPFS_Public_DonationFormModel $formModel
     * @param \StripeWPFS\Checkout\Session $checkoutSession
     *
     * @return MM_WPFS_ChargeResult
     * @throws Exception
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    public function handle($formModel, $checkoutSession) {

        if ($this->debugLog) {
            MM_WPFS_Utils::log(__CLASS__ . __FUNCTION__ . '(): CALLED');
            MM_WPFS_Utils::log(__CLASS__ . __FUNCTION__ . '(): formModel=' . print_r($formModel, true));
            MM_WPFS_Utils::log(__CLASS__ . __FUNCTION__ . '(): checkoutSession=' . print_r($checkoutSession, true));
        }

        $chargeResult = new MM_WPFS_DonationCheckoutResult();

        $stripeCustomer = $this->checkoutSubmissionService->retrieveStripeCustomerByCheckoutSession( $checkoutSession );
        $formModel->setStripeCustomer( $stripeCustomer, true );

        $paymentIntent = $this->checkoutSubmissionService->retrieveStripePaymentIntentByCheckoutSession( $checkoutSession );
        $formModel->setTransactionId( $paymentIntent->id );

        $this->copyCardholderDataFromPaymentIntentToFormModel( $paymentIntent, $formModel );

        $paymentMethod = $this->setDefaultPaymentMethodFromPaymentIntent( $paymentIntent, $stripeCustomer );
        $formModel->setStripePaymentMethod( $paymentMethod );

        $this->stripe->updateCustomerBillingAddressByPaymentMethod( $stripeCustomer, $paymentMethod );

        $transactionData = MM_WPFS_TransactionDataService::createDonationDataByFormModel($formModel);

        $this->updateStripeCustomer( $formModel, $stripeCustomer );
        $this->updatePaymentIntent( $formModel, $transactionData, $paymentIntent );

        $this->addFormNameToPaymentIntent( $paymentIntent, $formModel->getFormName() );

        $subscription = null;
        if ( $this->isRecurringDonation( $formModel )) {
            $subscription = $this->createSubscriptionForDonation( $formModel );
        }
        $this->db->insertCheckoutDonation( $formModel, $paymentIntent, $subscription );

        $this->fireAfterCheckoutDonationAction( $formModel, $paymentIntent );

        // tnagy update result
        $this->createDonationResultSuccess( $chargeResult,
                                            /* translators: Banner title of successful transaction */
                                            __('Success', MM_WPFS::L10N_DOMAIN_PUBLIC),
                                            /* translators: Banner message of successful payment */
                                            __('Donation Successful!', MM_WPFS::L10N_DOMAIN_PUBLIC));

        $this->handleRedirect($formModel, $transactionData, $chargeResult);

        if ( MM_WPFS_Utils::isSendingPluginEmail( $formModel )) {
            $this->sendDonationEmailReceipt( $formModel, $transactionData );
        }

        return $chargeResult;
    }
}


class MM_WPFS_CheckoutSubscriptionChargeHandler extends MM_WPFS_CheckoutChargeHandler {

	public function handle( $formModel, $checkoutSession ) {

		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'MM_WPFS_CheckoutSubscriptionChargeHandler.handle(): CALLED' );
			MM_WPFS_Utils::log( 'MM_WPFS_CheckoutSubscriptionChargeHandler.handle(): formModel=' . print_r( $formModel, true ) );
			MM_WPFS_Utils::log( 'MM_WPFS_CheckoutSubscriptionChargeHandler.handle(): checkoutSession=' . print_r( $checkoutSession, true ) );
		}

		$chargeResult = new MM_WPFS_ChargeResult();

		// tnagy retrieve Stripe Customer and update form model
		$stripeCustomer = $this->checkoutSubmissionService->retrieveStripeCustomerByCheckoutSession( $checkoutSession );
		$formModel->setStripeCustomer( $stripeCustomer, true );
		$stripeSubscription = $this->checkoutSubmissionService->retrieveStripeSubscriptionByCheckoutSession( $checkoutSession );
		$popupFormSubmit    = null;
		// tnagy retrieve Stripe Subscription and update form model
		if ( isset( $stripeSubscription ) && isset( $stripeSubscription->id ) ) {
			$formModel->setTransactionId( $stripeSubscription->id );
			if ( isset( $stripeSubscription->metadata ) && isset( $stripeSubscription->metadata->client_reference_id ) ) {
				$submitHash      = $stripeSubscription->metadata->client_reference_id;
				$popupFormSubmit = $this->db->find_popup_form_submit_by_hash( $submitHash );
			}
		}
		// tnagy retrieve Stripe PaymentIntent if exists
		$stripePaymentIntent = $this->checkoutSubmissionService->findPaymentIntentInCheckoutSession( $checkoutSession );
		// tnagy retrieve Stripe SetupIntent if exists
		$stripeSetupIntent = $this->checkoutSubmissionService->findSetupIntentInCheckoutSession( $checkoutSession );

		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'MM_WPFS_CheckoutSubscriptionChargeHandler.handle(): stripeCustomer=' . print_r( $stripeCustomer, true ) );
			MM_WPFS_Utils::log( 'MM_WPFS_CheckoutSubscriptionChargeHandler.handle(): paymentIntent=' . print_r( $stripePaymentIntent, true ) );
			MM_WPFS_Utils::log( 'MM_WPFS_CheckoutSubscriptionChargeHandler.handle(): setupIntent=' . print_r( $stripeSetupIntent, true ) );
		}

		// tnagy retrieve Stripe PaymentMethod and update form model
		$paymentMethod = $this->checkoutSubmissionService->retrieveStripePaymentMethodByPaymentIntent( $stripePaymentIntent );
		if ( is_null( $paymentMethod ) ) {
			$paymentMethod = $this->checkoutSubmissionService->retrieveStripePaymentMethodBySetupIntent( $stripeSetupIntent );
		}
		if ( ! is_null( $paymentMethod ) ) {
			// tnagy set as default PaymentMethod
			$paymentMethod = $this->stripe->attachPaymentMethodToCustomerIfMissing(
				$stripeCustomer,
				$paymentMethod,
				/* set to default */
				true
			);
			$formModel->setStripePaymentMethod( $paymentMethod );
			$this->stripe->updateCustomerBillingAddressByPaymentMethod( $stripeCustomer, $paymentMethod );
			// $this->stripe->updateCustomerShippingAddressByPaymentMethod( $stripeCustomer, $paymentMethod );
		}
		if ( ! is_null( $paymentMethod ) && isset( $paymentMethod->billing_details ) ) {
			$formModel->setCardHolderName( $paymentMethod->billing_details->name );
			$formModel->setCardHolderPhone( $paymentMethod->billing_details->phone );
			$formModel->setBillingName( $paymentMethod->billing_details->name );
			if ( isset( $paymentMethod->billing_details->address ) ) {
				$formModel->updateBillingAddressByStripeAddressHash( $paymentMethod->billing_details->address );
			}
		}
		// tnagy calculate VAT percent
		$vatPercent = $this->getVATPercent(
			$formModel->getForm()->checkoutSubscriptionFormID,
			MM_WPFS_Utils::getFormType( $formModel->getForm() ),
			$formModel->getStripePlan(),
			$formModel->getBillingAddress(),
			$formModel->getDecodedCustomInputLabels(),
			$formModel->getCustomInputvalues()
		);

		// save metadata for the subscription
		$stripeSubscription->metadata = $formModel->getMetadata();
		$stripeSubscription->save();

		// set customer's name
		$stripeCustomer->name = $formModel->getCardHolderName();
		$stripeCustomer->save();

		// tnagy insert subscriber
		$this->insertSubscriber(
			$stripeCustomer,
			$stripeSubscription,
			$stripePaymentIntent,
			$stripeSetupIntent,
			$formModel,
			$vatPercent
		);

		if ( isset( $stripePaymentIntent ) && $stripePaymentIntent instanceof \StripeWPFS\PaymentIntent ) {
			if (
				\StripeWPFS\PaymentIntent::STATUS_SUCCEEDED === $stripePaymentIntent->status
				|| \StripeWPFS\PaymentIntent::STATUS_REQUIRES_CAPTURE === $stripePaymentIntent->status
			) {
				$this->updateSubscriptionByPaymentIntentToRunning( $stripePaymentIntent );
			}
		}

		// tnagy process related events
		if ( isset( $popupFormSubmit ) && isset( $popupFormSubmit->relatedStripeEventIDs ) ) {
			$relatedStripeEventIDs = $this->retrieveStripeEventIDs( $popupFormSubmit->relatedStripeEventIDs );
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'MM_WPFS_CheckoutSubscriptionChargeHandler.handle(): Processing related Stripe events=' . print_r( $relatedStripeEventIDs, true ) );
			}
			foreach ( $relatedStripeEventIDs as $relatedStripeEventID ) {
				$stripeEvent = $this->retrieveStripeEvent( $relatedStripeEventID );
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( 'MM_WPFS_CheckoutSubscriptionChargeHandler.handle(): stripeEvent=' . print_r( $stripeEvent, true ) );
				}
				if ( isset( $stripeEvent ) ) {
					$this->eventHandler->handle( $stripeEvent );
				}
			}
		}

		$subscriptionDescription = sprintf(
		/* translators: Default payment description for initial subscription payments.
		 * p1: Cardholder's name
		 */
			__( 'Subscriber: %s', MM_WPFS::L10N_DOMAIN_PUBLIC ), $formModel->getCardHolderName() );
		// tnagy create TransactionData
		$transactionData = MM_WPFS_TransactionDataService::createSubscriptionDataByModel( $formModel, $vatPercent, $subscriptionDescription );
		$transactionData->setInvoiceUrl( $this->getLatestInvoiceUrl( $stripeSubscription ) );
		// tnagy prepare macros
		$macros      = MM_WPFS_Utils::get_subscription_macros();
		$macroValues = MM_WPFS_Utils::getSubscriptionMacroValues( $formModel->getForm(), $transactionData, MM_WPFS_Utils::ESCAPE_TYPE_NONE );
		if ( ! is_null( $transactionData->getCustomInputValues() ) && is_array( $transactionData->getCustomInputValues() ) ) {
			$customFieldMacros      = MM_WPFS_Utils::get_custom_field_macros();
			$customFieldMacroValues = MM_WPFS_Utils::get_custom_field_macro_values(
				count( $customFieldMacros ),
				$transactionData->getCustomInputValues()
			);
			$macros                 = array_merge( $macros, $customFieldMacros );
			$macroValues            = array_merge( $macroValues, $customFieldMacroValues );
		}
		$additionalData = MM_WPFS_Utils::prepare_additional_data_for_subscription_charge(
			MM_WPFS::ACTION_NAME_AFTER_CHECKOUT_SUBSCRIPTION_CHARGE,
			$formModel->getStripeCustomer(),
			$macros,
			$macroValues
		);
		do_action(
			MM_WPFS::ACTION_NAME_AFTER_CHECKOUT_SUBSCRIPTION_CHARGE,
			$formModel->getStripeCustomer(),
			$additionalData );
		$chargeResult->setSuccess( true );
		$chargeResult->setMessageTitle(
		/* translators: Banner title of successful transaction */
			__( 'Success', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
		$chargeResult->setMessage(
		/* translators: Banner message of successful payment */
			__( 'Payment Successful!', MM_WPFS::L10N_DOMAIN_PUBLIC ) );

		$this->handleRedirect( $formModel, $transactionData, $chargeResult );

		if ( MM_WPFS_Utils::isSendingPluginEmail( $formModel ) && 1 == $formModel->getForm()->sendEmailReceipt ) {
			$this->mailer->sendSubscriptionStartedEmailReceipt( $formModel->getForm(), $transactionData );
		}

		return $chargeResult;
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
	private function getVATPercent( $formId, $formType, $stripePlan, $billingAddress, $customInputLabels, $customInputValues ) {

		$form = $this->getSubscriptionFormById( $formId, $formType );

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
	private function getSubscriptionFormById( $formId, $formType ) {
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
	 * @param \StripeWPFS\Customer $stripeCustomer
	 * @param \StripeWPFS\Subscription $stripeSubscription
	 * @param \StripeWPFS\PaymentIntent $stripePaymentIntent
	 * @param \StripeWPFS\SetupIntent $stripeSetupIntent
	 * @param MM_WPFS_Public_PopupSubscriptionFormModel $subscriptionFormModel
	 * @param $vatPercent
	 */
	private function insertSubscriber( $stripeCustomer, $stripeSubscription, $stripePaymentIntent, $stripeSetupIntent, $subscriptionFormModel, $vatPercent ) {
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
			$subscriptionFormModel->getForm()->checkoutSubscriptionFormID,
			$subscriptionFormModel->getForm()->name,
			$vatPercent
		);
	}

	/**
	 * @param \StripeWPFS\PaymentIntent $paymentIntent
	 *
	 * @return bool|int
	 */
	private function updateSubscriptionByPaymentIntentToRunning( $paymentIntent ) {
		return $this->db->updateSubscriptionByPaymentIntentToRunning( $paymentIntent->id );
	}

	/**
	 * @param $encodedStripeEventIDs
	 *
	 * @return array|mixed|object
	 */
	protected function retrieveStripeEventIDs( $encodedStripeEventIDs ) {
		$decodedStripeEventIDs = json_decode( $encodedStripeEventIDs );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			$decodedStripeEventIDs = array();
		}
		if ( ! is_array( $decodedStripeEventIDs ) ) {
			$decodedStripeEventIDs = array();
		}

		return $decodedStripeEventIDs;
	}

	/**
	 * @param $stripeEventID
	 *
	 * @return static
	 */
	protected function retrieveStripeEvent( $stripeEventID ) {
		return \StripeWPFS\Event::retrieve( $stripeEventID );
	}

	protected function getLatestInvoiceUrl( \StripeWPFS\Subscription $stripeSubscription ) {
		$latestInvoice = $this->stripe->retrieveInvoice( $stripeSubscription->latest_invoice );

		return $latestInvoice->invoice_pdf;
	}

}
