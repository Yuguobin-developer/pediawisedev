<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2017.02.23.
 * Time: 14:57
 */

$plans = MM_WPFS::getInstance()->get_plans();

?>

<?php if ( count( $plans ) === 0 ): ?>
	<p class="alert alert-info"><?php esc_html_e( 'You must have at least one subscription plan created before creating a subscription form.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
<?php else: ?>
	<h2 id="create-subscription-form-tabs" class="nav-tab-wrapper wpfs-admin-form-tabs">
		<a href="#create-subscription-form-tab-payment" class="nav-tab"><?php esc_html_e( 'Payment', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
		<a href="#create-subscription-form-tab-finance" class="nav-tab"><?php esc_html_e( 'Finance', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
		<a href="#create-subscription-form-tab-appearance" class="nav-tab"><?php esc_html_e( 'Appearance', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
		<a href="#create-subscription-form-tab-custom-fields" class="nav-tab"><?php esc_html_e( 'Custom Fields', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
		<a href="#create-subscription-form-tab-actions-after-payment" class="nav-tab"><?php esc_html_e( 'Actions after payment', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
	</h2>
	<form class="form-horizontal wpfs-admin-form" action="" method="POST" id="create-subscription-form">
		<p class="tips"></p>
		<input type="hidden" name="action" value="wp_full_stripe_create_subscripton_form"/>
		<div id="create-subscription-form-tab-payment" class="wpfs-tab-content">
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label class="control-label"><?php esc_html_e( 'Form Type:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
					</th>
					<td><?php esc_html_e( 'Inline subscription form', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label class="control-label"><?php esc_html_e( 'Form Name:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
					</th>
					<td>
						<input type="text" class="regular-text" name="form_name" id="form_name" maxlength="<?php echo $form_data::NAME_LENGTH; ?>">
						<p class="description"><?php esc_html_e( 'This name will be used to identify this form in the shortcode i.e. [fullstripe_form name="formName" type="inline_subscription"].', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label class="control-label"><?php esc_html_e( 'Include Coupon Input Field?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
					</th>
					<td>
						<label class="radio inline">
							<input type="radio" name="form_include_coupon_input" id="noinclude_coupon_input" value="0" checked="checked">
							<?php esc_html_e( 'No', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
						</label>
						<label class="radio inline">
							<input type="radio" name="form_include_coupon_input" id="include_coupon_input" value="1">
							<?php esc_html_e( 'Yes', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
						</label>
						<p class="description"><?php printf( __( 'You can allow customers to input coupon codes for discounts. Must create the coupon in your <a href="%s" target="_blank">Stripe account dashboard</a>.', MM_WPFS::L10N_DOMAIN_ADMIN ), 'https://dashboard.stripe.com/' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label class="control-label"><?php esc_html_e( 'Allow Multiple Subscriptions?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
					</th>
					<td>
						<label class="radio inline">
							<input type="radio" name="form_allow_multiple_subscriptions_input" id="allow_multiple_subscriptions_input_no" value="0" checked="checked">
							<?php esc_html_e( 'No', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
						</label>
						<label class="radio inline">
							<input type="radio" name="form_allow_multiple_subscriptions_input" id="allow_multiple_subscriptions_input_yes" value="1">
							<?php esc_html_e( 'Yes', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Allow customers to choose how many plans they want to subscribe.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
					</td>
				</tr>
				<tr valign="top" id="maximum_number_of_subscriptions_row" style="display: none;">
					<th scope="row">
						<label class="control-label"><?php esc_html_e( 'Maximum Number of Subscriptions:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
					</th>
					<td>
						<input type="text" class="regular-text" name="form_maximum_quantity_of_subscriptions" id="form_maximum_quantity_of_subscriptions" value="0">
						<p class="description"><?php esc_html_e( 'Enter 0 (zero) if customers can subscribe to any number of subscriptions.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
					</td>
				</tr>
                <tr valign="top">
                    <th scope="row">
                        <label class="control-label"><?php esc_html_e( 'Billing Cycle Anchor Day:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
                    </th>
                    <td>
                        <label class="radio inline">
                            <input type="radio" name="form_anchor_billing_cycle_input" id="anchor_billing_cycle_no" value="0" checked="checked">
                            <?php esc_html_e( 'When customer subscribed', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                        </label>
                        <label class="radio inline">
                            <input type="radio" name="form_anchor_billing_cycle_input" id="anchor_billing_cycle_yes" value="1">
                            <?php esc_html_e( 'On this day of month:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                            <select name="form_billing_cycle_anchor_day_input" id="billing_cycle_anchor_day_select" disabled>
                            <?php for ( $dayIdx = 1; $dayIdx <= 28; $dayIdx++ ) { ?>
                                <option value="<?php echo $dayIdx ?>"><?php echo $dayIdx ?></option>
                            <?php } ?>
                            </select>
                        </label>
                        <p class="description"><?php esc_html_e( 'Anchor day of the month for monthly subscription plans.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
                    </td>
                </tr>
                <tr valign="top" id="prorate_until_anchor_day_row" style="display: none;">
                    <th scope="row">
                        <label class="control-label"><?php esc_html_e( 'Prorate until Anchor Day?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
                    </th>
                    <td>
                        <label class="radio inline">
                            <input type="radio" name="form_prorate_until_anchor_day_input" id="prorate_until_anchor_day_no" value="0">
                            <?php esc_html_e( 'No', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                        </label>
                        <label class="radio inline">
                            <input type="radio" name="form_prorate_until_anchor_day_input" id="prorate_until_anchor_day_yes" value="1" checked="checked">
                            <?php esc_html_e( 'Yes', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                        </label>
                        <p class="description"><?php esc_html_e( 'Should the plugin prorate the charges resulting from the billing anchor day?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
                    </td>
                </tr>
				<tr valign="top">
					<th scope="row">
						<label class="control-label"><?php esc_html_e( 'Plans:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
					</th>
					<td>
						<div class="plan_checkboxes">
							<ul class="plan_checkbox_list">
								<?php $plan_order = array(); ?>
								<?php foreach ( $plans as $plan ): ?>
									<?php
									$plan_order[]   = $plan->id;
									$interval_label = MM_WPFS_Admin::formatIntervalLabelAdmin( $plan->interval, $plan->interval_count );
									$amount_label   = isset( $plan->amount ) ? MM_WPFS_Currencies::formatAndEscape( $plan->currency, $plan->amount ) : '';
									?>
									<li class="ui-state-default" data-toggle="tooltip" title="<?php esc_attr_e( 'You can reorder this list by using drag\'n\'drop.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" data-plan-id="<?php echo esc_attr( $plan->id ); ?>">
										<label class="checkbox inline">
											<input type="checkbox" class="plan_checkbox" id="check_<?php echo esc_attr( $plan->id ); ?>" value="<?php echo esc_attr( $plan->id ); ?>">
                                        <span class="plan_checkbox_text"><?php echo esc_html( $plan->product->name ); ?>
	                                        (<?php echo $amount_label; ?> / <?php echo esc_html( $interval_label ); ?>)
                                        </span>
										</label>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
						<p class="description"><?php esc_html_e( 'Which subscription plans can be chosen on this form. The list can be reordered by using drag\'n\'drop.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
						<input type="hidden" id="plan_order" name="plan_order" value="<?php echo rawurlencode( json_encode( $plan_order ) ); ?>"/>
					</td>
				</tr>
			</table>
		</div>
		<div id="create-subscription-form-tab-finance" class="wpfs-tab-content">
			<?php include('create_subscription_form_tab_finance.php'); ?>
		</div>
		<div id="create-subscription-form-tab-appearance" class="wpfs-tab-content">
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label class="control-label" for=""><?php esc_html_e( 'Plan Selector Style: ', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
					</th>
					<td>
						<select name="plan_selector_style">
							<option value="<?php echo MM_WPFS::PLAN_SELECTOR_STYLE_DROPDOWN; ?>"><?php esc_html_e( 'Dropdown', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
							<option value="<?php echo MM_WPFS::PLAN_SELECTOR_STYLE_LIST; ?>"><?php esc_html_e( 'List', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
						</select>

						<p class="description"><?php esc_html_e( 'Style of the plan selector component.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label class="control-label"><?php esc_html_e( 'Subscribe Button Text:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
					</th>
					<td>
						<input type="text" class="regular-text" name="form_button_text" id="form_button_text" value="Subscribe" maxlength="<?php echo $form_data::BUTTON_TITLE_LENGTH; ?>">
						<p class="description"><?php esc_html_e( 'The text on the subscribe button.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
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
		<div id="create-subscription-form-tab-custom-fields" class="wpfs-tab-content">
			<?php include('create_payment_form_tab_custom_fields.php'); ?>
		</div>
		<div id="create-subscription-form-tab-actions-after-payment" class="wpfs-tab-content">
			<?php include('create_payment_form_tab_actions_after_payment.php'); ?>
		</div>
		<p class="submit">
			<button class="button button-primary" type="submit"><?php esc_html_e( 'Create Form', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></button>
			<a href="<?php echo admin_url( 'admin.php?page=fullstripe-subscriptions&tab=forms' ); ?>" class="button"><?php esc_html_e( 'Cancel', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
			<img src="<?php echo MM_WPFS_Assets::images( 'loader.gif' ); ?>" alt="<?php esc_attr_e( 'Loading...', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" class="showLoading"/>
		</p>
	</form>
<?php endif; ?>
