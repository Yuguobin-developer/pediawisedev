<?php

/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2016.03.01.
 * Time: 14:43
 */
class MM_WPFS_Patcher {

	private static $debugLog = false;

	public static function apply_patches() {

		if ( self::$debugLog ) {
			MM_WPFS_Utils::log( 'WPFS INFO apply_patches(): Apply patches...' );
		}

		$patches         = self::prepare_patches();
		$applied_patches = self::load_applied_patches();

		foreach ( $patches as $patch ) {
			/* @var $patch MM_WPFS_Patch */
			$apply = false;
			if ( array_key_exists( $patch->getId(), $applied_patches ) ) {
				if ( $patch->isRepeatable() ) {
					$apply = true;
				}
			} else {
				$apply = true;
			}
			if ( $apply ) {

				try {

					if ( self::$debugLog ) {
						MM_WPFS_Utils::log( 'WPFS INFO apply_patches(): Applying ' . $patch->getId() . '...' );
					}

					$result = $patch->apply();

					if ( $result ) {

						self::book_applied( $patch );

						if ( self::$debugLog ) {
							MM_WPFS_Utils::log( 'WPFS INFO apply_patches(): ' . $patch->getId() . ' applied successfully.' );
						}
					} else {
						if ( self::$debugLog ) {
							MM_WPFS_Utils::log( 'WPFS ERROR apply_patches(): ' . $patch->getId() . ' failed!' );
						}
					}

				} catch ( Exception $e ) {
					MM_WPFS_Utils::log( sprintf( 'WPFS ERROR apply_patches(): Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() ) );
				}

			}
		}

		if ( self::$debugLog ) {
			MM_WPFS_Utils::log( 'WPFS INFO apply_patches(): Patches applied.' );
		}
	}

	/**
	 * @return array
	 */
	private static function prepare_patches() {

		$convert_subscription_form_plans                              = new MM_WPFS_ConvertSubscriptionFormPlansPatch();
		$convert_email_receipts                                       = new MM_WPFS_ConvertEmailReceiptsPatch();
		$convert_subscription_status                                  = new MM_WPFS_ConvertSubscriptionStatus();
		$set_current_currency_for_payments                            = new MM_WPFS_SetCurrentCurrencyForPayments();
		$set_list_of_amount_custom                                    = new MM_WPFS_SetAllowListOfAmountsCustom();
		$set_show_detailed_success_page                               = new MM_WPFS_SetShowDetailedSuccessPage();
		$set_custom_input_required                                    = new MM_WPFS_SetCustomInputRequired();
		$migrate_currency_and_setup_fee                               = new MM_WPFS_MigrateCurrencyAndSetupFee();
		$set_specified_amount_for_checkout_forms                      = new MM_WPFS_SetSpecifiedAmountForCheckoutForms();
		$drop_checkout_subscription_alipay_columns                    = new MM_WPFS_DropCheckoutSubscriptionAlipayColumns();
		$fix_email_receipts_json                                      = new MM_WPFS_FixEmailReceiptJSON();
		$set_vat_rate_type_for_subscription_forms                     = new MM_WPFS_SetInitialVATRateTypeForSubscriptionForms();
		$set_simple_button_layout                                     = new MM_WPFS_SetSimpleButtonLayout();
		$set_preferred_language_for_popup_forms                       = new MM_WPFS_SetPreferredLanguageForPopupForms();
		$set_stripe_description_payment_forms                         = new MM_WPFS_SetStripeDescriptionForPaymentForms();
		$set_charge_type                                              = new MM_WPFS_SetInitialChargeTypeForForms();
		$set_stripe_charge_flags_and_method_for_payments              = new MM_WPFS_SetStripeChargeFlagsAndMethodForPayments();
		$set_default_show_button_amount_for_popup_payment_forms       = new MM_WPFS_SetShowButtonAmountForPopupPaymentForms();
		$set_initial_amount_selector_style_for_payment_forms          = new MM_WPFS_SetInitialAmountSelectorStyleForPaymentForms();
		$set_initial_plan_selector_style_for_subscription_forms       = new MM_WPFS_SetInitialPlanSelectorStyleForSubscriptionForms();
		$convert_preferred_language_for_popup_forms                   = new MM_WPFS_ConvertPreferredLanguageForPopupForms();
		$set_initial_product_name_for_checkout_forms                  = new MM_WPFS_SetInitialProductNameForCheckoutForms();
		$set_initial_default_billing_country_for_inline_payment_forms = new MM_WPFS_SetInitialDefaultBillingCountryForInlinePaymentForms();
		$set_initial_quantities_for_subscription_forms                = new MM_WPFS_SetInitialQuantitiesForSubscriptionForms();
		$set_initial_decimal_separator_for_forms                      = new MM_WPFS_SetInitialDecimalSeparatorForForms();
        $set_donation_form_email_receipt                              = new MM_WPFS_SetDonationFormEmailReceipt();
        $set_currency_for_one_time_payments                           = new MM_WPFS_SetCurrencyForOneTimePayments();
        $set_currency_for_one_time_forms                              = new MM_WPFS_SetCurrencyForOneTimeForms();

		$patches = array(
			$convert_subscription_form_plans->getId()                              => $convert_subscription_form_plans,
			$convert_email_receipts->getId()                                       => $convert_email_receipts,
			$convert_subscription_status->getId()                                  => $convert_subscription_status,
			$set_current_currency_for_payments->getId()                            => $set_current_currency_for_payments,
			$set_list_of_amount_custom->getId()                                    => $set_list_of_amount_custom,
			$set_show_detailed_success_page->getId()                               => $set_show_detailed_success_page,
			$set_custom_input_required->getId()                                    => $set_custom_input_required,
			$migrate_currency_and_setup_fee->getId()                               => $migrate_currency_and_setup_fee,
			$drop_checkout_subscription_alipay_columns->getId()                    => $drop_checkout_subscription_alipay_columns,
			$set_specified_amount_for_checkout_forms->getId()                      => $set_specified_amount_for_checkout_forms,
			$fix_email_receipts_json->getId()                                      => $fix_email_receipts_json,
			$set_vat_rate_type_for_subscription_forms->getId()                     => $set_vat_rate_type_for_subscription_forms,
			$set_simple_button_layout->getId()                                     => $set_simple_button_layout,
			$set_preferred_language_for_popup_forms->getId()                       => $set_preferred_language_for_popup_forms,
			$set_stripe_description_payment_forms->getId()                         => $set_stripe_description_payment_forms,
			$set_charge_type->getId()                                              => $set_charge_type,
			$set_stripe_charge_flags_and_method_for_payments->getId()              => $set_stripe_charge_flags_and_method_for_payments,
			$set_default_show_button_amount_for_popup_payment_forms->getId()       => $set_default_show_button_amount_for_popup_payment_forms,
			$set_initial_amount_selector_style_for_payment_forms->getId()          => $set_initial_amount_selector_style_for_payment_forms,
			$set_initial_plan_selector_style_for_subscription_forms->getId()       => $set_initial_plan_selector_style_for_subscription_forms,
			$convert_preferred_language_for_popup_forms->getId()                   => $convert_preferred_language_for_popup_forms,
			$set_initial_product_name_for_checkout_forms->getId()                  => $set_initial_product_name_for_checkout_forms,
			$set_initial_default_billing_country_for_inline_payment_forms->getId() => $set_initial_default_billing_country_for_inline_payment_forms,
			$set_initial_quantities_for_subscription_forms->getId()                => $set_initial_quantities_for_subscription_forms,
			$set_initial_decimal_separator_for_forms->getId()                      => $set_initial_decimal_separator_for_forms,
            $set_donation_form_email_receipt->getId()                              => $set_donation_form_email_receipt,
            $set_currency_for_one_time_payments->getId()                           => $set_currency_for_one_time_payments,
            $set_currency_for_one_time_forms->getId()                              => $set_currency_for_one_time_forms
		);

		return $patches;
	}

	/**
	 * @return array
	 */
	private static function load_applied_patches() {
		global $wpdb;

		$result = $wpdb->get_results( "select id,patch_id,plugin_version,applied_at,description from {$wpdb->prefix}fullstripe_patch_info" );

		$applied_patches = array();

		foreach ( $result as $applied_patch ) {
			$applied_patches[ $applied_patch->patch_id ] = $applied_patch;
		}

		return $applied_patches;
	}

	private static function book_applied( $patch ) {

		if ( ! isset( $patch ) ) {
			return;
		}

		/* @var $patch MM_WPFS_Patch */

		global $wpdb;

		$data = array(
			'patch_id'       => $patch->getId(),
			'plugin_version' => $patch->getPluginVersion(),
			'applied_at'     => current_time( 'mysql', 1 ),
			'description'    => $patch->getDescription()
		);

		if ( $wpdb->insert( "{$wpdb->prefix}fullstripe_patch_info", $data ) === false ) {
			throw new Exception( 'Cannot insert patch_info: ' . $wpdb->last_error );
		}
	}

}

abstract class MM_WPFS_WPFSF_Patch extends MM_WPFS_Patch {
    /**
     * Returns the currency used in WP Full Stripe Free, or USD (if no configuration found).
     *
     * @return string payment currency
     */
    protected function getFullStripeFreeCurrency() {
        $logPrefix = __CLASS__ . __FUNCTION__ . '(): ';
        $res  = 'usd';

        $options = get_option( 'fullstripe_options_f' );
        if ( $options ) {
            if ( array_key_exists( 'currency', $options ) ) {
                $defaultCurrency = $options[ 'currency' ];

                $lookedUpCurrency = MM_WPFS_Currencies::get_currency_for( $defaultCurrency );
                if ( !is_null( $lookedUpCurrency ) ) {
                    $res = $defaultCurrency;
                    if ( $this->debugLog ) {
                        MM_WPFS_Utils::log( $logPrefix . " currency '" . $defaultCurrency .  "' found, and it's supported by WP Full Stripe." );
                    }
                } else {
                    if ( $this->debugLog ) {
                        MM_WPFS_Utils::log( $logPrefix . " currency '" . $defaultCurrency . "' is not supported by WP Full Stripe." );
                    }
                }
            } else {
                if ( $this->debugLog ) {
                    MM_WPFS_Utils::log( $logPrefix . " no 'currency' key found among the 'fullstripe_options_f' options." );
                }
            }
        } else {
            if ( $this->debugLog ) {
                MM_WPFS_Utils::log( $logPrefix . " no 'fullstripe_options_f' key found in the options table." );
            }
        }

        return $res;
    }
}

class MM_WPFS_SetCurrencyForOneTimeForms extends MM_WPFS_WPFSF_Patch {
    /**
     * MM_WPFS_SetCurrencyForOneTimeForms constructor.
     */
    public function __construct() {
        $this->id             = 'set_currency_for_one_time_forms';
        $this->plugin_version = '5.5.2';
        $this->description    = 'A patch for setting the payment currency for one-time payment forms made with WP Full Stripe Free. JIRA reference: WPFS-1274';
        $this->repeatable     = true;
    }

    private function updateCurrencyInDatabase( $currency ) {
        global $wpdb;

        // Let's start with rows where currency is null
        $nullUpdateResult = $wpdb->update(
            "{$wpdb->prefix}fullstripe_payment_forms",
            array(
                'currency' => $currency
            ),
            array( 'currency' => null )
        );

        // Make sure we update rows where currency is an empty string
        $emptyStringUpdateResult = $wpdb->update(
            "{$wpdb->prefix}fullstripe_payment_forms",
            array(
                'currency' => $currency
            ),
            array( 'currency' => '' )
        );
    }

    public function apply() {
        $logPrefix = __CLASS__ . __FUNCTION__ . '(): ';

        if ( $this->debugLog ) {
            MM_WPFS_Utils::log( $logPrefix . " started processing." );
        }

        $currency = $this->getFullStripeFreeCurrency();
        $this->updateCurrencyInDatabase( $currency );

        if ( $this->debugLog ) {
            MM_WPFS_Utils::log( $logPrefix . " finished processing." );
        }

        return true;
    }
}

class MM_WPFS_SetCurrencyForOneTimePayments extends MM_WPFS_WPFSF_Patch {
    /**
     * MM_WPFS_SetCurrencyForOneTimePayments constructor.
     */
    public function __construct() {
        $this->id             = 'set_currency_for_one_time_payments';
        $this->plugin_version = '5.5.2';
        $this->description    = 'A patch for setting the payment currency for one-time payments made with WP Full Stripe Free. JIRA reference: WPFS-1272';
        $this->repeatable     = true;
    }

    private function updateCurrencyInDatabase( $currency ) {
        global $wpdb;

        // Let's start with rows where currency is null
        $nullUpdateResult = $wpdb->update(
            "{$wpdb->prefix}fullstripe_payments",
            array(
                'currency' => $currency
            ),
            array( 'currency' => null )
        );

        // Make sure we update rows where currency is an empty string
        $emptyStringUpdateResult = $wpdb->update(
            "{$wpdb->prefix}fullstripe_payments",
            array(
                'currency' => $currency
            ),
            array( 'currency' => '' )
        );
    }

    public function apply() {
        $logPrefix = __CLASS__ . __FUNCTION__ . '(): ';

        if ( $this->debugLog ) {
            MM_WPFS_Utils::log( $logPrefix . " started processing." );
        }

        $currency = $this->getFullStripeFreeCurrency();
        $this->updateCurrencyInDatabase( $currency );

        if ( $this->debugLog ) {
            MM_WPFS_Utils::log( $logPrefix . " finished processing." );
        }

        return true;
    }
}


class MM_WPFS_SetDonationFormEmailReceipt extends MM_WPFS_Patch {
    /**
     * MM_WPFS_SetDonationFormEmailReceipt constructor.
     */
	public function __construct() {
        $this->id             = 'set_donation_form_email_receipt';
        $this->plugin_version = '5.5.0';
        $this->description    = 'A patch for setting the default email receipt for donation forms. JIRA reference: WPFS-1126';
        $this->repeatable     = true;
    }

    public function apply() {
        $options   = get_option( 'fullstripe_options' );
        $logPrefix = 'SetDonationFormEmailReceipt::apply():';

        if ( $this->debugLog ) {
            MM_WPFS_Utils::log( $logPrefix . ' current fullstripe_options=' . print_r( $options, true ) );
        }

        if ( is_array( $options ) ) {
            $createDefaultEmailReceiptOptions = false;
            $decodedEmailReceipts = null;

            if ( array_key_exists( 'email_receipts', $options ) ) {
                $encodedEmailReceipts = $options['email_receipts'];
                $decodedEmailReceipts = json_decode( $encodedEmailReceipts );

                if ( $decodedEmailReceipts == null || json_last_error() != JSON_ERROR_NONE  ) {
                    if ( $this->debugLog ) {
                        MM_WPFS_Utils::log( $logPrefix . ' Decoded email receipt templates JSON structure in fullstripe_options is invalid!' );
                    }
                    $createDefaultEmailReceiptOptions = true;
                } else {
                    if ( $this->debugLog ) {
                        MM_WPFS_Utils::log( $logPrefix . ' decodedEmailReceipts=' . print_r( $decodedEmailReceipts, true ) );
                    }
                }
            } else {
                if ( $this->debugLog ) {
                    MM_WPFS_Utils::log( $logPrefix . ' email-receipts key do not exist in fullstripe_options!' );
                }
                $createDefaultEmailReceiptOptions = true;
            }

            if ( $createDefaultEmailReceiptOptions ) {
                $decodedEmailReceipts = MM_WPFS_Utils::create_default_email_receipts();
                if ( $this->debugLog ) {
                    MM_WPFS_Utils::log( $logPrefix . ' Recreating default email templates.' );
                }
            } else {
                if ( $this->debugLog ) {
                    MM_WPFS_Utils::log( $logPrefix . ' Adding email template for donation receipt.' );
                }
                $decodedEmailReceipts->donationMade = MM_WPFS_Utils::create_default_donation_made_email_receipt();
            }

            $options['email_receipts'] = json_encode( $decodedEmailReceipts );
            update_option( 'fullstripe_options', $options );
        } else {
            if ( $this->debugLog ) {
                MM_WPFS_Utils::log( $logPrefix . ' WPFS options variable is not an array!' );
            }
        }

        return true;
    }
}


class MM_WPFS_SetInitialDecimalSeparatorForForms extends MM_WPFS_Patch {

	/**
	 * MM_WPFS_SetInitialDecimalSeparatorForForms constructor.
	 */
	public function __construct() {
		$this->id             = 'set_initial_decimal_separator_for_forms';
		$this->plugin_version = '5.5.0';
		$this->description    = 'A patch for setting initial value for currency decimal separator for forms. JIRA reference: WPFS-537';
		$this->repeatable     = true;
	}

	public function apply() {
		global $wpdb;

		$set_payment_form_initial_decimal_separator_result      = $wpdb->update(
			"{$wpdb->prefix}fullstripe_payment_forms",
			array( 'decimalSeparator' => MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT ),
			array( 'decimalSeparator' => '' )
		);
		$set_subscription_form_initial_decimal_separator_result      = $wpdb->update(
			"{$wpdb->prefix}fullstripe_subscription_forms",
			array( 'decimalSeparator' => MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT ),
			array( 'decimalSeparator' => '' )
		);
		$set_checkout_form_initial_decimal_separator_result      = $wpdb->update(
			"{$wpdb->prefix}fullstripe_checkout_forms",
			array( 'decimalSeparator' => MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT ),
			array( 'decimalSeparator' => '' )
		);
		$set_checkout_subscription_form_initial_decimal_separator_result      = $wpdb->update(
			"{$wpdb->prefix}fullstripe_checkout_subscription_forms",
			array( 'decimalSeparator' => MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT ),
			array( 'decimalSeparator' => '' )
		);

		if (
			false === $set_payment_form_initial_decimal_separator_result
			|| false === $set_subscription_form_initial_decimal_separator_result
			|| false === $set_checkout_form_initial_decimal_separator_result
			|| false === $set_checkout_subscription_form_initial_decimal_separator_result
		) {
			
		} else {
			return $set_payment_form_initial_decimal_separator_result 
			       + $set_subscription_form_initial_decimal_separator_result
			       + $set_checkout_form_initial_decimal_separator_result
			       + $set_checkout_subscription_form_initial_decimal_separator_result;
		}
		
		return true;
	}

}

class MM_WPFS_SetInitialQuantitiesForSubscriptionForms extends MM_WPFS_Patch {

	/**
	 * MM_WPFS_SetInitialQuantitiesForSubscriptionForms constructor.
	 */
	public function __construct() {
		$this->id             = 'set_initial_quantities_for_subscription_forms';
		$this->plugin_version = '5.2.0';
		$this->description    = 'A patch for setting initial value for quantity related fields on subscription forms and subscriptions. JIRA reference: WPFS-1043';
		$this->repeatable     = true;
	}

	public function apply() {
		$this->set_quantity_related_subscription_form_fields();
		$this->set_quantity_related_checkout_subscription_form_fields();
		$this->set_default_quantities_for_subscriptions();

		return true;
	}

	private function set_quantity_related_subscription_form_fields() {
		global $wpdb;

		$allow_multiple_subscriptions_update_result      = $wpdb->update(
			"{$wpdb->prefix}fullstripe_subscription_forms",
			array( 'allowMultipleSubscriptions' => '0' ),
			array( 'allowMultipleSubscriptions' => '' )
		);
		$maximum_quantity_of_subscriptions_update_result = $wpdb->update(
			"{$wpdb->prefix}fullstripe_subscription_forms",
			array( 'maximumQuantityOfSubscriptions' => '0' ),
			array( 'maximumQuantityOfSubscriptions' => '' )
		);

		if ( $allow_multiple_subscriptions_update_result === false || $maximum_quantity_of_subscriptions_update_result === false ) {
			return false;
		} else {
			return $allow_multiple_subscriptions_update_result + $maximum_quantity_of_subscriptions_update_result;
		}
	}

	private function set_quantity_related_checkout_subscription_form_fields() {
		global $wpdb;

		$allow_multiple_subscriptions_update_result      = $wpdb->update(
			"{$wpdb->prefix}fullstripe_checkout_subscription_forms",
			array( 'allowMultipleSubscriptions' => '0' ),
			array( 'allowMultipleSubscriptions' => '' )
		);
		$maximum_quantity_of_subscriptions_update_result = $wpdb->update(
			"{$wpdb->prefix}fullstripe_checkout_subscription_forms",
			array( 'maximumQuantityOfSubscriptions' => '0' ),
			array( 'maximumQuantityOfSubscriptions' => '' )
		);

		if ( $allow_multiple_subscriptions_update_result === false || $maximum_quantity_of_subscriptions_update_result === false ) {
			return false;
		} else {
			return $allow_multiple_subscriptions_update_result + $maximum_quantity_of_subscriptions_update_result;
		}
	}

	private function set_default_quantities_for_subscriptions() {
		global $wpdb;

		$update_result = $wpdb->update(
			"{$wpdb->prefix}fullstripe_subscribers",
			array( 'quantity' => 1 ),
			array( 'quantity' => '' )
		);

		if ( false === $update_result ) {
			return false;
		} else {
			return $update_result;
		}
	}

}


class MM_WPFS_SetInitialDefaultBillingCountryForInlinePaymentForms extends MM_WPFS_Patch {

	/**
	 * MM_WPFS_SetInitialDefaultBillingCountryForInlinePaymentForms constructor.
	 */
	public function __construct() {
		$this->id             = 'set_initial_default_billing_country_for_inline_payment_forms';
		$this->plugin_version = '5.1.0';
		$this->description    = 'A patch for setting initial value for default billing country on inline payment forms. JIRA reference: WPFS-1014';
		$this->repeatable     = true;
	}

	public function apply() {
		$this->set_default_billing_country();

		return true;
	}

	private function set_default_billing_country() {
		global $wpdb;

		$payment_form_update_result = $wpdb->update(
			"{$wpdb->prefix}fullstripe_payment_forms",
			array( 'defaultBillingCountry' => MM_WPFS::DEFAULT_BILLING_COUNTRY_INITIAL_VALUE ),
			array( 'defaultBillingCountry' => '' )
		);

		if ( $payment_form_update_result === false ) {
			return false;
		} else {
			return $payment_form_update_result;
		}

	}
}

class MM_WPFS_SetInitialProductNameForCheckoutForms extends MM_WPFS_Patch {


	/**
	 * MM_WPFS_SetInitialProductNameForPopupForms constructor.
	 */
	public function __construct() {
		$this->id             = 'set_initial_product_name_for_checkout_forms';
		$this->plugin_version = '5.0.1';
		$this->description    = 'A patch for setting initial Product Name values for Checkout forms. JIRA reference: WPFS-1011';
		$this->repeatable     = true;
	}

	public function apply() {
		$this->set_default_product_name_for_checkout_forms();

		return true;
	}

	private function set_default_product_name_for_checkout_forms() {
		global $wpdb;

		$defaultProductName          = MM_WPFS_Admin_PopupFormModel::getDefaultProductDescription();
		$checkout_form_update_result = $wpdb->update(
			"{$wpdb->prefix}fullstripe_checkout_forms",
			array( 'productDesc' => $defaultProductName ),
			array( 'productDesc' => '' )
		);
		if ( $checkout_form_update_result === false ) {
			return false;
		} else {
			return $checkout_form_update_result;
		}
	}

}

class MM_WPFS_ConvertPreferredLanguageForPopupForms extends MM_WPFS_Patch {

	/**
	 * MM_WPFS_ConvertPreferredLanguageForPopupForms constructor.
	 */
	public function __construct() {
		$this->id             = 'convert_preferred_language_for_popup_forms';
		$this->plugin_version = '5.0.0';
		$this->description    = 'A patch for converting preferredLanguage values from Norwegian (\'no\') to Norwegian Bokmål (\'nb\') for popup forms. JIRA reference: WPFS-961';
		$this->repeatable     = true;
	}

	public function apply() {
		$this->convert_preferred_language_for_popup_forms();

		return true;
	}

	private function convert_preferred_language_for_popup_forms() {
		global $wpdb;

		$preferredLanguageNorwegian               = 'no';
		$preferredLanguageNorwegianBokmal         = 'nb';
		$checkout_form_update_result              = $wpdb->update( "{$wpdb->prefix}fullstripe_checkout_forms", array( 'preferredLanguage' => $preferredLanguageNorwegianBokmal ), array( 'preferredLanguage' => $preferredLanguageNorwegian ) );
		$checkout_subscription_form_update_result = $wpdb->update( "{$wpdb->prefix}fullstripe_checkout_subscription_forms", array( 'preferredLanguage' => $preferredLanguageNorwegianBokmal ), array( 'preferredLanguage' => $preferredLanguageNorwegian ) );
		if ( $checkout_form_update_result === false || $checkout_subscription_form_update_result === false ) {
			return false;
		} else {
			return $checkout_form_update_result + $checkout_subscription_form_update_result;
		}
	}

}

class MM_WPFS_SetInitialPlanSelectorStyleForSubscriptionForms extends MM_WPFS_Patch {


	/**
	 * MM_WPFS_SetInitialPlanSelectorStyleForSubscriptionForms constructor.
	 */
	public function __construct() {
		$this->id             = 'set_initial_plan_selector_style_for_subscription_forms';
		$this->plugin_version = '4.0.0';
		$this->description    = 'A patch for setting the initial value of planSelectorStyle for subscription forms. JIRA reference: WPFS-814';
		$this->repeatable     = true;
	}

	public function apply() {
		$this->update_subscription_form_plan_selector_style();

		return true;
	}

	protected function update_subscription_form_plan_selector_style() {
		global $wpdb;

		$subscription_form_update_result          = $wpdb->update( "{$wpdb->prefix}fullstripe_subscription_forms", array( 'planSelectorStyle' => MM_WPFS::PLAN_SELECTOR_STYLE_DROPDOWN ), array( 'planSelectorStyle' => '' ) );
		$checkout_subscription_form_update_result = $wpdb->update( "{$wpdb->prefix}fullstripe_checkout_subscription_forms", array( 'planSelectorStyle' => MM_WPFS::PLAN_SELECTOR_STYLE_DROPDOWN ), array( 'planSelectorStyle' => '' ) );
		if ( $subscription_form_update_result === false || $checkout_subscription_form_update_result === false ) {
			return false;
		} else {
			return $subscription_form_update_result + $checkout_subscription_form_update_result;
		}
	}

}

class MM_WPFS_SetInitialAmountSelectorStyleForPaymentForms extends MM_WPFS_Patch {

	/**
	 * MM_WPFS_SetInitialAmountSelectorStyleForPaymentForms constructor.
	 */
	public function __construct() {
		$this->id             = 'set_initial_amount_selector_style_for_payment_forms';
		$this->plugin_version = '4.0.0';
		$this->description    = 'A patch for setting the initial value of amountSelectorStyle for payment forms. JIRA reference: WPFS-782';
		$this->repeatable     = true;
	}

	public function apply() {
		$this->update_payment_form_amount_selector_style();

		return true;
	}

	protected function update_payment_form_amount_selector_style() {
		global $wpdb;

		$payment_form_update_result  = $wpdb->update( "{$wpdb->prefix}fullstripe_payment_forms", array( 'amountSelectorStyle' => MM_WPFS::AMOUNT_SELECTOR_STYLE_DROPDOWN ), array( 'amountSelectorStyle' => '' ) );
		$checkout_form_update_result = $wpdb->update( "{$wpdb->prefix}fullstripe_checkout_forms", array( 'amountSelectorStyle' => MM_WPFS::AMOUNT_SELECTOR_STYLE_DROPDOWN ), array( 'amountSelectorStyle' => '' ) );
		if ( $payment_form_update_result === false || $checkout_form_update_result === false ) {
			return false;
		} else {
			return $payment_form_update_result + $checkout_form_update_result;
		}
	}

}

class MM_WPFS_SetShowButtonAmountForPopupPaymentForms extends MM_WPFS_Patch {

	/**
	 * MM_WPFS_SetShowButtonAmountForPopupPaymentForms constructor.
	 */
	public function __construct() {
		$this->id             = 'set_default_show_button_amount_for_popup_payment_forms';
		$this->plugin_version = '4.0.0';
		$this->description    = 'A patch for setting the initial value of showButtonAmount for popup payment forms. JIRA reference: WPFS-708';
		$this->repeatable     = true;
	}

	/**
	 * @return boolean
	 */
	public function apply() {
		$this->update_popup_payment_form_show_button_amounts();

		return true;
	}

	protected function update_popup_payment_form_show_button_amounts() {
		global $wpdb;

		$checkout_form_update_result = $wpdb->update( "{$wpdb->prefix}fullstripe_checkout_forms", array( 'showButtonAmount' => 0 ), array( 'showButtonAmount' => '' ) );
		if ( false === $checkout_form_update_result ) {
			return false;
		} else {
			return $checkout_form_update_result;
		}

	}

}

class MM_WPFS_SetStripeChargeFlagsAndMethodForPayments extends MM_WPFS_Patch {

	/**
	 * MM_WPFS_SetStripeChargeFlagsAndMethodForPayments constructor.
	 */
	public function __construct() {
		$this->id             = 'set_stripe_charge_flags_and_method_for_payments';
		$this->plugin_version = '3.16.0';
		$this->description    = 'A patch for setting the initial values of payment_method, captured, refunded, expired, last_charge_status for payments. JIRA reference: WPFS-202';
		$this->repeatable     = true;
	}

	/**
	 * @return boolean
	 */
	public function apply() {
		$this->update_payment_method();
		$this->update_payment_flags();

		return true;
	}

	private function update_payment_method() {
		global $wpdb;

		$payment_method_update_result = $wpdb->update(
			"{$wpdb->prefix}fullstripe_payments",
			array(
				'payment_method' => MM_WPFS::PAYMENT_METHOD_CARD
			),
			array( 'payment_method' => null )
		);

		return $payment_method_update_result;
	}

	private function update_payment_flags() {
		global $wpdb;

		$payment_flags_update_result = $wpdb->update(
			"{$wpdb->prefix}fullstripe_payments",
			array(
				'captured'           => 1,
				'refunded'           => 0,
				'expired'            => 0,
				'last_charge_status' => MM_WPFS::STRIPE_CHARGE_STATUS_SUCCEEDED
			),
			array( 'paid' => 1 )
		);

		return $payment_flags_update_result;
	}
}

class MM_WPFS_SetInitialChargeTypeForForms extends MM_WPFS_Patch {

	/**
	 * MM_WPFS_SetInitialChargeTypeForForms constructor.
	 */
	public function __construct() {
		$this->id             = 'set_initial_charge_type_for_forms';
		$this->plugin_version = '3.16.0';
		$this->description    = 'A patch for setting the initial value of chargeType for inline nad popup payment forms. JIRA reference: WPFS-202';
		$this->repeatable     = true;
	}

	public function apply() {
		$this->update_payment_forms_initial_charge_type();

		return true;
	}

	private function update_payment_forms_initial_charge_type() {
		global $wpdb;

		$payment_form_update_result  = $wpdb->update( "{$wpdb->prefix}fullstripe_payment_forms", array( 'chargeType' => MM_WPFS::CHARGE_TYPE_IMMEDIATE ), array( 'chargeType' => '' ) );
		$checkout_form_update_result = $wpdb->update( "{$wpdb->prefix}fullstripe_checkout_forms", array( 'chargeType' => MM_WPFS::CHARGE_TYPE_IMMEDIATE ), array( 'chargeType' => '' ) );
		if ( $payment_form_update_result === false || $checkout_form_update_result === false ) {
			return false;
		} else {
			return $payment_form_update_result + $checkout_form_update_result;
		}
	}

}

class MM_WPFS_SetStripeDescriptionForPaymentForms extends MM_WPFS_Patch {

	/**
	 * MM_WPFS_SetStripeDescriptionForPaymentForms constructor.
	 */
	public function __construct() {
		$this->id             = 'set_stripe_description_for_payment_forms';
		$this->plugin_version = '3.15.0';
		$this->description    = 'A patch for setting the Stripe description to an initial value for payment forms. JIRA reference: WPFS-85';
		$this->repeatable     = true;
	}

	public function apply() {
		$this->update_payment_forms_initial_stripe_description();

		return true;
	}

	private function update_payment_forms_initial_stripe_description() {
		global $wpdb;

		$payment_form_update_result  = $wpdb->update( "{$wpdb->prefix}fullstripe_payment_forms", array( 'stripeDescription' => MM_WPFS_Utils::create_default_payment_stripe_description() ), array( 'stripeDescription' => null ) );
		$checkout_form_update_result = $wpdb->update( "{$wpdb->prefix}fullstripe_checkout_forms", array( 'stripeDescription' => MM_WPFS_Utils::create_default_payment_stripe_description() ), array( 'stripeDescription' => null ) );
		if ( $payment_form_update_result === false || $checkout_form_update_result === false ) {
			return false;
		} else {
			return $payment_form_update_result + $checkout_form_update_result;
		}
	}

}

class MM_WPFS_SetPreferredLanguageForPopupForms extends MM_WPFS_Patch {
	/**
	 * MM_WPFS_SetPreferredLanguageForPopupForms constructor.
	 */
	public function __construct() {
		$this->id             = 'set_preferred_language_for_popup_forms';
		$this->plugin_version = '3.12.0';
		$this->description    = 'A patch for setting the preferred language to an initial value for popup (subscription) forms. JIRA reference: WPFS-191';
		$this->repeatable     = true;
	}

	/**
	 * @return boolean
	 */
	public function apply() {
		$this->update_popup_forms_initial_preferred_language();

		return true;
	}

	private function update_popup_forms_initial_preferred_language() {
		global $wpdb;

		$checkout_form_update_result              = $wpdb->update( "{$wpdb->prefix}fullstripe_checkout_forms", array( 'preferredLanguage' => MM_WPFS::PREFERRED_LANGUAGE_AUTO ), array( 'preferredLanguage' => null ) );
		$checkout_subscription_form_update_result = $wpdb->update( "{$wpdb->prefix}fullstripe_checkout_subscription_forms", array( 'preferredLanguage' => MM_WPFS::PREFERRED_LANGUAGE_AUTO ), array( 'preferredLanguage' => null ) );
		if ( $checkout_form_update_result === false || $checkout_subscription_form_update_result === false ) {
			return false;
		} else {
			return $checkout_form_update_result + $checkout_subscription_form_update_result;
		}
	}

}

class MM_WPFS_SetSimpleButtonLayout extends MM_WPFS_Patch {

	/**
	 * MM_WPFS_SetSimpleButtonLayout constructor.
	 */
	public function __construct() {
		$this->id             = 'set_simple_button_layout';
		$this->plugin_version = '3.12.0';
		$this->description    = 'A patch for setting the initial simple button layout value for popup subscription forms. JIRA reference: WPFS-452';
		$this->repeatable     = true;
	}

	/**
	 * @return boolean
	 */
	public function apply() {
		$this->update_simple_button_layout_for_popup_subscription_forms();

		return true;
	}

	private function update_simple_button_layout_for_popup_subscription_forms() {
		global $wpdb;

		$checkout_subscription_form_update_result = $wpdb->update( "{$wpdb->prefix}fullstripe_checkout_subscription_forms", array( 'simpleButtonLayout' => '0' ), array( 'simpleButtonLayout' => '' ) );

		return $checkout_subscription_form_update_result;
	}

}

class MM_WPFS_SetInitialVATRateTypeForSubscriptionForms extends MM_WPFS_Patch {

	/**
	 * MM_WPFS_SetInitialVatRateTypeForSubscriptionForms constructor.
	 */
	public function __construct() {
		$this->id             = 'set_initial_vat_rate_type_for_subscription_forms';
		$this->plugin_version = '3.12.0';
		$this->description    = 'A patch for setting the initial VAT rate type value for subscription and subscription checkout forms. JIRA reference: WPFS-119';
		$this->repeatable     = true;
	}

	public function apply() {
		$this->update_subscription_forms_vat_rate_type_value();

		return true;
	}

	private function update_subscription_forms_vat_rate_type_value() {
		global $wpdb;

		$subscription_form_update_result          = $wpdb->update( "{$wpdb->prefix}fullstripe_subscription_forms", array( 'vatRateType' => MM_WPFS::VAT_RATE_TYPE_NO_VAT ), array( 'vatRateType' => '' ) );
		$checkout_subscription_form_update_result = $wpdb->update( "{$wpdb->prefix}fullstripe_checkout_subscription_forms", array( 'vatRateType' => MM_WPFS::VAT_RATE_TYPE_NO_VAT ), array( 'vatRateType' => '' ) );
		if ( $subscription_form_update_result === false || $checkout_subscription_form_update_result === false ) {
			return false;
		} else {
			return $subscription_form_update_result + $checkout_subscription_form_update_result;
		}
	}

}

class MM_WPFS_FixEmailReceiptJSON extends MM_WPFS_Patch {
	/**
	 * MM_WPFS_FixEmailReceiptJSON constructor.
	 */
	public function __construct() {
		$this->id             = 'fix_email_receipts_option';
		$this->plugin_version = '3.11.3';
		$this->description    = 'A patch for fixing an invalid email receipts JSON string in the options array. JIRA reference: WPFS-463';
		$this->repeatable     = true;
	}

	/**
	 * @return boolean
	 */
	public function apply() {
		$options   = get_option( 'fullstripe_options' );
		$logPrefix = 'MM_WPFS_FixEmailReceiptJSON::apply():';

		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( $logPrefix . ' current fullstripe_options=' . print_r( $options, true ) );
		}

		if ( is_array( $options ) ) {
			$createDefaultEmailReceiptOptions = false;
			if ( array_key_exists( 'email_receipts', $options ) ) {
				$encodedEmailReceipts = $options['email_receipts'];
				$decodedEmailReceipts = json_decode( $encodedEmailReceipts );

				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( $logPrefix . ' decodedEmailReceipts=' . print_r( $decodedEmailReceipts, true ) );
				}
				if ( $decodedEmailReceipts == null || json_last_error() != JSON_ERROR_NONE ) {
					if ( $this->debugLog ) {
						MM_WPFS_Utils::log( $logPrefix . ' Decoded email receipt templates JSON structure in fullstripe_options is invalid!' );
					}
					$createDefaultEmailReceiptOptions = true;
				}
			} else {
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( $logPrefix . ' email-receipts key do not exist in fullstripe_options!' );
				}
				$createDefaultEmailReceiptOptions = true;
			}
			if ( $createDefaultEmailReceiptOptions ) {
				$defaultEmailReceipts      = MM_WPFS_Utils::create_default_email_receipts();
				$options['email_receipts'] = json_encode( $defaultEmailReceipts );
				update_option( 'fullstripe_options', $options );
			}
		} else {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( $logPrefix . ' WPFS options variable is not an array!' );
			}
		}

		return true;
	}

}

class MM_WPFS_SetSpecifiedAmountForCheckoutForms extends MM_WPFS_Patch {
	/**
	 * MM_WPFS_SetSpecifiedAmountForCheckoutForms constructor.
	 */
	public function __construct() {
		$this->id             = 'set_specified_amount_for_checkout_forms';
		$this->plugin_version = '3.11.0';
		$this->description    = 'A patch for setting the \'customAmount\' field for popup (checkout) forms to an initial value. JIRA reference: WPFS-412';
		$this->repeatable     = true;
	}

	/**
	 * @return boolean
	 */
	public function apply() {
		$this->update_checkout_forms_custom_amount();

		return true;
	}

	private function update_checkout_forms_custom_amount() {
		global $wpdb;

		return $wpdb->update( "{$wpdb->prefix}fullstripe_checkout_forms", array( 'customAmount' => MM_WPFS::PAYMENT_TYPE_SPECIFIED_AMOUNT ), array( 'customAmount' => '' ) );
	}

}

class MM_WPFS_DropCheckoutSubscriptionAlipayColumns extends MM_WPFS_Patch {
	/**
	 * MM_WPFS_DropCheckoutSubscriptionAlipayColumns constructor.
	 */
	public function __construct() {
		$this->id             = 'drop_alipay_columns';
		$this->plugin_version = '3.10.0';
		$this->description    = 'A patch for dropping Alipay related columns from checkout subscription table. JIRA reference: WPFS-424';
		$this->repeatable     = false;
	}

	/**
	 * @return boolean
	 */
	public function apply() {
		global $wpdb;

		$use_alipay_column_exists = $wpdb->get_results( $wpdb->prepare( "select * from information_schema.columns where table_schema=%s and table_name=%s and column_name=%s", DB_NAME, "{$wpdb->prefix}fullstripe_checkout_subscription_forms", 'useAlipay' ) );
		if ( ! empty( $use_alipay_column_exists ) ) {
			$wpdb->query( "alter table {$wpdb->prefix}fullstripe_checkout_subscription_forms drop column useAlipay" );
		}
		$alipay_reusable_column_exists = $wpdb->get_results( $wpdb->prepare( "select * from information_schema.columns where table_schema=%s and table_name=%s and column_name=%s", DB_NAME, "{$wpdb->prefix}fullstripe_checkout_subscription_forms", 'alipayReusable' ) );
		if ( ! empty( $alipay_reusable_column_exists ) ) {
			$wpdb->query( "alter table {$wpdb->prefix}fullstripe_checkout_subscription_forms drop column alipayReusable" );
		}

		return true;
	}
}

class MM_WPFS_MigrateCurrencyAndSetupFee extends MM_WPFS_Patch {

	/**
	 * MM_WPFS_MigrateCurrency constructor.
	 */
	public function __construct() {
		$this->id             = 'migrate_currency';
		$this->plugin_version = '3.9.0';
		$this->description    = 'A patch for setting the currency field for forms and currency and setup fee for plans. JIRA reference: WPFS-356';
		$this->repeatable     = true;
	}

	public function apply() {
		$update_form_currencies_result = $this->update_currency_for_forms();
		if ( $update_form_currencies_result !== false ) {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'MM_WPFS_MigrateCurrency::apply(): ' . sprintf( 'Updated %d forms with current currency.', $update_form_currencies_result ) );
			}
		} else {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'MM_WPFS_MigrateCurrency::apply(): Failed to update forms!' );
			}
		}
		$update_plan_setup_fees_result = $this->update_setup_fees_for_plans();
		if ( $update_plan_setup_fees_result !== false ) {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'MM_WPFS_MigrateCurrency::apply(): ' . sprintf( 'Updated %d plans with setup fee.', $update_plan_setup_fees_result ) );
			}
		} else {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'MM_WPFS_MigrateCurrency::apply(): Failed to update forms!' );
			}
		}
		if ( $update_form_currencies_result !== false ) {
			$this->remove_currency_from_fullstripe_options();
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'MM_WPFS_MigrateCurrency::apply(): Currency removed from options.' );
			}
		} else {
			return false;
		}

		return true;
	}

	private function update_currency_for_forms() {
		$options = get_option( 'fullstripe_options' );
		if ( is_array( $options ) ) {
			if ( array_key_exists( 'currency', $options ) ) {
				$currency = $options['currency'];
				global $wpdb;
				$payment_form_update_result  = $wpdb->update( "{$wpdb->prefix}fullstripe_payment_forms", array( 'currency' => $currency ), array( 'currency' => '' ) );
				$checkout_form_update_result = $wpdb->update( "{$wpdb->prefix}fullstripe_checkout_forms", array( 'currency' => $currency ), array( 'currency' => '' ) );
				if ( $payment_form_update_result === false || $checkout_form_update_result === false ) {
					return false;
				} else {
					return $payment_form_update_result + $checkout_form_update_result;
				}
			}
		}

		return 0;
	}

	private function update_setup_fees_for_plans() {
		$subscription_forms = $this->load_subscription_forms();

		$updated_plan_count = 0;

		if ( isset( $subscription_forms ) ) {
			foreach ( $subscription_forms as $form ) {
				if ( $form->setupFee != - 1 ) {
					$subscription_form_plans = json_decode( $form->plans );
					foreach ( $subscription_form_plans as $plan_id ) {
						$plan = \StripeWPFS\Plan::retrieve( $plan_id );
						if ( isset( $plan ) ) {
							if ( isset( $plan->metadata ) && ! isset( $plan->metadata->setup_fee ) ) {
								$plan->metadata->setup_fee = $form->setupFee;
								$plan->save();
								$updated_plan_count += 1;
							}
						}
					}
					$this->update_subscription_form_setup_fee( $form->subscriptionFormID, - 1 /* setupFee */ );
				}
			}
		}

		return $updated_plan_count;
	}

	private function load_subscription_forms() {
		global $wpdb;

		return $wpdb->get_results( "select * from {$wpdb->prefix}fullstripe_subscription_forms" );
	}

	private function update_subscription_form_setup_fee( $id, $setupFee ) {
		global $wpdb;

		return $wpdb->update( "{$wpdb->prefix}fullstripe_subscription_forms", array( 'setupFee' => $setupFee ), array( 'subscriptionFormID' => $id ) );
	}

	private function remove_currency_from_fullstripe_options() {
		$options = get_option( 'fullstripe_options' );
		if ( is_array( $options ) ) {
			if ( array_key_exists( 'currency', $options ) ) {
				unset( $options['currency'] );
				update_option( 'fullstripe_options', $options );
			}
		}
	}

}

class MM_WPFS_SetCustomInputRequired extends MM_WPFS_Patch {

	/**
	 * MM_WPFS_SetCustomInputRequired constructor.
	 */
	public function __construct() {
		$this->id             = 'set_custom_input_required';
		$this->plugin_version = '3.8.0';
		$this->description    = 'A patch for setting the customInputRequired field for forms. JIRA reference: WPFS-318';
		$this->repeatable     = true;
	}

	public function apply() {
		$this->update_custom_input_required_for_forms();

		return true;
	}

	private function update_custom_input_required_for_forms() {
		global $wpdb;

		$payment_update_result      = $wpdb->update( "{$wpdb->prefix}fullstripe_payment_forms", array( 'customInputRequired' => '0' ), array( 'customInputRequired' => '' ) );
		$subscription_update_result = $wpdb->update( "{$wpdb->prefix}fullstripe_subscription_forms", array( 'customInputRequired' => '0' ), array( 'customInputRequired' => '' ) );

		if ( $payment_update_result === false || $subscription_update_result === false ) {
			return false;
		} else {
			return $payment_update_result + $subscription_update_result;
		}
	}

}

class MM_WPFS_SetShowDetailedSuccessPage extends MM_WPFS_Patch {

	/**
	 * MM_WPFS_SetShowDetailedSuccessPage constructor.
	 */
	public function __construct() {
		$this->id             = 'set_show_detailed_success_page';
		$this->plugin_version = '3.8.0';
		$this->description    = 'A patch for setting the showDetailedSuccessPage field for forms. JIRA reference: WPFS-313';
		$this->repeatable     = true;
	}

	public function apply() {
		$this->update_show_detailed_success_page_for_forms();

		return true;
	}

	private function update_show_detailed_success_page_for_forms() {
		global $wpdb;

		$payment_update_result      = $wpdb->update( "{$wpdb->prefix}fullstripe_payment_forms", array( 'showDetailedSuccessPage' => '0' ), array( 'showDetailedSuccessPage' => '' ) );
		$checkout_update_result     = $wpdb->update( "{$wpdb->prefix}fullstripe_checkout_forms", array( 'showDetailedSuccessPage' => '0' ), array( 'showDetailedSuccessPage' => '' ) );
		$subscription_update_result = $wpdb->update( "{$wpdb->prefix}fullstripe_subscription_forms", array( 'showDetailedSuccessPage' => '0' ), array( 'showDetailedSuccessPage' => '' ) );

		if ( $payment_update_result === false || $checkout_update_result === false || $subscription_update_result === false ) {
			// tnagy an error occurred
			return false;
		} else {
			return $payment_update_result + $checkout_update_result + $subscription_update_result;
		}
	}

}

class MM_WPFS_SetAllowListOfAmountsCustom extends MM_WPFS_Patch {

	/**
	 * MM_WPFS_SetAllowListOfAmountsCustom constructor.
	 */
	public function __construct() {
		$this->id             = 'set_allow_list_of_amounts_custom';
		$this->plugin_version = '3.8.0';
		$this->description    = 'A patch for setting the allowListOfAmountsCustom field for payment forms. JIRA reference: WPFS-307';
		$this->repeatable     = true;
	}

	public function apply() {
		$this->update_allow_list_of_amounts_custom_for_payment_forms();

		return true;
	}

	private function update_allow_list_of_amounts_custom_for_payment_forms() {
		global $wpdb;

		return $wpdb->update( "{$wpdb->prefix}fullstripe_payment_forms", array( 'allowListOfAmountsCustom' => '0' ), array( 'allowListOfAmountsCustom' => '' ) );
	}

}

class MM_WPFS_SetCurrentCurrencyForPayments extends MM_WPFS_Patch {

	/**
	 * MM_WPFS_SetCurrentCurrencyForPayments constructor.
	 */
	public function __construct() {
		$this->id             = 'set_current_currency_for_payments';
		$this->plugin_version = '3.7.0';
		$this->description    = 'A patch for setting the current currency for payments made before 3.7.0 without saved currency. JIRA reference: WPFS-240';
		$this->repeatable     = true;
	}

	public function apply() {
		$this->update_currency_for_payments();

		return true;
	}

	private function update_currency_for_payments() {
		$options = get_option( 'fullstripe_options' );
		if ( is_array( $options ) ) {
			if ( array_key_exists( 'currency', $options ) ) {
				$currency = $options['currency'];

				global $wpdb;

				return $wpdb->update( "{$wpdb->prefix}fullstripe_payments", array( 'currency' => $currency ), array( 'currency' => '' ) );
			}
		}

		return false;
	}

}

class MM_WPFS_ConvertSubscriptionStatus extends MM_WPFS_Patch {

	/**
	 * MM_WPFS_ConvertSubscriptionStatus constructor.
	 */
	public function __construct() {
		$this->id             = 'convert_subscription_status';
		$this->plugin_version = '3.6.0';
		$this->description    = 'A patch for converting subscription status fields from version before 3.6.0. JIRA reference: WPFS-194';
		$this->repeatable     = true;
	}

	public function apply() {

		$this->update_subscription_status();

		return true;
	}

	private function update_subscription_status() {
		global $wpdb;

		return $wpdb->update( "{$wpdb->prefix}fullstripe_subscribers", array( 'status' => 'running' ), array( 'status' => '' ) );
	}

}

class MM_WPFS_ConvertEmailReceiptsPatch extends MM_WPFS_Patch {

	/**
	 * MM_WPFS_ConvertEmailReceiptsPatch constructor.
	 */
	public function __construct() {
		$this->id             = 'convert_email_receipts';
		$this->plugin_version = '3.6.0';
		$this->description    = 'A patch for converting email receipts to JSON format. JIRA reference: WPFS-170';
		$this->repeatable     = true;
	}

	public function apply() {
		$options = get_option( 'fullstripe_options' );
		if ( is_array( $options ) ) {
			if (
				array_key_exists( 'email_receipt_subject', $options )
				&& array_key_exists( 'email_receipt_html', $options )
				&& array_key_exists( 'subscription_email_receipt_subject', $options )
				&& array_key_exists( 'subscription_email_receipt_html', $options )
			) {
				$emailReceipts                         = array();
				$paymentMade                           = new stdClass();
				$subscriptionStarted                   = new stdClass();
				$subscriptionFinished                  = new stdClass();
				$paymentMade->subject                  = $options['email_receipt_subject'];
				$paymentMade->html                     = html_entity_decode( $options['email_receipt_html'] );
				$subscriptionStarted->subject          = $options['subscription_email_receipt_subject'];
				$subscriptionStarted->html             = html_entity_decode( $options['subscription_email_receipt_html'] );
				$subscriptionFinished->subject         = 'Subscription ended';
				$subscriptionFinished->html            = '<html><body><p>Hi,</p><p>Your %PLAN_NAME% subscription has come to an end.</p><p>Thanks</p><br/>%NAME%</body></html>';
				$emailReceipts['paymentMade']          = $paymentMade;
				$emailReceipts['subscriptionStarted']  = $subscriptionStarted;
				$emailReceipts['subscriptionFinished'] = $subscriptionFinished;

				$options['email_receipts'] = json_encode( $emailReceipts );
				unset( $options['email_receipt_subject'] );
				unset( $options['email_receipt_html'] );
				unset( $options['subscription_email_receipt_subject'] );
				unset( $options['subscription_email_receipt_html'] );

				update_option( 'fullstripe_options', $options );
			}
		}

		return true;
	}

}

class MM_WPFS_ConvertSubscriptionFormPlansPatch extends MM_WPFS_Patch {

	/**
	 * MM_WPFS_ConvertSubscriptionFormPlansPatch constructor.
	 */
	public function __construct() {
		$this->id             = 'convert_subscription_form_plans';
		$this->plugin_version = '3.6.0';
		$this->description    = 'A patch for converting subscription forms\' plans column to JSON format. JIRA reference: WPFS-15';
		$this->repeatable     = true;
	}

	/**
	 * @return bool
	 * @throws Exception
	 */
	public function apply() {
		$subscription_forms = $this->load_subscription_forms();

		if ( isset( $subscription_forms ) ) {
			foreach ( $subscription_forms as $form ) {
				json_decode( $form->plans );
				if ( json_last_error() != JSON_ERROR_NONE ) {
					$this->update_subscription_form_plans( $form->subscriptionFormID, json_encode( explode( ',', $form->plans ) ) );
				}
			}
		}

		return true;
	}

	private function load_subscription_forms() {
		global $wpdb;

		return $wpdb->get_results( "select * from {$wpdb->prefix}fullstripe_subscription_forms" );
	}

	private function update_subscription_form_plans( $id, $plans ) {
		global $wpdb;

		return $wpdb->update( "{$wpdb->prefix}fullstripe_subscription_forms", array( 'plans' => $plans ), array( 'subscriptionFormID' => $id ) );
	}
}

class MM_WPFS_DummyPatch extends MM_WPFS_Patch {

	/**
	 * MM_WPFS_Dummy constructor.
	 */
	public function __construct() {
		$this->id             = 'dummy';
		$this->plugin_version = '3.6.0';
		$this->description    = 'A dummy patch for testing purposes.';
		$this->repeatable     = false;
	}

	public function apply() {
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'WPFS DEBUG apply(): ' . 'Starting DummyPatch...' );
			MM_WPFS_Utils::log( 'WPFS DEBUG apply(): ' . 'DummyPatch finished.' );
		}

		return true;
	}
}

abstract class MM_WPFS_Patch {

	protected $debugLog = false;

	/* @var $id string */
	protected $id;
	/* @var $plugin_version string */
	protected $plugin_version;
	/* @var $description string */
	protected $description;
	/* @var $repeatable boolean */
	protected $repeatable = false;

	/**
	 * @return boolean
	 */
	public abstract function apply();

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getPluginVersion() {
		return $this->plugin_version;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @return boolean
	 */
	public function isRepeatable() {
		return $this->repeatable;
	}

	/**
	 *
	 * @param $result
	 *
	 * @param $message
	 *
	 * @throws Exception
	 */
	protected function handleDbError( $result, $message ) {
		if ( $result === false ) {
			global $wpdb;
			MM_WPFS_Utils::log( sprintf( "%s: Raised exception with message=%s", 'WPFS ERROR', $message ) );
			MM_WPFS_Utils::log( sprintf( "%s: SQL last error=%s", 'WPFS ERROR', $wpdb->last_error ) );
			throw new Exception( $message );
		}
	}

}