<?php

/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2016.02.26.
 * Time: 14:16
 */
class MM_WPFS_Mailer {

	const TEMPLATE_TYPE_PAYMENT_RECEIPT = "PaymentReceipt";
    const TEMPLATE_TYPE_DONATION_RECEIPT = "DonationReceipt";
	const TEMPLATE_TYPE_SUBSCRIPTION_RECEIPT = "SubscriptionReceipt";
	const TEMPLATE_TYPE_SUBSCRIPTION_ENDED = "SubscriptionEnded";
	const TEMPLATE_TYPE_CARD_SAVED = "CardSaved";
	const TEMPLATE_TYPE_MANAGE_SUBSCRIPTIONS_SECURITY_CODE = "ManageSubscriptionsSecurityCode";

	/**
	 * @param MM_WPFS_Public_PaymentFormModel $paymentFormModel
	 */
	public function sendPaymentEmailReceipt( $paymentFormModel ) {
		$this->send_payment_email_receipt(
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

    /**
     * @param MM_WPFS_Public_DonationFormModel $donationFormModel
     * @param MM_WPFS_DonationTransactionData $transactionData
     */
    public function sendDonationEmailReceipt( $donationFormModel, $transactionData ) {
        if ( MM_WPFS_Utils::isDemoMode() ) {
            return;
        }

        $options       = get_option( 'fullstripe_options' );
        $emailReceipts = json_decode( $options['email_receipts'] );
        $subject       = $emailReceipts->donationMade->subject;
        $message       = stripslashes( $emailReceipts->donationMade->html );

        $replacer = new DonationMacroReplacer( $donationFormModel->getForm(), $transactionData );
        $subject = $replacer->replaceMacrosWithHtmlEscape( $subject );
        $message = $replacer->replaceMacrosWithHtmlEscape( $message );

        $this->send_email( $transactionData->getCustomerEmail(), $subject, $message, self::TEMPLATE_TYPE_DONATION_RECEIPT, $transactionData->getFormName() );
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
	 * @param $customInputValues
	 * @param $formName
	 * @param $transactionId
	 */
	public function send_payment_email_receipt( $form, $customerName, $email, $currency, $amount, $billingName, $billingAddress, $shippingName, $shippingAddress, $productName, $customInputValues, $formName, $transactionId ) {

		if ( MM_WPFS_Utils::isDemoMode() ) {
			return;
		}

		$options       = get_option( 'fullstripe_options' );
		$emailReceipts = json_decode( $options['email_receipts'] );
		$subject       = $emailReceipts->paymentMade->subject;
		$message       = stripslashes( $emailReceipts->paymentMade->html );

		$search  = MM_WPFS_Utils::get_payment_macros();
		$replace = MM_WPFS_Utils::get_payment_macro_values(
			$form,
			$customerName,
			$email,
			$currency,
			$amount,
			$billingName,
			$billingAddress,
			$shippingName,
			$shippingAddress,
			$productName,
			$formName,
			$transactionId
		);
		$message = str_replace(
			$search,
			$replace,
			$message );

		$message = MM_WPFS_Utils::replace_custom_fields( $message, $customInputValues );

		$this->send_email( $email, $subject, $message, self::TEMPLATE_TYPE_PAYMENT_RECEIPT, $formName );
	}

	public function send_email( $email, $subject, $message, $templateType, $formName ) {
		$options = get_option( 'fullstripe_options' );

		$name = html_entity_decode( get_bloginfo( 'name' ) );

		$admin_email  = get_bloginfo( 'admin_email' );
		$sender_email = $admin_email;
		if ( isset( $options['email_receipt_sender_address'] ) && ! empty( $options['email_receipt_sender_address'] ) ) {
			$sender_email = $options['email_receipt_sender_address'];
		}
		$headers[] = "From: $name <$sender_email>";

		$headers[] = "Content-type: text/html";

		wp_mail( $email,
			apply_filters( MM_WPFS::FILTER_NAME_MODIFY_EMAIL_SUBJECT, $subject, $templateType, $formName ),
			apply_filters( MM_WPFS::FILTER_NAME_MODIFY_EMAIL_MESSAGE, $message, $templateType, $formName ),
			apply_filters( 'fullstripe_email_headers_filter', $headers ) );

		if ( $options['admin_payment_receipt'] == 'website_admin' || $options['admin_payment_receipt'] == 'sender_address' ) {
			$receipt_to = $admin_email;
			if ( $options['admin_payment_receipt'] == 'sender_address' && isset( $options['email_receipt_sender_address'] ) && ! empty( $options['email_receipt_sender_address'] ) ) {
				$receipt_to = $options['email_receipt_sender_address'];
			}
			wp_mail( $receipt_to,
				"COPY: " . apply_filters( MM_WPFS::FILTER_NAME_MODIFY_EMAIL_SUBJECT, $subject, $templateType, $formName ),
				apply_filters( MM_WPFS::FILTER_NAME_MODIFY_EMAIL_MESSAGE, $message, $templateType, $formName ),
				apply_filters( 'fullstripe_email_headers_filter', $headers ) );
		}
	}

	/**
	 * @param MM_WPFS_Public_PaymentFormModel $paymentFormModel
	 */
	public function sendCardCapturedEmailReceipt( $paymentFormModel ) {
		$this->send_card_captured_email_receipt(
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

	/**
	 * @param $form
	 * @param $customerName
	 * @param $email
	 * @param $billingName
	 * @param $billingAddress
	 * @param $shippingName
	 * @param $shippingAddress
	 * @param $productName
	 * @param $custom_input_values
	 * @param $form_name
	 * @param $transaction_id
	 */
	public function send_card_captured_email_receipt( $form, $customerName, $email, $billingName, $billingAddress, $shippingName, $shippingAddress, $productName, $custom_input_values, $form_name, $transaction_id ) {

		if ( MM_WPFS_Utils::isDemoMode() ) {
			return;
		}

		$options       = get_option( 'fullstripe_options' );
		$emailReceipts = json_decode( $options['email_receipts'] );
		$subject       = $emailReceipts->cardCaptured->subject;
		$message       = stripslashes( $emailReceipts->cardCaptured->html );

		$search  = MM_WPFS_Utils::get_save_card_macros();
		$replace = MM_WPFS_Utils::get_save_card_macro_values(
			$form,
			$customerName,
			$email,
			$billingName,
			$billingAddress,
			$shippingName,
			$shippingAddress,
			$form_name,
			$transaction_id
		);
		$message = str_replace(
			$search,
			$replace,
			$message );

		$message = MM_WPFS_Utils::replace_custom_fields( $message, $custom_input_values );

		$this->send_email( $email, $subject, $message, self::TEMPLATE_TYPE_CARD_SAVED, $form_name );
	}

	/**
	 * @param $form
	 * @param MM_WPFS_SubscriptionTransactionData $transactionData
	 */
	public function sendSubscriptionStartedEmailReceipt( $form, $transactionData ) {
		$this->send_subscription_started_email_receipt(
			$form,
			$transactionData->getCustomerEmail(),
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
			$transactionData->getCustomerName(),
			$transactionData->getBillingName(),
			$transactionData->getBillingAddress(),
			$transactionData->getShippingName(),
			$transactionData->getShippingAddress(),
			$transactionData->getProductName(),
			$transactionData->getTransactionId(),
			$transactionData->getFormName(),
			$transactionData->getInvoiceUrl(),
			$transactionData->getCustomInputValues()
		);
	}

	/**
	 * @deprecated
	 *
	 * @param $form
	 * @param $customerEmail
	 * @param $planName
	 * @param $planCurrency
	 * @param $planNetSetupFee
	 * @param $planGrossSetupFee
	 * @param $planSetupFeeVAT
	 * @param $planSetupFeeVATRate
	 * @param $planNetSetupFeeTotal
	 * @param $planGrossSetupFeeTotal
	 * @param $planSetupFeeVATTotal
	 * @param $planNetAmount
	 * @param $planGrossAmount
	 * @param $planAmountVAT
	 * @param $planAmountVATRate
	 * @param $planQuantity
	 * @param $planNetAmountTotal
	 * @param $planGrossAmountTotal
	 * @param $planAmountVATTotal
	 * @param $grossAmountAndGrossSetupFee
	 * @param $customerName
	 * @param $billingName
	 * @param $billingAddress
	 * @param $shippingName
	 * @param $shippingAddress
	 * @param $productName
	 * @param $transactionId
	 * @param $formName
	 * @param $invoiceUrl
	 * @param null $customInputValues
	 */
	public function send_subscription_started_email_receipt(
		$form,
		$customerEmail,
		$planName, $planCurrency,
		$planNetSetupFee, $planGrossSetupFee, $planSetupFeeVAT, $planSetupFeeVATRate,
		$planNetSetupFeeTotal, $planGrossSetupFeeTotal, $planSetupFeeVATTotal,
		$planNetAmount, $planGrossAmount, $planAmountVAT, $planAmountVATRate,
		$planQuantity,
		$planNetAmountTotal, $planGrossAmountTotal, $planAmountVATTotal,
		$grossAmountAndGrossSetupFee,
		$customerName, $billingName, $billingAddress, $shippingName, $shippingAddress,
		$productName,
		$transactionId,
		$formName,
		$invoiceUrl,
		$customInputValues = null
	) {
		if ( MM_WPFS_Utils::isDemoMode() ) {
			return;
		}

		$options       = get_option( 'fullstripe_options' );
		$emailReceipts = json_decode( $options['email_receipts'] );
		$subject       = $emailReceipts->subscriptionStarted->subject;
		$message       = stripslashes( $emailReceipts->subscriptionStarted->html );

		$search  = MM_WPFS_Utils::get_subscription_macros();
		$replace = MM_WPFS_Utils::get_subscription_macro_values(
			$form,
			$customerName,
			$customerEmail,
			$billingName,
			$billingAddress,
			$shippingName,
			$shippingAddress,
			$planName,
			$planCurrency,
			$planNetSetupFee,
			$planGrossSetupFee,
			$planSetupFeeVAT,
			$planSetupFeeVATRate,
			$planNetSetupFeeTotal,
			$planGrossSetupFeeTotal,
			$planSetupFeeVATTotal,
			$planNetAmount,
			$planGrossAmount,
			$planAmountVAT,
			$planAmountVATRate,
			$planQuantity,
			$planNetAmountTotal,
			$planGrossAmountTotal,
			$planAmountVATTotal,
			$grossAmountAndGrossSetupFee,
			$productName,
			$transactionId,
			$formName,
			$invoiceUrl
		);
		$message = str_replace(
			$search,
			$replace,
			$message
		);

		$message = MM_WPFS_Utils::replace_custom_fields( $message, $customInputValues );

		$this->send_email( $customerEmail, $subject, $message, self::TEMPLATE_TYPE_SUBSCRIPTION_RECEIPT, $formName );
	}

	/**
	 * @param $form
	 * @param MM_WPFS_SubscriptionTransactionData $transactionData
	 */
	public function sendSubscriptionFinishedEmailReceipt( $form, $transactionData ) {
		$this->send_subscription_finished_email_receipt(
			$form,
			$transactionData->getCustomerEmail(),
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
			$transactionData->getCustomerName(),
			$transactionData->getBillingName(),
			$transactionData->getBillingAddress(),
			$transactionData->getShippingName(),
			$transactionData->getShippingAddress(),
			$transactionData->getProductName(),
			$transactionData->getTransactionId(),
			$transactionData->getFormName(),
			$transactionData->getCustomInputValues()
		);
	}

	/**
	 * @deprecated
	 *
	 * @param $form
	 * @param $customerEmail
	 * @param $planName
	 * @param $planCurrency
	 * @param $planNetSetupFee
	 * @param $planGrossSetupFee
	 * @param $planSetupFeeVAT
	 * @param $planSetupFeeVATRate
	 * @param $planNetSetupFeeTotal
	 * @param $planGrossSetupFeeTotal
	 * @param $planSetupFeeVATTotal
	 * @param $planNetAmount
	 * @param $planGrossAmount
	 * @param $planAmountVAT
	 * @param $planAmountVATRate
	 * @param $planQuantity
	 * @param $planAmountNetTotal
	 * @param $planAmountGrossTotal
	 * @param $planAmountVATTotal
	 * @param $grossAmountAndGrossSetupFee
	 * @param $customerName
	 * @param $billingName
	 * @param $billingAddress
	 * @param $shippingName
	 * @param $shippingAddress
	 * @param $productName
	 * @param $transactionId
	 * @param $formName
	 * @param $customInputValues
	 */
	public function send_subscription_finished_email_receipt(
		$form,
		$customerEmail,
		$planName, $planCurrency,
		$planNetSetupFee, $planGrossSetupFee, $planSetupFeeVAT, $planSetupFeeVATRate,
		$planNetSetupFeeTotal, $planGrossSetupFeeTotal, $planSetupFeeVATTotal,
		$planNetAmount, $planGrossAmount, $planAmountVAT, $planAmountVATRate,
		$planQuantity,
		$planAmountNetTotal, $planAmountGrossTotal, $planAmountVATTotal,
		$grossAmountAndGrossSetupFee,
		$customerName, $billingName, $billingAddress, $shippingName, $shippingAddress,
		$productName,
		$transactionId,
		$formName,
		$customInputValues = null
	) {
		if ( MM_WPFS_Utils::isDemoMode() ) {
			return;
		}

		$options       = get_option( 'fullstripe_options' );
		$emailReceipts = json_decode( $options['email_receipts'] );
		$subject       = $emailReceipts->subscriptionFinished->subject;
		$message       = stripslashes( $emailReceipts->subscriptionFinished->html );

		$search  = MM_WPFS_Utils::get_subscription_macros();
		$replace = MM_WPFS_Utils::get_subscription_macro_values(
			$form,
			$customerName,
			$customerEmail,
			$billingName,
			$billingAddress,
			$shippingName,
			$shippingAddress,
			$planName,
			$planCurrency,
			$planNetSetupFee,
			$planGrossSetupFee,
			$planSetupFeeVAT,
			$planSetupFeeVATRate,
			$planNetSetupFeeTotal,
			$planGrossSetupFeeTotal,
			$planSetupFeeVATTotal,
			$planNetAmount,
			$planGrossAmount,
			$planAmountVAT,
			$planAmountVATRate,
			$planQuantity,
			$planAmountNetTotal,
			$planAmountGrossTotal,
			$planAmountVATTotal,
			$grossAmountAndGrossSetupFee,
			$productName,
			$transactionId,
			$formName,
			'' // No invoice URL for subscription ended emails
		);
		$message = str_replace(
			$search,
			$replace,
			$message
		);

		$message = MM_WPFS_Utils::replace_custom_fields( $message, $customInputValues );

		$this->send_email( $customerEmail, $subject, $message, self::TEMPLATE_TYPE_SUBSCRIPTION_ENDED, $formName );
	}

	public function send_card_update_confirmation_request( $customerName, $customerEmail, $cardUpdateSessionHash, $securityCode ) {
		if ( MM_WPFS_Utils::isDemoMode() ) {
			return;
		}

		$options       = get_option( 'fullstripe_options' );
		$emailReceipts = json_decode( $options['email_receipts'] );
		$subject       = $emailReceipts->cardUpdateConfirmationRequest->subject;
		$message       = stripslashes( $emailReceipts->cardUpdateConfirmationRequest->html );

		$search  = MM_WPFS_Utils::get_card_update_confirmation_request_macros();
		$replace = MM_WPFS_Utils::get_card_update_confirmation_request_macro_values( $customerName, $customerEmail, $cardUpdateSessionHash, $securityCode );

		$message = str_replace(
			$search,
			$replace,
			$message
		);

		$this->send_email( $customerEmail, $subject, $message, self::TEMPLATE_TYPE_MANAGE_SUBSCRIPTIONS_SECURITY_CODE, null );
	}

}