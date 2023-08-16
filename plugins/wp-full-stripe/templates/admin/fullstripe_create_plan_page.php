<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2017.02.27.
 * Time: 16:39
 */
?>
<div id="createplan" class="wrap">
	<h2><?php esc_html_e( 'Create subscription plan', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></h2>
	<div id="updateDiv"><p><strong id="updateMessage"></strong></p></div>
	<form class="form-horizontal" action="" method="POST" id="create-subscription-plan">
		<p class="tips"></p>
		<input type="hidden" name="action" value="wp_full_stripe_create_plan"/>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label class="control-label"><?php esc_html_e( 'ID:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
				</th>
				<td>
					<input type="text" class="regular-text" name="sub_id" id="sub_id">
					<p class="description"><?php esc_html_e( 'This ID is used to identify this plan when creating a subscription form and on your Stripe dashboard.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label class="control-label"><?php esc_html_e( 'Display Name:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
				</th>
				<td>
					<input type="text" class="regular-text" name="sub_name" id="sub_name">
					<p class="description"><?php esc_html_e( 'The name you wish to be displayed to customers for this plan.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label class="control-label" for="currency"><?php _e( "Payment Currency: ", MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
				</th>
				<td>
					<div class="ui-widget">
						<select id="currency" name="sub_currency">
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
			<tr valign="top">
				<th scope="row">
					<label class="control-label"><?php esc_html_e( 'Payment Amount:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
				</th>
				<td>
					<input type="text" class="regular-text" name="sub_amount" id="sub_amount"/>
					<p class="description"><?php esc_html_e( 'The amount this plan will charge your customer, in the smallest unit for the currency. i.e. for $10.00 enter 1000, for ¥10 enter 10.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label class="control-label"><?php esc_html_e( 'Setup Fee:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
				</th>
				<td>
					<input type="text" class="regular-text" name="sub_setup_fee" id="sub_setup_fee" value="0">
					<p class="description"><?php esc_html_e( 'Amount to charge the customer to setup the subscription, in the smallest unit for the currency. i.e. for $10.00 enter 1000, for ¥10 enter 10. Entering 0 will disable.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label class="control-label"><?php esc_html_e( 'Payment Interval:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
				</th>
				<td>
					<select id="sub_interval" name="sub_interval">
                        <option value="day"><?php esc_html_e( 'Daily', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
						<option value="week"><?php esc_html_e( 'Weekly', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
						<option value="month" selected="selected"><?php esc_html_e( 'Monthly', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
						<option value="year"><?php esc_html_e( 'Yearly', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'How often the payment amount is charged to the customer.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label class="control-label"><?php esc_html_e( 'Payment Interval Count:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
				</th>
				<td>
					<input type="text" class="regular-text" name="sub_interval_count" id="sub_interval_count" value="1"/>
					<p class="description"><?php esc_html_e( 'You could specify an interval count of 3 and an interval of \'Monthly\' for quarterly billing (every 3 months). Default is 1 for Daily/Weekly/Monthly/Yearly.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label class="control-label"><?php esc_html_e( 'Payment Cancellation Count:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
				</th>
				<td>
					<input type="text" class="regular-text" name="sub_cancellation_count" id="sub_cancellation_count" value="0"/>
					<p class="description"><?php esc_html_e( 'You could specify the number of charges after which the subscription is cancelled. Set to 0 to let the subscription run forever.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label class="control-label"><?php esc_html_e( 'Trial Period Days:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
				</th>
				<td>
					<input type="text" class="regular-text" name="sub_trial" id="sub_trial" value="0"/>
					<p class="description"><?php esc_html_e( 'How many trial days the customer has before being charged. Set to 0 to disable trial period.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
				</td>
			</tr>
		</table>
		<p class="submit">
			<button class="button button-primary" type="submit"><?php esc_html_e( 'Create Plan', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></button>
			<a href="<?php echo admin_url( 'admin.php?page=fullstripe-subscriptions&tab=plans' ); ?>" class="button"><?php esc_html_e( 'Cancel', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
			<img src="<?php echo MM_WPFS_Assets::images( 'loader.gif' ); ?>" alt="<?php esc_attr_e( 'Loading...', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" class="showLoading"/>
		</p>
	</form>
</div>
