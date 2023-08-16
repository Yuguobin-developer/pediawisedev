<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2017.02.23.
 * Time: 14:57
 */
?>
<h2 id="create-donation-form-tabs" class="nav-tab-wrapper wpfs-admin-form-tabs">
    <a href="#create-donation-form-tab-payment" class="nav-tab"><?php esc_html_e( 'Payment', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
    <a href="#create-donation-form-tab-appearance" class="nav-tab"><?php esc_html_e( 'Appearance', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
    <a href="#create-donation-form-tab-custom-fields" class="nav-tab"><?php esc_html_e( 'Custom Fields', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
    <a href="#create-donation-form-tab-actions-after-payment" class="nav-tab"><?php esc_html_e( 'Actions after payment', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
</h2>
<form class="form-horizontal wpfs-admin-form" action="" method="POST" id="create-donation-form">
    <p class="tips"></p>
    <input type="hidden" name="action" value="wp_full_stripe_create_inline_donation_form">
    <div id="create-donation-form-tab-payment" class="wpfs-tab-content">
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label class="control-label"><?php esc_html_e( 'Form Type:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
                </th>
                <td><?php esc_html_e( 'Inline donation form', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label"><?php esc_html_e( 'Form Name:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="form_name" id="form_name" maxlength="<?php echo $form_data::NAME_LENGTH; ?>">

                    <p class="description"><?php esc_html_e( 'This name will be used to identify this form in the shortcode i.e. [fullstripe_form name="formName" type="inline_donation"]', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
                </td>
            </tr>
            <tr valign="top" id="donation_currency_row">
                <th scope="row">
                    <label class="control-label" for="currency"><?php esc_html_e( "Donation Currency: ", MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
                </th>
                <td>
                    <div class="ui-widget">
                        <select id="currency" name="form_currency">
                            <option value=""><?php esc_attr_e( 'Select from the list or start typing', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
                            <?php
                            foreach ( MM_WPFS_Currencies::get_available_currencies() as $currency_key => $currency_obj ) {
                                $currency_array = MM_WPFS_Currencies::get_currency_for( $currency_key );
                                $option         = '<option value="' . $currency_key . '"';
                                $option .= ' data-currency-symbol="' . $currency_array['symbol'] . '"';
                                $option .= ' data-zero-decimal-support="' . ( $currency_array['zeroDecimalSupport'] == true ? 'true' : 'false' ) . '"';
                                if ( MM_WPFS::CURRENCY_USD === $currency_key ) {
                                    $option .= ' selected="selected"';
                                }
                                $option .= '>';
                                $option .= $currency_obj['name'] . ' (' . $currency_obj['code'] . ')';
                                $option .= '</option>';
                                echo $option;
                            }
                            ?>
                        </select>
                    </div>
                </td>
            </tr>
            <tr valign="top" id="donation_amount_list_row">
                <th scope="row">
                    <label class="control-label"><?php esc_html_e( 'Donation Amount Options:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
                </th>
                <td>
                    <input type="text" id="donation_amount_value" placeholder="<?php esc_attr_e( 'Amount', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" maxlength="<?php echo $form_data::PAYMENT_AMOUNT_LENGTH; ?>">
                    <a href="#" class="button button-primary" id="add_donation_amount_button"><?php esc_html_e( 'Add', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
                    <ul id="donation_amount_list"></ul>
                    <p class="description"><?php esc_html_e( 'The amount is in the smallest unit for the currency. i.e. for $10.00 enter 1000, for ¥10 enter 10. You can use drag\'n\'drop to reorder the donation amounts.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
                    <br/>
                    <label class="checkbox inline"><input type="checkbox" name="allow_custom_donation_amount" id="allow_custom_donation_amount" value="1"><?php esc_html_e( 'Allow Custom Amount to Be Entered?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
                    <input type="hidden" name="donation_amount_values">
                </td>
            </tr>
            <tr valign="top" id="donation_frequencies_row">
                <th scope="row">
                    <label class="control-label"><?php esc_html_e( 'Recurring Donation Frequencies:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
                </th>
                <td>
                    <label class="checkbox inline"><input type="checkbox" name="allow_daily_recurring" id="allow_daily_recurring" value="1"><?php esc_html_e( 'Daily', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
                    <label class="checkbox inline"><input type="checkbox" name="allow_weekly_recurring" id="allow_weekly_recurring" value="1"><?php esc_html_e( 'Weekly', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
                    <label class="checkbox inline"><input type="checkbox" name="allow_monthly_recurring" id="allow_monthly_recurring" value="1"><?php esc_html_e( 'Monthly', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
                    <label class="checkbox inline"><input type="checkbox" name="allow_annual_recurring" id="allow_annual_recurring" value="1"><?php esc_html_e( 'Annual', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
                </td>
            </tr>
            <tr valign="top" id="stripe_description_row">
                <th scope="row">
                    <label class="control-label">
                        <?php esc_html_e( 'Donation description: ', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                    </label>
                </th>
                <td>
                    <textarea rows="3" class="large-text code" name="stripe_description"><?php echo esc_html( MM_WPFS_Utils::create_default_donation_stripe_description() ); ?></textarea>
                    <p class="description"><?php printf( __( 'It appears in the "Payment details" section of the payment on the Stripe dashboard. You can use placeholders, see the <a target="_blank" href="%s">Help page</a> for more options.', MM_WPFS::L10N_DOMAIN_ADMIN ), admin_url( "admin.php?page=fullstripe-help#receipt-tokens" ) ); ?> </p>
                </td>
            </tr>
        </table>
    </div>
    <div id="create-donation-form-tab-appearance" class="wpfs-tab-content">
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label class="control-label"><?php esc_html_e( 'Donation Button Text:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="form_button_text" id="form_button_text" value="<?php esc_attr_e( 'Donate', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" maxlength="<?php echo $form_data::BUTTON_TITLE_LENGTH; ?>">

                    <p class="description"><?php esc_html_e( 'You can use the {{amount}} placeholder to display the selected/entered donation amount.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label"><?php esc_html_e( 'Collect Billing Address?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
                </th>
                <td>
                    <label class="radio inline">
                        <input type="radio" name="form_show_address_input" id="hide_address_input" value="0" checked="checked">
                        <?php esc_html_e( 'No', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                    </label>
                    <label class="radio inline">
                        <input type="radio" name="form_show_address_input" id="show_address_input" value="1">
                        <?php esc_html_e( 'Yes', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                    </label>

                    <p class="description"><?php esc_html_e( 'Should this payment form also ask for the customers\' billing address?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
                </td>
            </tr>
            <tr id="defaultBillingCountryRow" valign="top" style="display: none;">
                <th scope="row">
                    <label class="control-label"><?php esc_html_e( 'Default Billing Country:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
                </th>
                <td>
                    <select name="form_default_billing_country" class="fullstripe-form-input input-xlarge">
                        <?php
                        foreach ( MM_WPFS_Countries::get_available_countries() as $countryKey => $countryObject ) {
                            $option = '<option';
                            $option .= " value=\"{$countryKey}\"";
                            if ( $countryKey == MM_WPFS::DEFAULT_BILLING_COUNTRY_INITIAL_VALUE ) {
                                $option .= ' selected="selected"';
                            }
                            $option .= '>';
                            $option .= MM_WPFS_Admin::translateLabelAdmin($countryObject['name']);
                            $option .= '</option>';
                            echo $option;
                        }
                        ?>
                    </select>
                    <p class="description"><?php esc_html_e( "It's the selected country when the form is rendered for the first time.", MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label"><?php esc_html_e( 'Collect Shipping Address?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
                </th>
                <td>
                    <label class="radio inline">
                        <input type="radio" name="form_show_shipping_address_input" id="hide_shipping_address_input" value="0" checked="checked">
                        <?php esc_html_e( 'No', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                    </label>
                    <label class="radio inline">
                        <input type="radio" name="form_show_shipping_address_input" id="show_shipping_address_input" value="1">
                        <?php esc_html_e( 'Yes', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                    </label>

                    <p class="description"><?php esc_html_e( 'Should this payment form also ask for the customers\' shipping address?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label" for=""><?php esc_html_e( "Card Input Field Language: ", MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
                </th>
                <td>
                    <select name="form_preferred_language">
                        <option value="<?php echo MM_WPFS::PREFERRED_LANGUAGE_AUTO; ?>"><?php esc_html_e( 'Auto', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
                        <?php
                        foreach ( MM_WPFS::get_available_stripe_elements_languages() as $language ) {
                            $option = '<option value="' . $language['value'] . '"';
                            $option .= '>';
                            $option .= $language['name'];
                            $option .= '</option>';
                            echo $option;
                        }
                        ?>
                    </select>

                    <p class="description"><?php esc_html_e( "Display the card info field in the selected language. Use 'Auto' to determine the language from the locale sent by the customer's browser.", MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label"><?php esc_html_e( 'Format decimals with:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
                </th>
                <td>
                    <label class="checkbox">
                        <input type="radio" name="decimal_separator" id="decimal_separator_dot" value="<?php echo MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT; ?>" checked>
                        <?php esc_html_e( '$10.99 (dot)', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                    </label>
                    <label class="checkbox">
                        <input type="radio" name="decimal_separator" id="decimal_separator_comma" value="<?php echo MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_COMMA; ?>">
                        <?php esc_html_e( '$10,99 (comma)', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label"><?php esc_html_e( 'Use currency symbol or code?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
                </th>
                <td>
                    <label class="checkbox">
                        <input type="radio" name="show_currency_symbol_instead_of_code" id="use_currency_symbol" value="1" checked>
                        <?php esc_html_e( '$10.99 (symbol)', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                    </label>
                    <label class="checkbox">
                        <input type="radio" name="show_currency_symbol_instead_of_code" id="use_currency_code" value="0">
                        <?php esc_html_e( 'USD 10.99 (code)', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label"><?php esc_html_e( 'Put currency identifier on:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
                </th>
                <td>
                    <label class="checkbox">
                        <input type="radio" name="show_currency_sign_at_first_position" id="put_currency_identifier_to_the_left" value="1" checked>
                        <?php esc_html_e( '€10.99 (left)', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                    </label>
                    <label class="checkbox">
                        <input type="radio" name="show_currency_sign_at_first_position" id="put_currency_identifier_to_the_right" value="0">
                        <?php esc_html_e( '10.99€ (right)', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label class="control-label"><?php esc_html_e( 'Insert space between amount and currency?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
                </th>
                <td>
                    <label class="checkbox">
                        <input type="radio" name="put_whitespace_between_currency_and_amount" id="do_not_put_whitespace_between_currency_and_amount" value="0" checked>
                        <?php esc_html_e( '$10.99 (no)', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                    </label>
                    <label class="checkbox">
                        <input type="radio" name="put_whitespace_between_currency_and_amount" id="put_whitespace_between_currency_and_amount" value="1">
                        <?php esc_html_e( 'USD 10.99 (yes)', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                    </label>
                </td>
            </tr>
        </table>
    </div>
    <div id="create-donation-form-tab-custom-fields" class="wpfs-tab-content">
        <?php include('create_payment_form_tab_custom_fields.php'); ?>
    </div>
    <div id="create-donation-form-tab-actions-after-payment" class="wpfs-tab-content">
        <?php include('create_payment_form_tab_actions_after_payment.php'); ?>
    </div>
    <p class="submit">
        <button class="button button-primary" type="submit"><?php esc_html_e( 'Create Form', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></button>
        <a href="<?php echo admin_url( 'admin.php?page=fullstripe-donations&tab=forms' ); ?>" class="button"><?php esc_html_e( 'Cancel', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
        <img src="<?php echo MM_WPFS_Assets::images( 'loader.gif' ); ?>" alt="<?php esc_attr_e( 'Loading...', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" class="showLoading"/>
    </p>
</form>
