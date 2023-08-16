<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2017.03.02.
 * Time: 16:48
 */
?>
<table class="form-table">
    <tr valign="top">
        <th scope="row">
            <label class="control-label"><?php esc_html_e( 'Form Type:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
        </th>
        <td>
            <?php esc_html_e( 'Inline donation form', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <label class="control-label"><?php esc_html_e( 'Form Name:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
        </th>
        <td>
            <input type="text" class="regular-text" name="form_name" id="form_name" value="<?php echo $form->name; ?>" maxlength="<?php echo $form_data::NAME_LENGTH; ?>">

            <p class="description"><?php esc_html_e( 'This name will be used to identify this form in the shortcode i.e. [fullstripe_form name="formName" type="inline_donation"].', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
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
                        if ( $form->currency === $currency_key ) {
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

            <ul id="donation_amount_list">
                <?php
                $donationAmounts = MM_WPFS_Utils::decodeJsonArray( $form->donationAmounts );

                foreach ( $donationAmounts as $donationAmount ):
                        if ( !is_numeric ( $donationAmount ) ) {
                            continue;
                        }
                        $donationAmountLabel   = MM_WPFS_Currencies::formatAndEscapeByAdmin( $form->currency, $donationAmount );
                    ?>
                    <li class="ui-state-default" data-toggle="tooltip" title="<?php esc_attr_e( 'You can reorder this list by using drag\'n\'drop.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" data-donation-amount-value="<?php echo $donationAmount ?>">
                        <span class="amount"><?php echo $donationAmountLabel; ?></span>
                        <a class="dd_delete" href="#">Delete</a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="description"><?php esc_html_e( 'The amount is in the smallest unit for the currency. i.e. for $10.00 enter 1000, for ¥10 enter 10. You can use drag\'n\'drop to reorder the donation amounts.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
            <br>
            <label class="checkbox inline"><input type="checkbox" name="allow_custom_donation_amount" id="allow_custom_donation_amount" value="1" <?php echo $form->allowCustomDonationAmount == '1' ? 'checked' : '' ?>><?php esc_html_e( 'Allow Custom Amount to Be Entered?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
            <input type="hidden" name="donation_amount_values">
        </td>
    </tr>
    <tr valign="top" id="donation_frequencies_row">
        <th scope="row">
            <label class="control-label"><?php esc_html_e( 'Recurring Donation Frequencies:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
        </th>
        <td>
            <label class="checkbox inline"><input type="checkbox" name="allow_daily_recurring" id="allow_daily_recurring" value="1" <?php echo $form->allowDailyRecurring == '1' ? 'checked' : '' ?>><?php esc_html_e( 'Daily', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
            <label class="checkbox inline"><input type="checkbox" name="allow_weekly_recurring" id="allow_weekly_recurring" value="1" <?php echo $form->allowWeeklyRecurring == '1' ? 'checked' : '' ?>><?php esc_html_e( 'Weekly', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
            <label class="checkbox inline"><input type="checkbox" name="allow_monthly_recurring" id="allow_monthly_recurring" value="1" <?php echo $form->allowMonthlyRecurring == '1' ? 'checked' : '' ?>><?php esc_html_e( 'Monthly', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
            <label class="checkbox inline"><input type="checkbox" name="allow_annual_recurring" id="allow_annual_recurring" value="1" <?php echo $form->allowAnnualRecurring == '1' ? 'checked' : '' ?>><?php esc_html_e( 'Annual', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
        </td>
    </tr>
    <tr valign="top" id="stripe_description_row">
        <th scope="row">
            <label class="control-label">
                <?php esc_html_e( 'Donation description: ', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
            </label>
        </th>
        <td>
            <textarea rows="3" class="large-text code" name="stripe_description"><?php echo esc_html( $form->stripeDescription ); ?></textarea>
            <p class="description"><?php printf( __( 'It appears in the "Payment details" section of the payment on the Stripe dashboard. You can use placeholders, see the <a target="_blank" href="%s">Help page</a> for more options.', MM_WPFS::L10N_DOMAIN_ADMIN ), admin_url( "admin.php?page=fullstripe-help#receipt-tokens" ) ); ?> </p>
        </td>
    </tr>
</table>
