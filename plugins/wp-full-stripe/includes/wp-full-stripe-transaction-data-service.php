<?php

/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2016.11.29.
 * Time: 16:38
 */
class MM_WPFS_TransactionDataService {

	const KEY_PREFIX = 'wpfs_td_';
	const REQUEST_PARAM_NAME_WPFS_TRANSACTION_DATA_KEY = 'wpfs_td_key';

    /**
     * @param $donationFormModel MM_WPFS_Public_DonationFormModel
     *
     * @return MM_WPFS_DonationTransactionData
     */
	public static function createDonationDataByFormModel( $donationFormModel ) {
        $data = new MM_WPFS_DonationTransactionData();

        $stripeCustomerId = ! is_null( $donationFormModel->getStripeCustomer() ) ? $donationFormModel->getStripeCustomer()->id : null;

        $data->setFormName( $donationFormModel->getFormName() );
        $data->setStripePaymentIntentId( $donationFormModel->getStripePaymentIntentId() );
        $data->setStripePaymentMethodId( $donationFormModel->getStripePaymentMethodId() );
        $data->setStripeCustomerId( $stripeCustomerId );
        $data->setCustomerName( $donationFormModel->getCardHolderName() );
        $data->setCustomerEmail( $donationFormModel->getCardHolderEmail() );
        $data->setCustomerPhone( $donationFormModel->getCardHolderPhone() );
        $data->setCurrency( $donationFormModel->getForm()->currency );
        $data->setAmount( $donationFormModel->getAmount() );
        $data->setProductName( $donationFormModel->getProductName() );
        $data->setBillingName( $donationFormModel->getBillingName() );
        $data->setBillingAddress( $donationFormModel->getBillingAddress() );
        $data->setShippingName( $donationFormModel->getShippingName() );
        $data->setShippingAddress( $donationFormModel->getShippingAddress() );
        $data->setCustomInputValues( $donationFormModel->getCustomInputvalues() );
        $data->setTransactionId( $donationFormModel->getTransactionId() );

        return $data;
    }

	/**
	 * @param $formName
	 * @param $stripeToken
	 * @param $stripeCustomerId
	 * @param $customerEmail
	 * @param $customerPhone
	 * @param $currency
	 * @param $amount
	 * @param $productName
	 * @param $billingName
	 * @param $billingAddress
	 * @param $shippingName
	 * @param $shippingAddress
	 * @param null $customInputValues
	 *
	 * @return MM_WPFS_PaymentTransactionData
	 */
	public static function createPaymentDataByToken(
		$formName, $stripeToken, $stripeCustomerId, $customerEmail, $customerPhone, $currency, $amount, $productName,
		$billingName, $billingAddress, $shippingName, $shippingAddress, $customInputValues = null
	) {
		$transactionData = new MM_WPFS_PaymentTransactionData();

		$transactionData->setFormName( $formName );
		$transactionData->setStripeToken( $stripeToken );
		$transactionData->setStripeCustomerId( $stripeCustomerId );
		$transactionData->setCustomerEmail( $customerEmail );
		$transactionData->setCustomerPhone( $customerPhone );
		$transactionData->setCustomerName( $billingName );
		$transactionData->setCurrency( $currency );
		$transactionData->setAmount( $amount );
		$transactionData->setProductName( $productName );
		$transactionData->setBillingName( $billingName );
		$transactionData->setBillingAddress( $billingAddress );
		$transactionData->setShippingName( $shippingName );
		$transactionData->setShippingAddress( $shippingAddress );
		$transactionData->setCustomInputValues( $customInputValues );

		return $transactionData;
	}

	/**
	 * @param MM_WPFS_Public_PaymentFormModel $paymentFormModel
	 *
	 * @return MM_WPFS_PaymentTransactionData
	 */
	public static function createPaymentDataByModel( $paymentFormModel ) {
		$transactionData = MM_WPFS_TransactionDataService::createPaymentDataByPaymentMethod(
			$paymentFormModel->getFormName(),
			$paymentFormModel->getStripePaymentMethodId(),
			$paymentFormModel->getStripePaymentIntentId(),
			! is_null( $paymentFormModel->getStripeCustomer() ) ? $paymentFormModel->getStripeCustomer()->id : null,
			$paymentFormModel->getCardHolderName(),
			$paymentFormModel->getCardHolderEmail(),
			$paymentFormModel->getCardHolderPhone(),
			$paymentFormModel->getForm()->currency,
			$paymentFormModel->getAmount(),
			$paymentFormModel->getProductName(),
			$paymentFormModel->getBillingName(),
			$paymentFormModel->getBillingAddress(),
			$paymentFormModel->getShippingName(),
			$paymentFormModel->getShippingAddress(),
			$paymentFormModel->getCustomInputvalues()
		);
		$transactionData->setTransactionId( $paymentFormModel->getTransactionId() );

		return $transactionData;
	}

	/**
	 * @param $formName
	 * @param $stripePaymentMethodId
	 * @param $stripePaymentIntentId
	 * @param $stripeCustomerId
	 * @param $customerName
	 * @param $customerEmail
	 * @param $customerPhone
	 * @param $currency
	 * @param $amount
	 * @param $productName
	 * @param $billingName
	 * @param $billingAddress
	 * @param $shippingName
	 * @param $shippingAddress
	 * @param null $customInputValues
	 *
	 * @return MM_WPFS_PaymentTransactionData
	 */
	public static function createPaymentDataByPaymentMethod(
		$formName, $stripePaymentMethodId, $stripePaymentIntentId, $stripeCustomerId,
		$customerName, $customerEmail, $customerPhone,
		$currency, $amount, $productName, $billingName, $billingAddress, $shippingName, $shippingAddress,
		$customInputValues = null
	) {
		$transactionData = new MM_WPFS_PaymentTransactionData();

		$transactionData->setFormName( $formName );
		$transactionData->setStripePaymentMethodId( $stripePaymentMethodId );
		$transactionData->setStripePaymentIntentId( $stripePaymentIntentId );
		$transactionData->setStripeCustomerId( $stripeCustomerId );
		$transactionData->setCustomerEmail( $customerEmail );
		$transactionData->setCustomerPhone( $customerPhone );
		$transactionData->setCustomerName( $customerName );
		$transactionData->setCurrency( $currency );
		$transactionData->setAmount( $amount );
		$transactionData->setProductName( $productName );
		$transactionData->setBillingName( $billingName );
		$transactionData->setBillingAddress( $billingAddress );
		$transactionData->setShippingName( $shippingName );
		$transactionData->setShippingAddress( $shippingAddress );
		$transactionData->setCustomInputValues( $customInputValues );

		return $transactionData;
	}

	/**
	 * @param MM_WPFS_Public_SubscriptionFormModel $subscriptionFormModel
	 * @param $vatPercent
	 * @param $subscriptionDescription
	 *
	 * @return MM_WPFS_SubscriptionTransactionData
	 */
	public static function createSubscriptionDataByModel( $subscriptionFormModel, $vatPercent, $subscriptionDescription ) {
		$form            = $subscriptionFormModel->getForm();
		$billingAddress  = null;
		$shippingAddress = null;
		if ( isset( $form->showAddress ) ) {
			$billingAddress  = 1 == $form->showAddress ? $subscriptionFormModel->getBillingAddress() : null;
			$shippingAddress = 1 == $form->showAddress ? $subscriptionFormModel->getShippingAddress() : null;
		}
		if ( isset( $form->showBillingAddress ) ) {
			$billingAddress = 1 == $form->showBillingAddress ? $subscriptionFormModel->getBillingAddress() : null;
		}
		if ( isset( $form->showShippingAddress ) ) {
			$shippingAddress = 1 == $form->showShippingAddress ? $subscriptionFormModel->getShippingAddress() : null;
		}

		$transactionData = MM_WPFS_TransactionDataService::createSubscriptionDataByPaymentMethod(
			$subscriptionFormModel->getFormName(),
			$subscriptionFormModel->getStripePaymentMethodId(),
			$subscriptionFormModel->getStripeCustomer()->id,
			$subscriptionFormModel->getCardHolderName(),
			$subscriptionFormModel->getCardHolderEmail(),
			$subscriptionFormModel->getCardHolderPhone(),
			$subscriptionFormModel->getStripePlanId(),
			$subscriptionFormModel->getStripePlan()->product->name,
			$subscriptionFormModel->getStripePlan()->currency,
			$subscriptionFormModel->getStripePlanAmount(),
			$subscriptionFormModel->getStripePlanSetupFee(),
			$subscriptionFormModel->getStripePlanQuantity(),
			$subscriptionFormModel->getProductName(),
			$subscriptionFormModel->getBillingName(),
			$billingAddress,
			$subscriptionFormModel->getShippingName(),
			$shippingAddress,
			$subscriptionFormModel->getCustomInputvalues(),
			$vatPercent,
			$subscriptionDescription,
			$subscriptionFormModel->getCouponCode(),
			$subscriptionFormModel->getMetadata(),
			0, // Anchor day not supported on checkout forms yet
			1  // Proration until anchor day is not supported on checkout forms yet
		);
		$transactionData->setTransactionId( $subscriptionFormModel->getTransactionId() );

		return $transactionData;
	}

	/**
	 * @param $formName
	 * @param $stripePaymentMethodId
	 * @param $stripeCustomerId
	 * @param $customerName
	 * @param $customerEmail
	 * @param $customerPhone
	 * @param $stripePlanId
	 * @param $stripePlanName
	 * @param $stripePlanCurrency
	 * @param $stripePlanAmount
	 * @param $stripePlanSetupFee
	 * @param $stripePlanQuantity
	 * @param $productName
	 * @param $billingName
	 * @param array $billingAddress
	 * @param $shippingName
	 * @param array $shippingAddress
	 * @param $customInputValues
	 * @param $vatPercent
	 * @param $subscriptionDescription
	 * @param $couponCode
	 * @param array $metadata
	 * @param integer $subscriptionAnchorDay
	 * @param integer $prorateUntilAnchorDay
	 *
	 * @return MM_WPFS_SubscriptionTransactionData
	 */
	public static function createSubscriptionDataByPaymentMethod(
		$formName, $stripePaymentMethodId, $stripeCustomerId,
		$customerName, $customerEmail, $customerPhone, $stripePlanId,
		$stripePlanName, $stripePlanCurrency, $stripePlanAmount, $stripePlanSetupFee, $stripePlanQuantity, $productName,
		$billingName, $billingAddress, $shippingName, $shippingAddress, $customInputValues, $vatPercent,
		$subscriptionDescription, $couponCode, $metadata, $subscriptionAnchorDay, $prorateUntilAnchorDay
	) {

		$planAmountGrossComposite        = MM_WPFS_Utils::calculateGrossFromNet( $stripePlanAmount, $vatPercent );
		$planSetupFeeGrossComposite      = MM_WPFS_Utils::calculateGrossFromNet( $stripePlanSetupFee, $vatPercent );
		$planAmountGrossTotalComposite   = MM_WPFS_Utils::calculateGrossFromNet( $stripePlanQuantity * $stripePlanAmount, $vatPercent );
		$planSetupFeeGrossTotalComposite = MM_WPFS_Utils::calculateGrossFromNet( $stripePlanQuantity * $stripePlanSetupFee, $vatPercent );

		$transactionData = new MM_WPFS_SubscriptionTransactionData();

		$transactionData->setFormName( $formName );
		$transactionData->setStripePaymentMethodId( $stripePaymentMethodId );
		$transactionData->setStripeCustomerId( $stripeCustomerId );
		$transactionData->setCustomerName( $customerName );
		$transactionData->setCustomerEmail( $customerEmail );
		$transactionData->setCustomerPhone( $customerPhone );
		$transactionData->setPlanId( $stripePlanId );
		$transactionData->setPlanName( $stripePlanName );
		$transactionData->setPlanCurrency( $stripePlanCurrency );
		$transactionData->setPlanNetSetupFee( $stripePlanSetupFee );
		$transactionData->setPlanGrossSetupFee( $planSetupFeeGrossComposite['gross'] );
		$transactionData->setPlanSetupFeeVAT( $transactionData->getPlanGrossSetupFee() - $transactionData->getPlanNetSetupFee() );
		$transactionData->setPlanSetupFeeVATRate( $vatPercent );
		$transactionData->setPlanNetSetupFeeTotal( $planSetupFeeGrossTotalComposite['net'] );
		$transactionData->setPlanGrossSetupFeeTotal( $planSetupFeeGrossTotalComposite['gross'] );
		$transactionData->setPlanSetupFeeVATTotal( $planSetupFeeGrossTotalComposite['taxValue'] );
		$transactionData->setPlanNetAmount( $stripePlanAmount );
		$transactionData->setPlanGrossAmount( $planAmountGrossComposite['gross'] );
		$transactionData->setPlanAmountVAT( $transactionData->getPlanGrossAmount() - $transactionData->getPlanNetAmount() );
		$transactionData->setPlanAmountVATRate( $vatPercent );
		$transactionData->setPlanQuantity( $stripePlanQuantity );
		$transactionData->setPlanNetAmountTotal( $planAmountGrossTotalComposite['net'] );
		$transactionData->setPlanGrossAmountTotal( $planAmountGrossTotalComposite['gross'] );
		$transactionData->setPlanAmountVATTotal( $planAmountGrossTotalComposite['taxValue'] );
		$transactionData->setProductName( $productName );
		$transactionData->setBillingName( $billingName );
		$transactionData->setBillingAddress( $billingAddress );
		$transactionData->setShippingName( $shippingName );
		$transactionData->setShippingAddress( $shippingAddress );
		$transactionData->setCustomInputValues( $customInputValues );
		$transactionData->setSubscriptionDescription( $subscriptionDescription );
		$transactionData->setCouponCode( $couponCode );
		$transactionData->setMetadata( $metadata );
		$transactionData->setBillingCycleAnchorDay( $subscriptionAnchorDay );
		$transactionData->setProrateUntilAnchorDay( $prorateUntilAnchorDay );

		return $transactionData;
	}

	/**
	 * @deprecated
	 *
	 * @param $formName
	 * @param $stripeToken
	 * @param $stripeCustomerId
	 * @param $customerEmail
	 * @param $customerPhone
	 * @param $stripePlanId
	 * @param $stripePlanName
	 * @param $stripePlanCurrency
	 * @param $stripePlanAmount
	 * @param $stripePlanSetupFee
	 * @param $stripePlanQuantity
	 * @param $productName
	 * @param $billingName
	 * @param array $billingAddress
	 * @param $shippingName
	 * @param array $shippingAddress
	 * @param $customInputValues
	 * @param $vatPercent
	 * @param $subscriptionDescription
	 * @param $couponCode
	 * @param array $metadata
	 * @param integer $subscriptionAnchorDay
	 * @param integer $prorateUntilAnchorDay
	 *
	 * @return MM_WPFS_SubscriptionTransactionData
	 */
	public static function createSubscriptionData(
		$formName, $stripeToken, $stripeCustomerId, $customerEmail, $customerPhone, $stripePlanId, $stripePlanName,
		$stripePlanCurrency, $stripePlanAmount, $stripePlanSetupFee, $stripePlanQuantity, $productName, $billingName, $billingAddress,
		$shippingName, $shippingAddress, $customInputValues, $vatPercent, $subscriptionDescription, $couponCode,
		$metadata, $subscriptionAnchorDay, $prorateUntilAnchorDay
	) {

		$planAmountGrossComposite        = MM_WPFS_Utils::calculateGrossFromNet( $stripePlanAmount, $vatPercent );
		$planSetupFeeGrossComposite      = MM_WPFS_Utils::calculateGrossFromNet( $stripePlanSetupFee, $vatPercent );
		$planAmountGrossTotalComposite   = MM_WPFS_Utils::calculateGrossFromNet( $stripePlanQuantity * $stripePlanAmount, $vatPercent );
		$planSetupFeeGrossTotalComposite = MM_WPFS_Utils::calculateGrossFromNet( $stripePlanQuantity * $stripePlanSetupFee, $vatPercent );

		$transactionData = new MM_WPFS_SubscriptionTransactionData();

		$transactionData->setFormName( $formName );
		$transactionData->setStripeToken( $stripeToken );
		$transactionData->setStripeCustomerId( $stripeCustomerId );
		$transactionData->setCustomerEmail( $customerEmail );
		$transactionData->setCustomerPhone( $customerPhone );
		$transactionData->setCustomerName( $billingName );
		$transactionData->setPlanId( $stripePlanId );
		$transactionData->setPlanName( $stripePlanName );
		$transactionData->setPlanCurrency( $stripePlanCurrency );
		$transactionData->setPlanNetSetupFee( $stripePlanSetupFee );
		$transactionData->setPlanGrossSetupFee( $planSetupFeeGrossComposite['gross'] );
		$transactionData->setPlanSetupFeeVAT( $transactionData->getPlanGrossSetupFee() - $transactionData->getPlanNetSetupFee() );
		$transactionData->setPlanSetupFeeVATRate( $vatPercent );
		$transactionData->setPlanNetSetupFeeTotal( $planSetupFeeGrossTotalComposite['net'] );
		$transactionData->setPlanGrossSetupFeeTotal( $planSetupFeeGrossTotalComposite['gross'] );
		$transactionData->setPlanSetupFeeVATTotal( $planSetupFeeGrossTotalComposite['taxValue'] );
		$transactionData->setPlanNetAmount( $stripePlanAmount );
		$transactionData->setPlanGrossAmount( $planAmountGrossComposite['gross'] );
		$transactionData->setPlanAmountVAT( $transactionData->getPlanGrossAmount() - $transactionData->getPlanNetAmount() );
		$transactionData->setPlanAmountVATRate( $vatPercent );
		$transactionData->setPlanQuantity( $stripePlanQuantity );
		$transactionData->setPlanNetAmountTotal( $planAmountGrossTotalComposite['net'] );
		$transactionData->setPlanGrossAmountTotal( $planAmountGrossTotalComposite['gross'] );
		$transactionData->setPlanAmountVATTotal( $planAmountGrossTotalComposite['taxValue'] );
		$transactionData->setProductName( $productName );
		$transactionData->setBillingName( $billingName );
		$transactionData->setBillingAddress( $billingAddress );
		$transactionData->setShippingName( $shippingName );
		$transactionData->setShippingAddress( $shippingAddress );
		$transactionData->setCustomInputValues( $customInputValues );
		$transactionData->setSubscriptionDescription( $subscriptionDescription );
		$transactionData->setCouponCode( $couponCode );
		$transactionData->setMetadata( $metadata );
		$transactionData->setBillingCycleAnchorDay( $subscriptionAnchorDay );
		$transactionData->setProrateUntilAnchorDay( $prorateUntilAnchorDay );

		return $transactionData;
	}

	/**
	 * Store transaction data as a transient.
	 *
	 * @param $data MM_WPFS_TransactionData
	 *
	 * @return null|string
	 */
	public function store( $data ) {
		$key = $this->generateKey();
		set_transient( $key, $data );

		return rawurlencode( $key );
	}

	/**
	 * Generates a random key currently not in use as a transient key.
	 */
	private function generateKey() {
		$key = null;
		do {
			$key = self::KEY_PREFIX . crypt( strval( round( microtime( true ) * 1000 ) ), strval( rand() ) );
		} while ( get_transient( $key ) !== false );

		return $key;
	}

	/**
	 * @param $data_key
	 *
	 * @return bool|MM_WPFS_TransactionData
	 */
	public function retrieve( $data_key ) {
		if ( is_null( $data_key ) ) {
			return false;
		}
		$prefix_position = strpos( $data_key, self::KEY_PREFIX );
		if ( $prefix_position === false ) {
			return false;
		}
		if ( $prefix_position == 0 ) {
			$data = get_transient( $data_key );

			if ( $data !== false ) {
				delete_transient( $data_key );
			}

			return $data;
		} else {
			return false;
		}
	}

}

abstract class MM_WPFS_TransactionData {

	protected $formName;
	protected $stripeCustomerId;
	protected $customerName;
	protected $customerEmail;
	protected $customerPhone;
	protected $billingName;
	/**
	 * @var array|null
	 */
	protected $billingAddress;
	protected $shippingName;
	/**
	 * @var array|null
	 */
	protected $shippingAddress;
	protected $productName;
	protected $customInputValues;
	protected $couponCode;
	/**
	 * @deprecated
	 */
	protected $stripeToken;
	protected $stripePaymentMethodId;
	protected $stripePaymentIntentId;

	/**
	 * @var array|null
	 */
	protected $metadata;
	protected $transactionId;
	protected $invoiceUrl;

	/**
	 * @return mixed
	 */
	public function getInvoiceUrl() {
		return $this->invoiceUrl;
	}

	/**
	 * @param mixed $invoiceUrl
	 */
	public function setInvoiceUrl( $invoiceUrl ) {
		$this->invoiceUrl = $invoiceUrl;
	}

	/**
	 * @return mixed
	 */
	public function getFormName() {
		return $this->formName;
	}

	/**
	 * @param mixed $formName
	 */
	public function setFormName( $formName ) {
		$this->formName = $formName;
	}

	/**
	 * @return mixed
	 */
	public function getStripeCustomerId() {
		return $this->stripeCustomerId;
	}

	/**
	 * @param mixed $stripeCustomerId
	 */
	public function setStripeCustomerId( $stripeCustomerId ) {
		$this->stripeCustomerId = $stripeCustomerId;
	}

	/**
	 * @return mixed
	 */
	public function getCustomerName() {
		return $this->customerName;
	}

	/**
	 * @param mixed $customerName
	 */
	public function setCustomerName( $customerName ) {
		$this->customerName = $customerName;
	}

	/**
	 * @return mixed
	 */
	public function getCustomerEmail() {
		return $this->customerEmail;
	}

	/**
	 * @param mixed $customerEmail
	 */
	public function setCustomerEmail( $customerEmail ) {
		$this->customerEmail = $customerEmail;
	}

	/**
	 * @return mixed
	 */
	public function getCustomerPhone() {
		return $this->customerPhone;
	}

	/**
	 * @param mixed $customerPhone
	 */
	public function setCustomerPhone( $customerPhone ) {
		$this->customerPhone = $customerPhone;
	}

	/**
	 * @return mixed
	 */
	public function getBillingName() {
		return $this->billingName;
	}

	/**
	 * @param mixed $billingName
	 */
	public function setBillingName( $billingName ) {
		$this->billingName = $billingName;
	}

	/**
	 * @return mixed
	 */
	public function getBillingAddress() {
		return $this->billingAddress;
	}

	/**
	 * @param array|null $billingAddress
	 */
	public function setBillingAddress( $billingAddress ) {
		$this->billingAddress = $billingAddress;
	}

	/**
	 * @return mixed
	 */
	public function getShippingName() {
		return $this->shippingName;
	}

	/**
	 * @param mixed $shippingName
	 */
	public function setShippingName( $shippingName ) {
		$this->shippingName = $shippingName;
	}

	/**
	 * @return mixed
	 */
	public function getShippingAddress() {
		return $this->shippingAddress;
	}

	/**
	 * @param mixed $shippingAddress
	 */
	public function setShippingAddress( $shippingAddress ) {
		$this->shippingAddress = $shippingAddress;
	}

	/**
	 * @return mixed
	 */
	public function getProductName() {
		return $this->productName;
	}

	/**
	 * @param mixed $productName
	 */
	public function setProductName( $productName ) {
		$this->productName = $productName;
	}

	/**
	 * @return mixed
	 */
	public function getCustomInputValues() {
		return $this->customInputValues;
	}

	/**
	 * @param mixed $customInputValues
	 */
	public function setCustomInputValues( $customInputValues ) {
		$this->customInputValues = $customInputValues;
	}

	/**
	 * @return mixed
	 */
	public function getCouponCode() {
		return $this->couponCode;
	}

	/**
	 * @param mixed $couponCode
	 */
	public function setCouponCode( $couponCode ) {
		$this->couponCode = $couponCode;
	}

	/**
	 * @return mixed
	 */
	public function getStripeToken() {
		return $this->stripeToken;
	}

	/**
	 * @param mixed $stripeToken
	 */
	public function setStripeToken( $stripeToken ) {
		$this->stripeToken = $stripeToken;
	}

	/**
	 * @return mixed
	 */
	public function getStripePaymentMethodId() {
		return $this->stripePaymentMethodId;
	}

	/**
	 * @param mixed $stripePaymentMethodId
	 */
	public function setStripePaymentMethodId( $stripePaymentMethodId ) {
		$this->stripePaymentMethodId = $stripePaymentMethodId;
	}

	/**
	 * @return mixed
	 */
	public function getStripePaymentIntentId() {
		return $this->stripePaymentIntentId;
	}

	/**
	 * @param mixed $stripePaymentIntentId
	 */
	public function setStripePaymentIntentId( $stripePaymentIntentId ) {
		$this->stripePaymentIntentId = $stripePaymentIntentId;
	}

	/**
	 * @return mixed
	 */
	public function getMetadata() {
		return $this->metadata;
	}

	/**
	 * @param mixed $metadata
	 */
	public function setMetadata( $metadata ) {
		$this->metadata = $metadata;
	}

	/**
	 * @return mixed
	 */
	public function getTransactionId() {
		return $this->transactionId;
	}

	/**
	 * @param mixed $transactionId
	 */
	public function setTransactionId( $transactionId ) {
		$this->transactionId = $transactionId;
	}
}


class MM_WPFS_DonationTransactionData extends MM_WPFS_TransactionData {
    protected $currency;
    protected $amount;

    /**
     * @return mixed
     */
    public function getCurrency() {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency( $currency ) {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount( $amount ) {
        $this->amount = $amount;
    }
}

class MM_WPFS_PaymentTransactionData extends MM_WPFS_TransactionData {

	protected $currency;
	protected $amount;
	protected $vatPercent;

	/**
	 * @return mixed
	 */
	public function getCurrency() {
		return $this->currency;
	}

	/**
	 * @param mixed $currency
	 */
	public function setCurrency( $currency ) {
		$this->currency = $currency;
	}

	/**
	 * @return mixed
	 */
	public function getAmount() {
		return $this->amount;
	}

	/**
	 * @param mixed $amount
	 */
	public function setAmount( $amount ) {
		$this->amount = $amount;
	}

	/**
	 * @return mixed
	 */
	public function getVatPercent() {
		return $this->vatPercent;
	}

	/**
	 * @param mixed $vatPercent
	 */
	public function setVatPercent( $vatPercent ) {
		$this->vatPercent = $vatPercent;
	}

}


class MM_WPFS_SubscriptionTransactionData extends MM_WPFS_TransactionData {

	protected $planId;
	protected $planName;
	protected $planCurrency;
	protected $planNetAmount;
	protected $planGrossAmount;
	protected $planAmountVAT;
	protected $planAmountVATRate;
	protected $planNetAmountTotal;
	protected $planGrossAmountTotal;
	protected $planAmountVATTotal;
	protected $planNetSetupFee;
	protected $planGrossSetupFee;
	protected $planSetupFeeVAT;
	protected $planSetupFeeVATRate;
	protected $planNetSetupFeeTotal;
	protected $planGrossSetupFeeTotal;
	protected $planSetupFeeVATTotal;
	protected $planQuantity;
	protected $subscriptionDescription;
	protected $billingCycleAnchorDay;
	protected $prorateUntilAnchorDay;

	/**
	 * @return integer
	 */
	public function getProrateUntilAnchorDay() {
		return $this->prorateUntilAnchorDay;
	}

	/**
	 * @param integer $prorateUntilAnchorDay
	 */
	public function setProrateUntilAnchorDay( $prorateUntilAnchorDay ) {
		$this->prorateUntilAnchorDay = $prorateUntilAnchorDay;
	}

	/**
	 * @return integer
	 */
	public function getBillingCycleAnchorDay() {
		return $this->billingCycleAnchorDay;
	}

	/**
	 * @param integer $billingCycleAnchorDay
	 */
	public function setBillingCycleAnchorDay( $billingCycleAnchorDay ) {
		$this->billingCycleAnchorDay = $billingCycleAnchorDay;
	}


	public function getPlanGrossAmountAndGrossSetupFeeTotal() {
		return $this->planGrossAmountTotal + $this->planGrossSetupFeeTotal;
	}

	/**
	 * @return mixed
	 */
	public function getPlanNetAmountTotal() {
		return $this->planNetAmountTotal;
	}

	/**
	 * @param mixed $planNetAmountTotal
	 */
	public function setPlanNetAmountTotal( $planNetAmountTotal ) {
		$this->planNetAmountTotal = $planNetAmountTotal;
	}

	/**
	 * @return mixed
	 */
	public function getPlanGrossAmountTotal() {
		return $this->planGrossAmountTotal;
	}

	/**
	 * @param mixed $planGrossAmountTotal
	 */
	public function setPlanGrossAmountTotal( $planGrossAmountTotal ) {
		$this->planGrossAmountTotal = $planGrossAmountTotal;
	}

	/**
	 * @return mixed
	 */
	public function getPlanAmountVATTotal() {
		return $this->planAmountVATTotal;
	}

	/**
	 * @param mixed $planAmountVATTotal
	 */
	public function setPlanAmountVATTotal( $planAmountVATTotal ) {
		$this->planAmountVATTotal = $planAmountVATTotal;
	}

	/**
	 * @return mixed
	 */
	public function getPlanNetSetupFeeTotal() {
		return $this->planNetSetupFeeTotal;
	}

	/**
	 * @param mixed $planNetSetupFeeTotal
	 */
	public function setPlanNetSetupFeeTotal( $planNetSetupFeeTotal ) {
		$this->planNetSetupFeeTotal = $planNetSetupFeeTotal;
	}

	/**
	 * @return mixed
	 */
	public function getPlanGrossSetupFeeTotal() {
		return $this->planGrossSetupFeeTotal;
	}

	/**
	 * @param mixed $planGrossSetupFeeTotal
	 */
	public function setPlanGrossSetupFeeTotal( $planGrossSetupFeeTotal ) {
		$this->planGrossSetupFeeTotal = $planGrossSetupFeeTotal;
	}

	/**
	 * @return mixed
	 */
	public function getPlanSetupFeeVATTotal() {
		return $this->planSetupFeeVATTotal;
	}

	/**
	 * @param mixed $planSetupFeeVATTotal
	 */
	public function setPlanSetupFeeVATTotal( $planSetupFeeVATTotal ) {
		$this->planSetupFeeVATTotal = $planSetupFeeVATTotal;
	}

	/**
	 * @return mixed
	 */
	public function getPlanId() {
		return $this->planId;
	}

	/**
	 * @param mixed $planId
	 */
	public function setPlanId( $planId ) {
		$this->planId = $planId;
	}

	/**
	 * @return mixed
	 */
	public function getPlanName() {
		return $this->planName;
	}

	/**
	 * @param mixed $planName
	 */
	public function setPlanName( $planName ) {
		$this->planName = $planName;
	}

	/**
	 * @return mixed
	 */
	public function getPlanCurrency() {
		return $this->planCurrency;
	}

	/**
	 * @param mixed $planCurrency
	 */
	public function setPlanCurrency( $planCurrency ) {
		$this->planCurrency = $planCurrency;
	}

	/**
	 * @return mixed
	 */
	public function getPlanNetAmount() {
		return $this->planNetAmount;
	}

	/**
	 * @param mixed $planNetAmount
	 */
	public function setPlanNetAmount( $planNetAmount ) {
		$this->planNetAmount = $planNetAmount;
	}

	/**
	 * @return mixed
	 */
	public function getPlanGrossAmount() {
		return $this->planGrossAmount;
	}

	/**
	 * @param mixed $planGrossAmount
	 */
	public function setPlanGrossAmount( $planGrossAmount ) {
		$this->planGrossAmount = $planGrossAmount;
	}

	/**
	 * @return mixed
	 */
	public function getPlanAmountVAT() {
		return $this->planAmountVAT;
	}

	/**
	 * @param mixed $planAmountVAT
	 */
	public function setPlanAmountVAT( $planAmountVAT ) {
		$this->planAmountVAT = $planAmountVAT;
	}

	/**
	 * @return mixed
	 */
	public function getPlanAmountVATRate() {
		return $this->planAmountVATRate;
	}

	/**
	 * @param mixed $planAmountVATRate
	 */
	public function setPlanAmountVATRate( $planAmountVATRate ) {
		$this->planAmountVATRate = $planAmountVATRate;
	}

	/**
	 * @return mixed
	 */
	public function getPlanNetSetupFee() {
		return $this->planNetSetupFee;
	}

	/**
	 * @param mixed $planNetSetupFee
	 */
	public function setPlanNetSetupFee( $planNetSetupFee ) {
		$this->planNetSetupFee = $planNetSetupFee;
	}

	/**
	 * @return mixed
	 */
	public function getPlanGrossSetupFee() {
		return $this->planGrossSetupFee;
	}

	/**
	 * @param mixed $planGrossSetupFee
	 */
	public function setPlanGrossSetupFee( $planGrossSetupFee ) {
		$this->planGrossSetupFee = $planGrossSetupFee;
	}

	/**
	 * @return mixed
	 */
	public function getPlanSetupFeeVAT() {
		return $this->planSetupFeeVAT;
	}

	/**
	 * @param mixed $planSetupFeeVAT
	 */
	public function setPlanSetupFeeVAT( $planSetupFeeVAT ) {
		$this->planSetupFeeVAT = $planSetupFeeVAT;
	}

	/**
	 * @return mixed
	 */
	public function getPlanSetupFeeVATRate() {
		return $this->planSetupFeeVATRate;
	}

	/**
	 * @param mixed $planSetupFeeVATRate
	 */
	public function setPlanSetupFeeVATRate( $planSetupFeeVATRate ) {
		$this->planSetupFeeVATRate = $planSetupFeeVATRate;
	}

	/**
	 * @return mixed
	 */
	public function getPlanQuantity() {
		return $this->planQuantity;
	}

	/**
	 * @param mixed $planQuantity
	 */
	public function setPlanQuantity( $planQuantity ) {
		$this->planQuantity = $planQuantity;
	}

	/**
	 * @return mixed
	 */
	public function getSubscriptionDescription() {
		return $this->subscriptionDescription;
	}

	/**
	 * @param mixed $subscriptionDescription
	 */
	public function setSubscriptionDescription( $subscriptionDescription ) {
		$this->subscriptionDescription = $subscriptionDescription;
	}

}