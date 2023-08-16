<?php

interface MM_WPFS_Payment_API {
	function charge( $currency, $amount, $card, $description, $metadata = null, $stripeEmail = null );

	function create_plan( $id, $name, $currency, $amount, $setup_fee, $interval, $trial_days, $interval_count, $cancellation_count );

	function get_plans();

	function get_recipients();

	function create_recipient( $recipient );

	function create_transfer( $transfer );

	function get_coupon( $code );

	function get_customers_by_email( $email );

	function charge_customer( $customerId, $currency, $amount, $captureAmount, $description, $metadata = null, $stripeEmail = null );

	function retrieve_customer( $customerID );

	function update_customer_shipping_address( $stripe_customer, $shipping_name, $shipping_phone, $shipping_address );

	/**
	 * Add subscription to new customer
	 *
	 * @param string $stripeCustomerEmail
	 * @param string $stripePaymentMethodId
	 * @param $stripePlanId
	 * @param $stripePlanQuantity
	 * @param $taxPercent
	 * @param string $couponCode
	 * @param string $billingName
	 * @param array $billingAddress
	 * @param string $stripeCustomerPhone
	 * @param string $shippingName
	 * @param array $shippingAddress
	 * @param mixed $metadata
     * @param integer $billingCycleAnchorDay
     * @param integer $prorateUntilAnchorDay
	 *
	 * @return \StripeWPFS\Subscription
	 * @throws Exception
	 */
	function subscribe(
		$stripeCustomerEmail, $stripePaymentMethodId, $stripePlanId, $stripePlanQuantity, $taxPercent, $couponCode,
		$billingName, $billingAddress, $stripeCustomerPhone, $shippingName, $shippingAddress, $metadata,
        $billingCycleAnchorDay, $prorateUntilAnchorDay
	);

	/**
	 * Add subscription to existing customer
	 *
	 * @param $stripeCustomerId
	 * @param $stripePaymentMethodId
	 * @param $stripePlanId
	 * @param $stripePlanQuantity
	 * @param $taxPercent
	 * @param $couponCode
	 * @param $billingName
	 * @param $billingAddressAsCustomerShippingAddress
	 * @param null $customerPhone
	 * @param mixed $metadata
     * @param integer $billingCycleAnchorDay
     * @param integer $prorateUntilAnchorDay
	 *
	 * @return \StripeWPFS\Subscription
	 * @throws Exception when the plan or customer do not exist
	 */
	function subscribe_existing(
		$stripeCustomerId, $stripePaymentMethodId, $stripePlanId, $stripePlanQuantity, $taxPercent, $couponCode,
		$billingName, $customerPhone, $billingAddressAsCustomerShippingAddress, $metadata, $billingCycleAnchorDay,
        $prorateUntilAnchorDay
	);

	/**
	 * @param MM_WPFS_SubscriptionTransactionData $transactionData
	 *
	 * @return \StripeWPFS\Subscription
	 */
	function alternativeSubscribe( $transactionData );

	/**
	 * @param MM_WPFS_SubscriptionTransactionData $transactionData
	 *
	 * @return \StripeWPFS\Subscription
	 * @throws Exception
	 */
	function alternativeSubscribeExisting( $transactionData );

	/**
	 * @param $customerID
	 * @param $subscriptionID
	 *
	 * @return \StripeWPFS\Subscription
	 */
	function retrieve_subscription( $customerID, $subscriptionID );

	function cancel_subscription( $stripeCustomerID, $stripeSubscriptionID, $atPeriodEnd = false );

	/**
	 * Updates the subscription's quantity
	 *
	 * @param $stripeCustomerId
	 * @param $stripeSubscriptionId
	 * @param $newQuantity
	 *
	 * @return bool success
	 */
	function update_subscription_quantity( $stripeCustomerId, $stripeSubscriptionId, $newQuantity );

	/**
	 * @param $plan_id
	 * @param $plan_data
	 *
	 * @return mixed
	 */
	function update_plan( $plan_id, $plan_data );

	function delete_plan( $plan_id );

	/**
	 * @param bool|null $associativeArray
	 * @param array|null $productIds
	 *
	 * @return mixed
	 */
	function get_products( $associativeArray = false, $productIds = null );

	/**
	 * @param $charge_id
	 *
	 * @return mixed
	 */
	function capture_charge( $charge_id );

	/**
	 * @param $charge_id
	 *
	 * @return mixed
	 */
	function refund_charge( $charge_id );

	/**
	 * @param $paymentIntentId
	 *
	 * @return mixed
	 */
	function capturePaymentIntent( $paymentIntentId );

	/**
	 * @param $paymentIntentId
	 *
	 * @return mixed
	 */
	function cancelPaymentIntent( $paymentIntentId );

	/**
	 * @param $paymentIntentId
	 *
	 * @return mixed
	 */
	function refundPaymentIntent( $paymentIntentId );

	/**
	 * @param $paymentIntentId
	 *
	 * @return mixed
	 */
	function cancelOrRefundPaymentIntent( $paymentIntentId );

	public function retrieveSetupIntent( $stripeSetupIntentId );

	/**
	 * @param $stripePaymentMethodId
	 *
	 * @return \StripeWPFS\SetupIntent
	 */
	public function createSetupIntentWithPaymentMethod( $stripePaymentMethodId );

	/**
	 * @param $stripePaymentMethodId
	 *
	 * @return \StripeWPFS\PaymentMethod
	 * @throws Exception
	 */
	public function validatePaymentMethodCVCCheck( $stripePaymentMethodId );

	/**
	 * @param $stripeCustomerId
	 * @param $stripePaymentMethodId
	 * @param bool $setToDefault
	 *
	 * @return \StripeWPFS\Customer
	 */
	public function attachPaymentMethodToCustomerIfMissing( $stripeCustomer, $stripePaymentMethod, $setToDefault = false );

	/**
	 * @param \StripeWPFS\Customer $stripeCustomer
	 * @param \StripeWPFS\PaymentMethod $stripePaymentMethod
	 *
	 * @return mixed
	 */
	function updateCustomerBillingAddressByPaymentMethod( $stripeCustomer, $stripePaymentMethod );

	/**
	 * @param \StripeWPFS\Customer $stripeCustomer
	 * @param \StripeWPFS\PaymentMethod $stripePaymentMethod
	 *
	 * @return mixed
	 */
	function updateCustomerShippingAddressByPaymentMethod( $stripeCustomer, $stripePaymentMethod );

	/**
	 * @param $stripeCustomerId
	 * @param $paymentMethodCardFingerPrint
	 * @param $expiryYear
	 * @param $expiryMonth
	 *
	 * @return null|\StripeWPFS\PaymentMethod
	 */
	function findExistingPaymentMethodByFingerPrintAndExpiry( $stripeCustomerId, $paymentMethodCardFingerPrint, $expiryYear, $expiryMonth );

}

/**
 * Class MM_WPFS_Stripe
 *
 * deals with calls to Stripe API
 *
 */
class MM_WPFS_Stripe implements MM_WPFS_Payment_API {

	const DESIRED_STRIPE_API_VERSION = '2019-05-16';

	/**
	 * @var string
	 */
	const INVALID_NUMBER_ERROR = 'invalid_number';
	/**
	 * @var string
	 */
	const INVALID_NUMBER_ERROR_EXP_MONTH = 'invalid_number_exp_month';
	/**
	 * @var string
	 */
	const INVALID_NUMBER_ERROR_EXP_YEAR = 'invalid_number_exp_year';
	/**
	 * @var string
	 */
	const INVALID_EXPIRY_MONTH_ERROR = 'invalid_expiry_month';
	/**
	 * @var string
	 */
	const INVALID_EXPIRY_YEAR_ERROR = 'invalid_expiry_year';
	/**
	 * @var string
	 */
	const INVALID_CVC_ERROR = 'invalid_cvc';
	/**
	 * @var string
	 */
	const INCORRECT_NUMBER_ERROR = 'incorrect_number';
	/**
	 * @var string
	 */
	const EXPIRED_CARD_ERROR = 'expired_card';
	/**
	 * @var string
	 */
	const INCORRECT_CVC_ERROR = 'incorrect_cvc';
	/**
	 * @var string
	 */
	const INCORRECT_ZIP_ERROR = 'incorrect_zip';
	/**
	 * @var string
	 */
	const CARD_DECLINED_ERROR = 'card_declined';
	/**
	 * @var string
	 */
	const MISSING_ERROR = 'missing';
	/**
	 * @var string
	 */
	const PROCESSING_ERROR = 'processing_error';
	/**
	 * @var string
	 */
	const MISSING_PAYMENT_INFORMATION = 'missing_payment_information';
	/**
	 * @var string
	 */
	const COULD_NOT_FIND_PAYMENT_INFORMATION = 'Could not find payment information';

	private $debugLog = false;

	public function __construct() {
	}

	function get_error_codes() {
		return array(
			self::INVALID_NUMBER_ERROR,
			self::INVALID_NUMBER_ERROR_EXP_MONTH,
			self::INVALID_NUMBER_ERROR_EXP_YEAR,
			self::INVALID_EXPIRY_MONTH_ERROR,
			self::INVALID_EXPIRY_YEAR_ERROR,
			self::INVALID_CVC_ERROR,
			self::INCORRECT_NUMBER_ERROR,
			self::EXPIRED_CARD_ERROR,
			self::INCORRECT_CVC_ERROR,
			self::INCORRECT_ZIP_ERROR,
			self::CARD_DECLINED_ERROR,
			self::MISSING_ERROR,
			self::PROCESSING_ERROR,
			self::MISSING_PAYMENT_INFORMATION
		);
	}

	public function alternativeSubscribe( $transactionData ) {
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'alternativeSubscribe(): transactionData=' . print_r( $transactionData, true ) );
		}

		return $this->subscribe(
			$transactionData->getCustomerEmail(),
			$transactionData->getStripePaymentMethodId(),
			$transactionData->getPlanId(),
			$transactionData->getPlanQuantity(),
			$transactionData->getPlanAmountVATRate(),
			$transactionData->getCouponCode(),
			empty( $transactionData->getBillingName() ) ?
				$transactionData->getCustomerName() :
				$transactionData->getBillingName(),
			$transactionData->getBillingAddress(),
			$transactionData->getCustomerPhone(),
			$transactionData->getShippingName(),
			$transactionData->getShippingAddress(),
			$transactionData->getMetadata(),
            $transactionData->getBillingCycleAnchorDay(),
            $transactionData->getProrateUntilAnchorDay()
		);
	}


    /**
     * @param string $stripeCustomerId
     * @param string $stripePlanId
     *
     * @return \StripeWPFS\Subscription
     * @throws Exception
     */
	public function subscribeCustomerToPlan($stripeCustomerId, $stripePlanId ) {
        $subscriptionData = array(
            'customer'        => $stripeCustomerId,
            'items'           => array(
                array(
                    'plan'     => $stripePlanId,
                )
            )
        );

        $stripeSubscription = \StripeWPFS\Subscription::create( $subscriptionData );

        return $stripeSubscription;
    }

    /**
     * @param \StripeWPFS\Subscription $stripeSubscription
     * @param string $quantity
     *
     * @throws Exception
     */
    public function createUsageRecordForSubscription( $stripeSubscription, $quantity ) {
        $stripeSubscriptionItem = $stripeSubscription->items->data[0];

        \StripeWPFS\SubscriptionItem::createUsageRecord(
            $stripeSubscriptionItem->id,
            [
                'quantity' => $quantity,
                /*
                 * We add 5 minutes to avoid the following Stripe error message:
                 * "Cannot create the usage record with this timestamp because timestamps must be after
                 *  the subscription's last invoice period (or current period start time)."
                 */
                'timestamp' => time() + 5 * 60,
                'action' => 'set',
            ]
        );
    }

	/**
	 * @param string $stripeCustomerEmail
	 * @param string $stripePaymentMethodId
	 * @param $stripePlanId
	 * @param $stripePlanQuantity
	 * @param $taxPercent
	 * @param string $couponCode
	 * @param string $billingName
	 * @param array $billingAddress
	 * @param string $stripeCustomerPhone
	 * @param string $shippingName
	 * @param array $shippingAddress
	 * @param mixed $metadata
     * @param integer $billingCycleAnchorDay
     * @param integer $prorateUntilAnchorDay
	 *
	 * @return \StripeWPFS\Subscription
	 * @throws Exception
	 */
	public function subscribe(
		$stripeCustomerEmail, $stripePaymentMethodId, $stripePlanId, $stripePlanQuantity, $taxPercent, $couponCode,
		$billingName, $billingAddress, $stripeCustomerPhone, $shippingName, $shippingAddress, $metadata,
        $billingCycleAnchorDay, $prorateUntilAnchorDay
	) {

		if ( $this->debugLog ) {
			$billingAddressString = print_r( $billingAddress, true );
			$metadataString       = print_r( $metadata, true );

			MM_WPFS_Utils::log( "subscribe(): CALLED, params: stripeCustomerEmail={$stripeCustomerEmail}, planID={$stripePlanId}, taxPercent={$taxPercent}, stripePaymentMethodId={$stripePaymentMethodId}, couponCode={$couponCode}, billingAddress={$billingAddressString}, metadata={$metadataString}" );
		}

		$this->validatePaymentMethodCVCCheck( $stripePaymentMethodId );

		$params = array(
			'email'            => $stripeCustomerEmail,
			'payment_method'   => $stripePaymentMethodId,
			'invoice_settings' => array(
				'default_payment_method' => $stripePaymentMethodId
			)
		);
		if ( ! is_null( $billingName ) && ! empty( $billingName ) ) {
			$params['name'] = $billingName;
		}
		$stripeCustomer = \StripeWPFS\Customer::create( $params );
		if ( ! is_null( $billingAddress ) ) {
			$this->update_customer_billing_address( $stripeCustomer, $billingName, $billingAddress );
		}
		if ( ! is_null( $shippingAddress ) ) {
			$this->update_customer_shipping_address( $stripeCustomer, $shippingName, $stripeCustomerPhone, $shippingAddress );
		}

		$subscription = $this->createSubscriptionForCustomer(
			$stripeCustomer,
			$stripePlanId,
			$stripePlanQuantity,
			$taxPercent,
			$couponCode,
			$metadata,
            null,
            $billingCycleAnchorDay,
            $prorateUntilAnchorDay
		);

		return $subscription;
	}

	public function validatePaymentMethodCVCCheck( $stripePaymentMethodId ) {
		/* @var $paymentMethod \StripeWPFS\PaymentMethod */
		$paymentMethod = \StripeWPFS\PaymentMethod::retrieve( $stripePaymentMethodId );

		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'validatePaymentMethodCVC(): stripePaymentMethod=' . print_r( $paymentMethod, true ) );
		}

		if ( is_null( $paymentMethod->card->checks->cvc_check ) ) {
			throw new Exception(
			    /* translators: Validation error message for a card number without a CVC code */
			    __( 'Please enter a CVC code', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
		}

		return $paymentMethod;
	}

	/**
	 * Updates the \StripeWPFS\Customer object's address property with an appropriate address array.
	 *
	 * @param $stripe_customer \StripeWPFS\Customer
	 * @param $billing_name
	 * @param $billing_address array
	 *
	 * @return \StripeWPFS\Customer
	 */
	public function update_customer_billing_address( $stripe_customer, $billing_name, $billing_address ) {
		$stripe_array_hash = MM_WPFS_Utils::prepare_stripe_address_hash_from_array( $billing_address );
		if ( isset( $stripe_array_hash ) ) {
			$stripe_customer->address = $stripe_array_hash;
		}
		if ( ! empty( $billing_name ) ) {
			$stripe_customer->name = $billing_name;
		}
		$stripe_customer->save();

		return $stripe_customer;
	}

	/**
	 * Updates the \StripeWPFS\Customer object's shipping property with an appropriate address array.
	 *
	 * @param $stripe_customer \StripeWPFS\Customer
	 * @param $shipping_name
	 * @param $shipping_phone
	 * @param $shipping_address array
	 *
	 * @return \StripeWPFS\Customer
	 */
	public function update_customer_shipping_address( $stripe_customer, $shipping_name, $shipping_phone, $shipping_address ) {
		$stripe_shipping_hash = MM_WPFS_Utils::prepare_stripe_shipping_hash_from_array( $shipping_name, $shipping_phone, $shipping_address );
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'update_customer_shipping_address(): stripe_shipping_hash=' . print_r( $stripe_shipping_hash, true ) );
		}
		$stripe_customer->shipping = $stripe_shipping_hash;
		$stripe_customer->save();

		return $stripe_customer;
	}

	/**
	 * @param $stripeCustomer
	 * @param $stripePlanId
	 * @param $stripePlanQuantity
	 * @param $taxPercent
	 * @param $couponCode
	 * @param $metadata
	 * @param $stripePaymentMethodId
     * @param $billingCycleAnchorDay
     * @param $prorateUntilAnchorDay
	 *
	 * @return \StripeWPFS\Subscription
	 * @throws Exception
	 */
	private function createSubscriptionForCustomer(
		$stripeCustomer,
		$stripePlanId,
		$stripePlanQuantity,
		$taxPercent,
		$couponCode,
		$metadata,
		$stripePaymentMethodId,
        $billingCycleAnchorDay,
        $prorateUntilAnchorDay
	) {

		// tnagy check if plan exists
		$stripePlan = \StripeWPFS\Plan::retrieve( $stripePlanId );
		if ( ! isset( $stripePlan ) ) {
			throw new Exception( "Stripe plan with id '{$stripePlanId}' doesn't exist." );
		}

		// tnagy attach payment method to customer
		if ( ! is_null( $stripePaymentMethodId ) ) {
			$paymentMethod = \StripeWPFS\PaymentMethod::retrieve( $stripePaymentMethodId );
			$paymentMethod->attach( array( 'customer' => $stripeCustomer->id ) );
			// tnagy set as default payment method on the customer
			\StripeWPFS\Customer::update(
				$stripeCustomer->id,
				array(
					'invoice_settings' => array(
						'default_payment_method' => $stripePaymentMethodId
					)
				)
			);
		}

		// tnagy get setup fee
		$setupFee = MM_WPFS_Utils::get_setup_fee_for_plan( $stripePlan );
		if ( $setupFee > 0 ) {
			// tnagy add setup fee as invoice item
			\StripeWPFS\InvoiceItem::create( array(
					'unit_amount' => $setupFee,
					'currency'    => $stripePlan->currency,
					'quantity'    => $stripePlanQuantity,
					'customer'    => $stripeCustomer->id,
					'description' => sprintf(
					    /* translators: It's a line item for the initial payment of a subscription */
					    __( 'One-time setup fee (plan: %s)', MM_WPFS::L10N_DOMAIN_PUBLIC ), $stripePlan->id )
				)
			);
		}

        $hasBillingCycleAnchor          = $billingCycleAnchorDay > 0;
        $hasMonthlyBillingCycleAnchor   = $stripePlan->interval === 'month' && $hasBillingCycleAnchor;
        $hasTrialPeriod                 = $stripePlan->trial_period_days > 0;

        // tnagy create subscription
		$subscriptionData = array(
			'customer'        => $stripeCustomer->id,
            'trial_from_plan' => $hasMonthlyBillingCycleAnchor && $hasTrialPeriod ? false : true,
			'items'           => array(
				array(
					'plan'     => $stripePlan->id,
					'quantity' => $stripePlanQuantity
				)
			),
			'expand'          => array(
			    'latest_invoice',
				'latest_invoice.payment_intent',
				'pending_setup_intent'
			)
		);
		if ( $couponCode != '' ) {
			$subscriptionData['coupon'] = $couponCode;
		}
		if ( $taxPercent != 0.0 ) {
			$subscriptionData['tax_percent'] = $taxPercent;
		}
        if ( $hasMonthlyBillingCycleAnchor ) {
            if ( $hasTrialPeriod ) {
                $trialEndTimestamp = MM_WPFS_Utils::calculateTrialEndFromNow( $stripePlan->trial_period_days );
                $subscriptionData['trial_end'] = $trialEndTimestamp;
                $subscriptionData['billing_cycle_anchor'] = MM_WPFS_Utils::calculateBillingCycleAnchorFromTimestamp( $billingCycleAnchorDay, $trialEndTimestamp );
            } else {
                $subscriptionData['billing_cycle_anchor'] = MM_WPFS_Utils::calculateBillingCycleAnchorFromNow( $billingCycleAnchorDay );
            }

            if ( $prorateUntilAnchorDay ) {
                $subscriptionData['proration_behavior'] = 'create_prorations';
            } else {
                $subscriptionData['proration_behavior'] = 'none';
            }
        }
		if ( ! is_null( $metadata ) ) {
			$subscriptionData['metadata'] = $metadata;
		}
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'createSubscriptionForCustomer(): subscriptionData=' . print_r( $subscriptionData, true ) );
		}
		$stripeSubscription = \StripeWPFS\Subscription::create( $subscriptionData );
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'createSubscriptionForCustomer(): created subscription=' . print_r( $stripeSubscription, true ) );
		}

		return $stripeSubscription;
	}

	/**
	 * @param $customerID
	 *
	 * @return \StripeWPFS\Customer
	 */
	function retrieve_customer( $customerID ) {
		return \StripeWPFS\Customer::retrieve( $customerID );
	}

	public function alternativeSubscribeExisting( $transactionData ) {
		return $this->subscribe_existing(
			$transactionData->getStripeCustomerId(),
			$transactionData->getStripePaymentMethodId(),
			$transactionData->getPlanId(),
			$transactionData->getPlanQuantity(),
			$transactionData->getPlanAmountVATRate(),
			$transactionData->getCouponCode(),
			$transactionData->getBillingName(),
			$transactionData->getBillingAddress(),
			$transactionData->getCustomerPhone(),
			$transactionData->getMetadata(),
            $transactionData->getBillingCycleAnchorDay(),
            $transactionData->getProrateUntilAnchorDay()
		);
	}

	public function subscribe_existing(
		$stripeCustomerId, $stripePaymentMethodId, $stripePlanId, $stripePlanQuantity, $taxPercent, $couponCode,
		$billingName, $billingAddressAsCustomerShippingAddress, $customerPhone, $metadata, $billingCycleAnchorDay,
        $prorateUntilAnchorDay
	) {
		$subscription = null;

		if ( $this->debugLog ) {
			$metadataString = print_r( $metadata, true );
			MM_WPFS_Utils::log( "subscribe_existing(): CALLED, params: stripeCustomerID={$stripeCustomerId}, planID={$stripePlanId}, taxPercent={$taxPercent}, stripePaymentMethodId={$stripePaymentMethodId}, couponCode={$couponCode}, billingAddressAsCustomerShippingAddress={$billingAddressAsCustomerShippingAddress}, metadata={$metadataString}" );
		}

		$this->validatePaymentMethodCVCCheck( $stripePaymentMethodId );

		$stripeCustomer = \StripeWPFS\Customer::retrieve( $stripeCustomerId );
		if ( isset( $stripeCustomer ) && ( ! isset( $stripeCustomer->deleted ) || ! $stripeCustomer->deleted ) ) {
			$subscription = $this->createSubscriptionForCustomer(
				$stripeCustomer,
				$stripePlanId,
				$stripePlanQuantity,
				$taxPercent,
				$couponCode,
				$metadata,
				$stripePaymentMethodId,
                $billingCycleAnchorDay,
                $prorateUntilAnchorDay
			);
		} else {
			throw new Exception( "Stripe customer with id '{$stripeCustomerId}' doesn't exist." );
		}
		if ( ! is_null( $billingAddressAsCustomerShippingAddress ) ) {
			$this->update_customer_shipping_address( $stripeCustomer, $billingName, $customerPhone, $billingAddressAsCustomerShippingAddress );
		}

		return $subscription;
	}

	/**
	 * @param $code
	 *
	 * @return string|void
	 */
	function resolve_error_message_by_code( $code ) {
		if ( $code === self::INVALID_NUMBER_ERROR ) {
			$resolved_message =  /* translators: message for Stripe error code 'invalid_number' */
				__( 'Your card number is invalid.', MM_WPFS::L10N_DOMAIN_PUBLIC );
		} elseif ( $code === self::INVALID_EXPIRY_MONTH_ERROR || $code === self::INVALID_NUMBER_ERROR_EXP_MONTH ) {
			$resolved_message = /* translators: message for Stripe error code 'invalid_expiry_month' */
				__( 'Your card\'s expiration month is invalid.', MM_WPFS::L10N_DOMAIN_PUBLIC );
		} elseif ( $code === self::INVALID_EXPIRY_YEAR_ERROR || $code === self::INVALID_NUMBER_ERROR_EXP_YEAR ) {
			$resolved_message = /* translators: message for Stripe error code 'invalid_expiry_year' */
				__( 'Your card\'s expiration year is invalid.', MM_WPFS::L10N_DOMAIN_PUBLIC );
		} elseif ( $code === self::INVALID_CVC_ERROR ) {
			$resolved_message = /* translators: message for Stripe error code 'invalid_cvc' */
				__( 'Your card\'s security code is invalid.', MM_WPFS::L10N_DOMAIN_PUBLIC );
		} elseif ( $code === self::INCORRECT_NUMBER_ERROR ) {
			$resolved_message = /* translators: message for Stripe error code 'incorrect_number' */
				__( 'Your card number is incorrect.', MM_WPFS::L10N_DOMAIN_PUBLIC );
		} elseif ( $code === self::EXPIRED_CARD_ERROR ) {
			$resolved_message = /* translators: message for Stripe error code 'expired_card' */
				__( 'Your card has expired.', MM_WPFS::L10N_DOMAIN_PUBLIC );
		} elseif ( $code === self::INCORRECT_CVC_ERROR ) {
			$resolved_message = /* translators: message for Stripe error code 'incorrect_cvc' */
				__( 'Your card\'s security code is incorrect.', MM_WPFS::L10N_DOMAIN_PUBLIC );
		} elseif ( $code === self::INCORRECT_ZIP_ERROR ) {
			$resolved_message = /* translators: message for Stripe error code 'incorrect_zip' */
				__( 'Your card\'s zip code failed validation.', MM_WPFS::L10N_DOMAIN_PUBLIC );
		} elseif ( $code === self::CARD_DECLINED_ERROR ) {
			$resolved_message = /* translators: message for Stripe error code 'card_declined' */
				__( 'Your card was declined.', MM_WPFS::L10N_DOMAIN_PUBLIC );
		} elseif ( $code === self::MISSING_ERROR ) {
			$resolved_message = /* translators: message for Stripe error code 'missing' */
				__( 'There is no card on a customer that is being charged.', MM_WPFS::L10N_DOMAIN_PUBLIC );
		} elseif ( $code === self::PROCESSING_ERROR ) {
			$resolved_message = /* translators: message for Stripe error code 'processing_error' */
				__( 'An error occurred while processing your card.', MM_WPFS::L10N_DOMAIN_PUBLIC );
		} elseif ( $code === self::MISSING_PAYMENT_INFORMATION ) {
			$resolved_message = /* translators: Stripe error message 'Missing payment information' */
				__( 'Missing payment information', MM_WPFS::L10N_DOMAIN_PUBLIC );
		} elseif ( $code === self::COULD_NOT_FIND_PAYMENT_INFORMATION ) {
			$resolved_message = /* translators: Stripe error message 'Could not find payment information' */
				__( 'Could not find payment information', MM_WPFS::L10N_DOMAIN_PUBLIC );
		} else {
			$resolved_message = null;
		}

		return $resolved_message;
	}

	function charge( $currency, $amount, $card, $description, $metadata = null, $stripeEmail = null ) {
		$charge = array(
			'card'        => $card,
			'amount'      => $amount,
			'currency'    => $currency,
			'description' => $description
		);
		if ( isset( $stripeEmail ) ) {
			$charge['receipt_email'] = $stripeEmail;
		}
		if ( isset( $metadata ) ) {
			$charge['metadata'] = $metadata;
		}

		$result = \StripeWPFS\Charge::create( $charge );

		return $result;
	}

	function create_plan( $id, $name, $currency, $amount, $setup_fee, $interval, $trial_days, $interval_count, $cancellation_count ) {
        $plan_data = array(
            "amount"         => $amount,
            "interval"       => $interval,
            "nickname"       => $id,
            "product"        => array(
                "name" => $name,
            ),
            "currency"       => $currency,
            "interval_count" => $interval_count,
            "id"             => $id,
            "metadata"       => array(
                "cancellation_count" => $cancellation_count,
                "setup_fee"          => $setup_fee
            )
        );

        if ( $trial_days != 0 ) {
            $plan_data['trial_period_days'] = $trial_days;
        }

        \StripeWPFS\Plan::create( $plan_data );
	}

    function createRecurringDonationPlan( $id, $name, $currency, $interval, $interval_count ) {
        $plan_data = array(
            "amount"          => "1",
            "currency"        => $currency,
            "interval"        => $interval,
            "product"         => array(
                "name" => $name,
            ),
            "nickname"        => $id,
            "id"              => $id,
            "aggregate_usage" => 'last_ever',
            "interval_count"  => $interval_count,
            "usage_type"      => 'metered'
        );

        return \StripeWPFS\Plan::create( $plan_data );
    }


    /**
	 * @param $plan_id
	 *
	 * @return null|\StripeWPFS\Plan
	 */
	function retrieve_plan( $plan_id ) {
		try {
			$plan = \StripeWPFS\Plan::retrieve( array( "id" => $plan_id, "expand" => array( "product" ) ) );
		} catch ( Exception $e ) {
            // plan not found, let's fall through
			$plan = null;
		}

		return $plan;
	}

	public function get_customers_by_email( $email ) {
		$customers = array();

		try {
			do {
				$params        = array( 'limit' => 100, 'email' => $email );
				$last_customer = end( $customers );
				if ( $last_customer ) {
					$params['starting_after'] = $last_customer['id'];
				}
				$customer_collection = \StripeWPFS\Customer::all( $params );
				$customers           = array_merge( $customers, $customer_collection['data'] );
			} while ( $customer_collection['has_more'] );
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$customers = array();
		}

		return $customers;
	}

	/**
	 * @return array|\StripeWPFS\Collection
	 */
	public function get_plans() {
		$plans = array();
		try {
			do {
				$params    = array( 'limit' => 100, 'include[]' => 'total_count', 'expand' => array( 'data.product' ) );
				$last_plan = end( $plans );
				if ( $last_plan ) {
					$params['starting_after'] = $last_plan['id'];
				}
				$plan_collection = \StripeWPFS\Plan::all( $params );
				$plans           = array_merge( $plans, $plan_collection['data'] );
			} while ( $plan_collection['has_more'] );
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$plans = array();
		}

		return $plans;
	}

	function get_recipients() {
		try {
			$recipients = \StripeWPFS\Recipient::all();
		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$recipients = array();
		}

		return $recipients;
	}

	function create_recipient( $recipient ) {
		return \StripeWPFS\Recipient::create( $recipient );
	}

	function create_transfer( $transfer ) {
		return \StripeWPFS\Transfer::create( $transfer );
	}

	/**
	 * @param $code
	 *
	 * @return \StripeWPFS\Coupon
     * @throws \StripeWPFS\Exception\ApiErrorException
	 */
	function get_coupon( $code ) {
		return \StripeWPFS\Coupon::retrieve( $code );
	}

    /**
     * @param $invoiceId
     *
     * @return \StripeWPFS\Invoice
     * @throws \StripeWPFS\Exception\ApiErrorException
     */
    function retrieveInvoice($invoiceId ) {
        return \StripeWPFS\Invoice::retrieve( $invoiceId );
    }

    /**
	 * @param $paymentMethodId
	 * @param $customerName
	 * @param $customerEmail
	 * @param $metadata
	 *
	 * @return \StripeWPFS\Customer
	 */
	function createCustomerWithPaymentMethod( $paymentMethodId, $customerName, $customerEmail, $metadata ) {
		$customer = array(
			'payment_method'   => $paymentMethodId,
			'email'            => $customerEmail,
			'invoice_settings' => array(
				'default_payment_method' => $paymentMethodId
			)
		);

		if ( ! is_null( $customerName ) ) {
			$customer['name'] = $customerName;
		}

		if ( ! is_null( $metadata ) ) {
			$customer['metadata'] = $metadata;
		}

		return \StripeWPFS\Customer::create( $customer );
	}

	/**
	 * @param $customerId
	 * @param $currency
	 * @param $amount
	 * @param $captureAmount
	 * @param null $description
	 * @param null $metadata
	 * @param null $stripeEmail
	 *
	 * @return \StripeWPFS\ApiResource
	 */
	function charge_customer( $customerId, $currency, $amount, $captureAmount, $description, $metadata = null, $stripeEmail = null ) {
		$charge_parameters = array(
			'customer'    => $customerId,
			'amount'      => $amount,
			'currency'    => $currency,
			'description' => $description,
			'capture'     => $captureAmount
		);
		if ( isset( $stripeEmail ) ) {
			$charge_parameters['receipt_email'] = $stripeEmail;
		}
		if ( isset( $metadata ) ) {
			$charge_parameters['metadata'] = $metadata;
		}

		$charge = \StripeWPFS\Charge::create( $charge_parameters );

		return $charge;
	}

	/**
	 * @param $paymentMethodId
	 * @param $customerId
	 * @param $currency
	 * @param $amount
	 * @param $capture
	 * @param null $description
	 * @param null $metadata
	 * @param null $stripeEmail
	 *
	 * @return \StripeWPFS\PaymentIntent
	 */
	function createPaymentIntent( $paymentMethodId, $customerId, $currency, $amount, $capture, $description, $metadata = null, $stripeEmail = null ) {
		$paymentIntentParameters = array(
			'payment_method'      => $paymentMethodId,
			'amount'              => $amount,
			'currency'            => $currency,
			'confirmation_method' => 'manual',
			'confirm'             => true,
			'customer'            => $customerId,
		);
		if ( ! empty( $description ) ) {
			$paymentIntentParameters['description'] = $description;
		}
		if ( false === $capture ) {
			$paymentIntentParameters['capture_method'] = 'manual';
		}
		if ( isset( $stripeEmail ) ) {
			$paymentIntentParameters['receipt_email'] = $stripeEmail;
		}
		if ( isset( $metadata ) ) {
			$paymentIntentParameters['metadata'] = $metadata;
		}

		$intent = \StripeWPFS\PaymentIntent::create( $paymentIntentParameters );

		return $intent;
	}

	/**
	 * @param $paymentIntentId
	 *
	 * @return \StripeWPFS\PaymentIntent
	 */
	function retrievePaymentIntent( $paymentIntentId ) {
		$intent = \StripeWPFS\PaymentIntent::retrieve( $paymentIntentId );

		return $intent;
	}

	/**
	 * @param $stripePaymentMethodId
	 *
	 * @return \StripeWPFS\SetupIntent
	 */
	function createSetupIntentWithPaymentMethod( $stripePaymentMethodId ) {
		$params = array(
			'usage'                => 'off_session',
			'payment_method_types' => [ 'card' ],
			'payment_method'       => $stripePaymentMethodId,
			'confirm'              => false
		);
		$intent = \StripeWPFS\SetupIntent::create( $params );

		return $intent;
	}

	/**
	 * @param $stripeSetupIntentId
	 *
	 * @return \StripeWPFS\SetupIntent
	 */
	function retrieveSetupIntent( $stripeSetupIntentId ) {
		$intent = \StripeWPFS\SetupIntent::retrieve( $stripeSetupIntentId );

		return $intent;
	}

	/**
	 * Attaches the given PaymentMethod to the given Customer if the Customer do not have an identical PaymentMethod
	 * by card fingerprint.
	 *
	 * @param \StripeWPFS\Customer $stripeCustomer
	 * @param \StripeWPFS\PaymentMethod $currentPaymentMethod
	 * @param bool $setToDefault
	 *
	 * @return \StripeWPFS\PaymentMethod the attached PaymentMethod or the existing one
	 */
	function attachPaymentMethodToCustomerIfMissing( $stripeCustomer, $currentPaymentMethod, $setToDefault = false ) {
		$attachedPaymentMethod = null;
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log(
				"attachPaymentMethodToCustomerIfMissing(): CALLED, params: "
				. 'stripeCustomer=' . print_r( $stripeCustomer, true )
				. ', stripePaymentMethodId=' . print_r( $currentPaymentMethod, true )
				. ', setToDefault=' . ( $setToDefault ? 'true' : 'false' )
			);
		}
		if ( $stripeCustomer instanceof \StripeWPFS\Customer && $currentPaymentMethod instanceof \StripeWPFS\PaymentMethod ) {
			// WPFS-983: tnagy find existing PaymentMethod with identical fingerprint and reuse it
			$existingStripePaymentMethod = $this->findExistingPaymentMethodByFingerPrintAndExpiry(
				$stripeCustomer,
				$currentPaymentMethod->card->fingerprint,
				$currentPaymentMethod->card->exp_year,
				$currentPaymentMethod->card->exp_month
			);
			if ( $existingStripePaymentMethod instanceof \StripeWPFS\PaymentMethod ) {
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log(
						'attachPaymentMethodToCustomerIfMissing(): '
						. 'PaymentMethod with identical card fingerprint exists, won\'t attach.'
					);
				}
				$attachedPaymentMethod = $existingStripePaymentMethod;
			} else {
				if ( is_null( $currentPaymentMethod->customer ) ) {
					$currentPaymentMethod->attach( array( 'customer' => $stripeCustomer->id ) );
					if ( $this->debugLog ) {
						MM_WPFS_Utils::log( 'attachPaymentMethodToCustomerIfMissing(): PaymentMethod attached.' );
					}
				}
				$attachedPaymentMethod = $currentPaymentMethod;
			}
			if ( $setToDefault ) {
				\StripeWPFS\Customer::update(
					$stripeCustomer->id,
					array(
						'invoice_settings' => array(
							'default_payment_method' => $attachedPaymentMethod->id
						)
					)
				);
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( 'attachPaymentMethodToCustomerIfMissing(): Default PaymentMethod updated.' );
				}
			}

		}

		return $attachedPaymentMethod;
	}

	/**
	 * Find a Customer's PaymentMethod by fingerprint if exists.
	 *
	 * @param \StripeWPFS\Customer $stripeCustomer
	 * @param string $paymentMethodCardFingerPrint
	 * @param $expiryYear
	 * @param $expiryMonth
	 *
	 * @return null|\StripeWPFS\PaymentMethod the existing PaymentMethod
	 * @throws StripeWPFS\Exception\ApiErrorException
	 */
	public function findExistingPaymentMethodByFingerPrintAndExpiry( $stripeCustomer, $paymentMethodCardFingerPrint, $expiryYear, $expiryMonth ) {
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log(
				'findExistingPaymentMethodByFingerPrint(): CALLED, params: stripeCustomer='
				. print_r( $stripeCustomer, true ) . ', paymentMethodCardFingerPrint=' . $paymentMethodCardFingerPrint
			);
		}
		if ( empty( $paymentMethodCardFingerPrint ) ) {
			return null;
		}
		$paymentMethods        = \StripeWPFS\PaymentMethod::all( array(
				'customer' => $stripeCustomer->id,
				'type'     => 'card'
			)
		);
		$existingPaymentMethod = null;
		if ( $paymentMethods instanceof \StripeWPFS\Collection ) {
			foreach ( $paymentMethods['data'] as $paymentMethod ) {
				/**
				 * @var \StripeWPFS\PaymentMethod $paymentMethod
				 */
				if ( is_null( $existingPaymentMethod ) ) {
					if ( isset( $paymentMethod ) && isset( $paymentMethod->card ) && isset( $paymentMethod->card->fingerprint ) ) {
						if ( $paymentMethod->card->fingerprint == $paymentMethodCardFingerPrint &&
						     $paymentMethod->card->exp_year == $expiryYear &&
						     $paymentMethod->card->exp_month == $expiryMonth
						) {
							$existingPaymentMethod = $paymentMethod;
							if ( $this->debugLog ) {
								MM_WPFS_Utils::log(
									'findExistingPaymentMethodByFingerPrint(): Identical PaymentMethod found='
									. print_r( $existingPaymentMethod, true )
								);
							}
						}
					}
				}
			}
		}

		return $existingPaymentMethod;
	}

	function update_plan( $plan_id, $plan_data ) {
		if ( isset( $plan_id ) ) {
			/**
			 * @var \StripeWPFS\Plan
			 */
			$plan = \StripeWPFS\Plan::retrieve( array( "id" => $plan_id, "expand" => array( "product" ) ) );
			if ( isset( $plan_data ) ) {
				if ( array_key_exists( 'name', $plan_data ) && ! empty( $plan_data['name'] ) ) {
					$plan->product->name = $plan_data['name'];
				}
				if ( array_key_exists( 'statement_descriptor', $plan_data ) && ! empty( $plan_data['statement_descriptor'] ) ) {
					$plan->product->statement_descriptor = $plan_data['statement_descriptor'];
				} else {
					$plan->product->statement_descriptor = null;
				}
				if ( array_key_exists( 'setup_fee', $plan_data ) && ! empty( $plan_data['setup_fee'] ) ) {
					$plan->metadata->setup_fee = $plan_data['setup_fee'];
				} else {
					$plan->metadata->setup_fee = 0;
				}

				$plan->product->save();

				return $plan->save();
			}
		}

		return null;
	}

	public function delete_plan( $plan_id ) {
		if ( isset( $plan_id ) ) {
			$plan = \StripeWPFS\Plan::retrieve( $plan_id );

			return $plan->delete();
		}

		return null;
	}

	public function cancel_subscription( $stripeCustomerID, $stripeSubscriptionID, $atPeriodEnd = false ) {
		if ( isset( $stripeCustomerID ) && isset( $stripeSubscriptionID ) ) {
			if ( ! empty( $stripeCustomerID ) && ! empty( $stripeSubscriptionID ) ) {
				$subscription = $this->retrieve_subscription( $stripeCustomerID, $stripeSubscriptionID );
				if ( $subscription ) {
					do_action( MM_WPFS::ACTION_NAME_BEFORE_SUBSCRIPTION_CANCELLATION, $stripeSubscriptionID );
					/** @noinspection PhpUnusedLocalVariableInspection */
                    if ( $atPeriodEnd ) {
                        $cancellation_result = \StripeWPFS\Subscription::update(
                            $stripeSubscriptionID,
                            array (
                                'cancel_at_period_end' => true
                            )
                        );
                    } else {
                        $cancellation_result = $subscription->cancel();
                    }
					do_action( MM_WPFS::ACTION_NAME_AFTER_SUBSCRIPTION_CANCELLATION, $stripeSubscriptionID );
					if ( $cancellation_result instanceof \StripeWPFS\Subscription ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	function retrieve_subscription( $customerID, $subscriptionID ) {
		$cu = \StripeWPFS\Customer::retrieve( $customerID );

		return $cu->subscriptions->retrieve( $subscriptionID );
	}

    /**
     * @param $stripeSubscriptionId
     *
     * @return \StripeWPFS\Subscription
     * @throws Exception
     */
	public function retrieveSubscriptionWithPlanExpanded($stripeSubscriptionId ) {
        $retrieveAttributes = array(
            'expand' => 'data.plan'
        );

        $subscription = \StripeWPFS\Subscription::retrieve(
            $stripeSubscriptionId,
            $retrieveAttributes
        );

        return $subscription;
    }

	public function update_subscription_quantity( $stripeCustomerId, $stripeSubscriptionId, $newQuantity ) {
		if ( isset( $stripeCustomerId ) && isset( $stripeSubscriptionId ) && isset( $newQuantity ) ) {
			if ( ! empty( $stripeCustomerId ) && ! empty( $stripeSubscriptionId ) && ! empty( $newQuantity ) && is_numeric( $newQuantity ) ) {
				$subscription = $this->retrieve_subscription( $stripeCustomerId, $stripeSubscriptionId );
				if ( isset( $subscription ) ) {
					$allowMultipleSubscriptions = false;
					if ( isset( $subscription->metadata ) && isset( $subscription->metadata->allow_multiple_subscriptions ) ) {
						$allowMultipleSubscriptions = boolval( $subscription->metadata->allow_multiple_subscriptions );
					}
					$maximumQuantity = 0;
					if ( isset( $subscription->metadata ) && isset( $subscription->metadata->maximum_quantity_of_subscriptions ) ) {
						$maximumQuantity = $subscription->metadata->maximum_quantity_of_subscriptions;
					}
					if ( $allowMultipleSubscriptions ) {
						if ( $maximumQuantity > 0 && intval( $newQuantity ) > $maximumQuantity ) {
							throw new Exception( sprintf(
							    /* translators: Error message displayed when subscriber tries to set a quantity for a subscription which is beyond allowed value */
							    __(  "Subscription quantity '%d' is not allowed for this subscription!", MM_WPFS::L10N_DOMAIN_PUBLIC ), $newQuantity ));
						}
						if ( $subscription->quantity != intval( $newQuantity ) ) {
							\StripeWPFS\Subscription::update( $stripeSubscriptionId, array( 'quantity' => $newQuantity ) );
						}

						return true;
					} else {
						throw new Exception(
                            /* translators: Error message displayed when subscriber tries to set a quantity for a
                             * subscription where quantity other than one is not allowed.
                             */
						    __( 'Quantity update is not allowed for this subscription!', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
					}
				} else {
					throw new Exception( sprintf( __( "Subscription '%s' not found!", MM_WPFS::L10N_DOMAIN_PUBLIC ), $stripeSubscriptionId ));
				}
			} else {
			    // This is an internal error, no need to localize it
				throw new Exception( 'Invalid parameters!' );
			}
		} else {
            // This is an internal error, no need to localize it
			throw new Exception( 'Invalid parameters!' );
		}
	}

	function get_products( $associativeArray = false, $productIds = null ) {
		$products = array();
		try {

			$params = array(
				'limit'     => 100,
				'include[]' => 'total_count'
			);
			if ( ! is_null( $productIds ) && count( $productIds ) > 0 ) {
				$params['ids'] = $productIds;
			}
			$params            = array( 'active' => 'false', 'limit' => 100 );
			$productCollection = \StripeWPFS\Product::all( $params );
			foreach ( $productCollection->autoPagingIterator() as $product ) {
				if ( $associativeArray ) {
					$products[ $product->id ] = $product;
				} else {
					array_push( $products, $product );
				}
			}

			// MM_WPFS_Utils::log( 'params=' . print_r( $params, true ) );
			// MM_WPFS_Utils::log( 'productCollection=' . print_r( $productCollection, true ) );

		} catch ( Exception $e ) {
			MM_WPFS_Utils::logException( $e, $this );
			$products = array();
		}

		return $products;
	}

	function capture_charge( $charge_id ) {
		$charge = \StripeWPFS\Charge::retrieve( $charge_id );
		if ( $charge instanceof \StripeWPFS\Charge ) {
			return $charge->capture();
		}

		return $charge;
	}

	public function capturePaymentIntent( $paymentIntentId ) {
		$paymentIntent = \StripeWPFS\PaymentIntent::retrieve( $paymentIntentId );
		if ( $paymentIntent instanceof \StripeWPFS\PaymentIntent ) {
			return $paymentIntent->capture();
		}

		return $paymentIntent;
	}

	public function cancelPaymentIntent( $paymentIntentId ) {
		$paymentIntent = \StripeWPFS\PaymentIntent::retrieve( $paymentIntentId );
		if ( $paymentIntent instanceof \StripeWPFS\PaymentIntent ) {
			if (
				\StripeWPFS\PaymentIntent::STATUS_REQUIRES_PAYMENT_METHOD === $paymentIntent->status
				|| \StripeWPFS\PaymentIntent::STATUS_REQUIRES_CAPTURE === $paymentIntent->status
				|| \StripeWPFS\PaymentIntent::STATUS_REQUIRES_CONFIRMATION === $paymentIntent->status
				|| \StripeWPFS\PaymentIntent::STATUS_REQUIRES_ACTION === $paymentIntent->status
			) {
				$paymentIntent->cancel();

				return \StripeWPFS\PaymentIntent::retrieve( $paymentIntentId );
			}
		}

		return $paymentIntent;
	}

	public function refundPaymentIntent( $paymentIntentId ) {
		$paymentIntent = \StripeWPFS\PaymentIntent::retrieve( $paymentIntentId );
		if ( $paymentIntent instanceof \StripeWPFS\PaymentIntent ) {
			if (
				\StripeWPFS\PaymentIntent::STATUS_PROCESSING === $paymentIntent->status
				|| \StripeWPFS\PaymentIntent::STATUS_SUCCEEDED === $paymentIntent->status
			) {
				/** @var \StripeWPFS\Charge $lastCharge */
				$lastCharge = $paymentIntent->charges->data[0];

				return $this->refund_charge( $lastCharge->id );
			}
		}

		return $paymentIntent;
	}

	function refund_charge( $charge_id ) {
		$refund = \StripeWPFS\Refund::create( [
			'charge' => $charge_id
		] );

		return $refund;
	}

	public function cancelOrRefundPaymentIntent( $paymentIntentId ) {
		$paymentIntent = \StripeWPFS\PaymentIntent::retrieve( $paymentIntentId );
		if ( $paymentIntent instanceof \StripeWPFS\PaymentIntent ) {
			if (
				\StripeWPFS\PaymentIntent::STATUS_REQUIRES_PAYMENT_METHOD === $paymentIntent->status
				|| \StripeWPFS\PaymentIntent::STATUS_REQUIRES_CAPTURE === $paymentIntent->status
				|| \StripeWPFS\PaymentIntent::STATUS_REQUIRES_CONFIRMATION === $paymentIntent->status
				|| \StripeWPFS\PaymentIntent::STATUS_REQUIRES_ACTION === $paymentIntent->status
			) {
				return $paymentIntent->cancel();
			} elseif (
				\StripeWPFS\PaymentIntent::STATUS_PROCESSING === $paymentIntent->status
				|| \StripeWPFS\PaymentIntent::STATUS_SUCCEEDED === $paymentIntent->status
			) {
				/** @var \StripeWPFS\Charge $lastCharge */
				$lastCharge = $paymentIntent->charges->data[0];

				return $this->refund_charge( $lastCharge->id );
			}
		}

		return $paymentIntent;
	}

	public function updateCustomerBillingAddressByPaymentMethod( $stripeCustomer, $stripePaymentMethod ) {
		if ( $stripeCustomer instanceof \StripeWPFS\Customer && $stripePaymentMethod instanceof \StripeWPFS\PaymentMethod ) {
			$address = $this->fetchBillingAddressFromPaymentMethod( $stripePaymentMethod );
			if ( count( $address ) > 0 ) {
				\StripeWPFS\Customer::update(
					$stripeCustomer->id,
					array(
						'address' => $address
					)
				);
			}
		}
	}

	/**
	 * @param $stripePaymentMethod
	 *
	 * @return array
	 */
	private function fetchBillingAddressFromPaymentMethod( $stripePaymentMethod ) {
		$address = array();
		if (
			isset( $stripePaymentMethod->billing_details )
			&& isset( $stripePaymentMethod->billing_details->address )
			&& $this->isRealBillingAddressInPaymentMethod( $stripePaymentMethod )
		) {
			$billingDetailsAddress = $stripePaymentMethod->billing_details->address;
			if ( isset( $billingDetailsAddress->city ) ) {
				$address['city'] = $billingDetailsAddress->city;

			}
			if ( isset( $billingDetailsAddress->country ) ) {
				$address['country'] = $billingDetailsAddress->country;

			}
			if ( isset( $billingDetailsAddress->line1 ) ) {
				$address['line1'] = $billingDetailsAddress->line1;

			}
			if ( isset( $billingDetailsAddress->line2 ) ) {
				$address['line2'] = $billingDetailsAddress->line2;

			}
			if ( isset( $billingDetailsAddress->postal_code ) ) {
				$address['postal_code'] = $billingDetailsAddress->postal_code;

			}
			if ( isset( $billingDetailsAddress->state ) ) {
				$address['state'] = $billingDetailsAddress->state;

				return $address;

			}

			return $address;
		}

		return $address;
	}

	private function isRealBillingAddressInPaymentMethod( $stripePaymentMethod ) {
		$res = false;

		$billingDetailsAddress = $stripePaymentMethod->billing_details->address;
		if ( ! empty( $billingDetailsAddress->city )
		     && ! empty( $billingDetailsAddress->country )
		     && ! empty( $billingDetailsAddress->line1 )
		) {
			$res = true;
		}

		return $res;
	}

	public function updateCustomerShippingAddressByPaymentMethod( $stripeCustomer, $stripePaymentMethod ) {
		if ( $stripeCustomer instanceof \StripeWPFS\Customer && $stripePaymentMethod instanceof \StripeWPFS\PaymentMethod ) {
			$address = $this->fetchBillingAddressFromPaymentMethod( $stripePaymentMethod );
			if ( count( $address ) > 0 ) {
				\StripeWPFS\Customer::update(
					$stripeCustomer->id,
					array(
						'shipping' => array(
							'address' => $address
						)
					)
				);
			}
		}
	}

}