<?php
$roleNames      = MM_WPFS_Members::getInstance()->get_wp_role_names();
$stripePlans    = MM_WPFS_Members::getInstance()->get_stripe_plans_for_active_roles();
$options        = get_option( 'fullstripe_options' );

?>
<div id="wpfs_members_account_page">
	<h2><?php _e( 'My Account', 'wp-full-stripe-members' ); ?></h2>
	<div id="wpfs_members_account_page">
		<?php if ( defined( 'WPFS_MEMBERS_DEMO' ) ): ?>
			<h4><?php _e( 'DEMO VERSION: Updating membership is disabled.', 'wp-full-stripe-members' ); ?></h4>
		<?php endif; ?>
		<div class="_100 wpfs-members-summary">
			<table class="wpfs-members-table">
				<tr>
					<td><?php _e( 'Membership Level', 'wp-full-stripe-members' ); ?>:</td>
					<td class="wpfs-right-align"><?php echo MM_WPFS_Members::resolve_role_by_code( $member->role ); ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Membership Price', 'wp-full-stripe-members' ); ?>:</td>
					<td class="wpfs-right-align">
						<?php
						$price_label = MM_WPFS_Members::getTranslatedPricingLabel(
                            $subscription->plan->currency,
                            $subscription->plan->amount,
                            $subscription->plan->interval,
                            $subscription->plan->interval_count
                        );

						echo esc_html( $price_label );
						?>
					</td>
				</tr>
				<tr>
					<?php if ( $subscription->cancel_at_period_end ): ?>
						<td><?php _e( 'Membership End Date', 'wp-full-stripe-members' ); ?>:</td>
					<?php else: ?>
						<td><?php _e( 'Next Billing Date', 'wp-full-stripe-members' ); ?>:</td>
					<?php endif; ?>
					<td class="wpfs-right-align">
						<?php
						$date_format              = get_option( 'date_format' );
						$time_format              = get_option( 'time_format' );
						$current_period_end_label = date( "$date_format $time_format", $subscription->current_period_end );
						echo esc_html( $current_period_end_label );
						?>
					</td>
				</tr>
				<tr>
					<td><?php _e( 'Membership Status', 'wp-full-stripe-members' ); ?>:</td>
					<td class="wpfs-right-align"><?php echo MM_WPFS_Members::resolve_status_by_code( $subscription->cancel_at_period_end ? MM_WPFS_Members::STATUS_CANCELING : $member->stripeSubscriptionStatus ); ?></td>
				</tr>
			</table>
		</div>
		<?php if ( $options['wpfs_members_allow_change_level'] == 1 && count( $stripePlans ) > 1 ): ?>
			<div class="_100 wpfs-members-summary">
				<p class="tips"></p>
				<form action="" method="POST" id="wpfs_members_change_level">
					<input type="hidden" name="action" value="wpfs_members_change_level"/>
					<input type="hidden" name="memberID" value="<?php echo $member->memberID; ?>"/>
					<table class="wpfs-members-table">
						<tr>
							<td>
								<label for="wpfs_members_level"><?php _e( 'Select New Level', 'wp-full-stripe-members' ); ?>
									:</label>
								<select id="wpfs_members_level" name="wpfs_members_level">
									<?php foreach ( $stripePlans as $role => $plan ): ?>
										<?php
										$currency_array = MM_WPFS::get_currency_for( $plan->currency );
										$setup_fee      = 0;
										if ( isset( $plan->metadata ) && isset( $plan->metadata->setup_fee ) ) {
											$setup_fee = $plan->metadata->setup_fee;
										}
										?>
										<?php if ( $role !== $member->role ): ?>
											<option
												value="<?php echo $plan->id; ?>"
												data-amount="<?php echo $plan->amount; ?>"
												data-interval="<?php echo MM_WPFS_Members::get_translated_interval_label( $plan->interval, $plan->interval_count ); ?>"
												data-interval-count="<?php echo $plan->interval_count; ?>"
												data-role="<?php echo $role; ?>"
												data-currency="<?php echo esc_attr( $plan->currency ); ?>"
												data-zero-decimal-support="<?php echo( $currency_array['zeroDecimalSupport'] == true ? 'true' : 'false' ); ?>"
												data-currency-symbol="<?php echo esc_attr( $currency_array['symbol'] ); ?>"
												data-setup-fee="<?php echo MM_WPFS_Utils::format_amount( $plan->currency, $setup_fee ); ?>"
												data-setup-fee-in-smallest-common-currency="<?php echo $setup_fee; ?>"
											><?php MM_WPFS_Members::echo_translated_label( $roleNames[ $role ] ); ?></option>
										<?php endif; ?>
									<?php endforeach; ?>
									<?php if ( $subscription->cancel_at_period_end ): ?>
										<?php
										$currency_array = MM_WPFS::get_currency_for( $subscription->plan->currency );
										$setup_fee      = 0;
										if ( isset( $subscription->plan->metadata ) && isset( $subscription->plan->metadata->setup_fee ) ) {
											$setup_fee = $subscription->plan->metadata->setup_fee;
										}
										?>
										<option
											value="<?php echo $subscription->plan->id; ?>"
											data-amount="<?php echo $subscription->plan->amount; ?>"
											data-interval="<?php echo MM_WPFS_Members::get_translated_interval_label( $subscription->plan->interval, $subscription->plan->interval_count ); ?>"
											data-interval-count="<?php echo $subscription->plan->interval_count; ?>"
											data-role="<?php echo $member->role; ?>"
											data-currency="<?php echo $subscription->plan->currency; ?>"
											data-zero-decimal-support="<?php echo( $currency_array['zeroDecimalSupport'] == true ? 'true' : 'false' ); ?>"
											data-currency-symbol="<?php echo esc_attr( $currency_array['symbol'] ); ?>"
											data-setup-fee="<?php echo MM_WPFS_Utils::format_amount( $subscription->plan->currency, $setup_fee ); ?>"
											data-setup-fee-in-smallest-common-currency="<?php echo $setup_fee; ?>"
										><?php MM_WPFS_Members::echo_translated_label( $roleNames[ $member->role ] ); ?><?php _e( '(Re-join)', 'wp-full-stripe-members' ); ?></option>
									<?php endif; ?>
								</select>
								<p class="wpfs_members_level_details"></p>
							</td>
							<td class="wpfs-right-align">
								<button type="submit" <?php echo ( defined( 'WPFS_MEMBERS_DEMO' ) ) ? 'disabled="disabled"' : '' ?> ><?php _e( 'Change Membership Level', 'wp-full-stripe-members' ); ?></button>
								<p>
									<small><?php _e( 'Changing membership will prorate subscription costs.', 'wp-full-stripe-members' ); ?></small>
								</p>
								<img src="<?php echo plugins_url( '/assets/images/loader.gif', dirname( __FILE__ ) ); ?>" alt="Loading..." class="showLoading"/>
							</td>
						</tr>
					</table>
				</form>
			</div>
		<?php endif; ?>

		<div class="_100 wpfs-members-summary">
			<p class="update_tips"></p>
			<form action="" method="POST" id="wpfs_members_update_card">
				<input type="hidden" name="action" value="wpfs_members_update_card"/>
				<input type="hidden" name="memberID" value="<?php echo $member->memberID; ?>"/>
				<table class="wpfs-members-table">
					<tr>
						<td>
							<p>
								<small><?php _e( 'You can add new credit card details here. Your old details will be removed and future payments taken from this card.', 'wp-full-stripe-members' ); ?></small>
							</p>
						</td>
						<td class="wpfs-right-align">
							<button id="wpfs_members_update_card_button" <?php echo ( defined( 'WPFS_MEMBERS_DEMO' ) ) ? 'disabled="disabled"' : '' ?> ><?php _e( 'Update Credit Card', 'wp-full-stripe-members' ); ?></button>
						</td>
					</tr>
				</table>
				<table class="wpfs-members-table" id="wpfs_members_update_card_section" style="display: none;">
					<tr>
						<td><?php _e( 'Card Holder\'s Name', 'wp-full-stripe-members' ); ?>:</td>
						<td>
							<input type="text" name="wpfs_members_card_name" id="wpfs_members_card_name" data-stripe="name">
						</td>
					</tr>
					<tr>
						<td><?php _e( 'Card Number', 'wp-full-stripe-members' ); ?>:</td>
						<td><input type="text" autocomplete="off" size="20" data-stripe="number"></td>
					</tr>
					<tr>
						<td><?php _e( 'Card Expiry Month', 'wp-full-stripe-members' ); ?>:</td>
						<td>
							<select data-stripe="exp-month">
								<option value="01"><?php _e( 'January', 'wp-full-stripe-members' ); ?></option>
								<option value="02"><?php _e( 'February', 'wp-full-stripe-members' ); ?></option>
								<option value="03"><?php _e( 'March', 'wp-full-stripe-members' ); ?></option>
								<option value="04"><?php _e( 'April', 'wp-full-stripe-members' ); ?></option>
								<option value="05"><?php _e( 'May', 'wp-full-stripe-members' ); ?></option>
								<option value="06"><?php _e( 'June', 'wp-full-stripe-members' ); ?></option>
								<option value="07"><?php _e( 'July', 'wp-full-stripe-members' ); ?></option>
								<option value="08"><?php _e( 'August', 'wp-full-stripe-members' ); ?></option>
								<option value="09"><?php _e( 'September', 'wp-full-stripe-members' ); ?></option>
								<option value="10"><?php _e( 'October', 'wp-full-stripe-members' ); ?></option>
								<option value="11"><?php _e( 'November', 'wp-full-stripe-members' ); ?></option>
								<option value="12"><?php _e( 'December', 'wp-full-stripe-members' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php _e( 'Card Expiry Year', 'wp-full-stripe-members' ); ?>:</td>
						<td>
							<select data-stripe="exp-year">
								<?php
								$startYear = date( 'Y' );
								$numYears  = 20;
								for ( $i = 0; $i < $numYears; $i ++ ) {
									$yr = $startYear + $i;
									echo "<option value='" . $yr . "'>" . $yr . "</option>";
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Card CVC', 'wp-full-stripe-members' ); ?>:</td>
						<td>
							<input type="text" class="input-mini" autocomplete="off" size="4" maxlength="4" data-stripe="cvc">
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>
							<button type="submit" <?php echo ( defined( 'WPFS_MEMBERS_DEMO' ) ) ? 'disabled="disabled"' : '' ?> ><?php _e( 'Update Credit Card', 'wp-full-stripe-members' ); ?></button>
							<img src="<?php echo plugins_url( '/assets/images/loader.gif', dirname( __FILE__ ) ); ?>" alt="Loading..." class="update_showLoading"/>
						</td>
					</tr>
				</table>
			</form>
		</div>

		<?php if ( $options['wpfs_members_allow_cancel_membership'] == 1 ): ?>
			<div class="_100 wpfs-members-summary">
				<p class="delete_tips"></p>
				<form action="" method="POST" id="wpfs_members_cancel">
					<input type="hidden" name="action" value="wpfs_members_cancel"/>
					<input type="hidden" name="memberID" value="<?php echo $member->memberID; ?>"/>
					<table class="wpfs-members-table">
						<tr>
							<td>
								<p>
									<small><?php _e( 'Your subscription will be cancelled immediately. Any pending proration will be charged within an hour after cancellation.', 'wp-full-stripe-members' ); ?></small>
								</p>
							</td>
							<td class="wpfs-right-align">
								<button id="wpfs_members_cancel_membership" class="wpfs-members-delete-button" <?php echo $subscription->cancel_at_period_end ? 'disabled="disabled"' : '' ?> ><?php _e( 'Cancel Membership', 'wp-full-stripe-members' ); ?></button>
								<img src="<?php echo plugins_url( '/assets/images/loader.gif', dirname( __FILE__ ) ); ?>" alt="Loading..." class="delete_showLoading"/>
							</td>
						</tr>
					</table>
					<table class="wpfs-members-table" id="wpfs_members_cancel_question" style="display: none;">
						<tr>
							<td><?php _e( 'Are you sure you want to cancel your membership?', 'wp-full-stripe-members' ); ?></td>
							<td>
								<button type="submit" <?php echo ( defined( 'WPFS_MEMBERS_DEMO' ) ) ? 'disabled="disabled"' : '' ?> id="wpfs_members_cancel_membership_yes" class="wpfs-members-delete-button"><?php _e( 'Yes, Cancel My Membership', 'wp-full-stripe-members' ); ?></button>
							</td>
							<td class="wpfs-right-align">
								<button id="wpfs_members_cancel_membership_no"><?php _e( 'No, Don\'t Cancel My Membership', 'wp-full-stripe-members' ); ?></button>
							</td>
						</tr>
					</table>
				</form>
			</div>
		<?php endif; ?>
	</div>
</div>
