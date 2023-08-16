<?php

/* @var $plan \StripeWPFS\Plan */
$plan      = null;
$setup_fee = 0;

$plan_id = '';
if ( isset( $_GET['plan'] ) ) {
	$plan_id = $_GET['plan'];
}

$valid = false;
if ( $plan_id == '' ) {
	$valid = false;
} else {
	$plan_id = stripslashes( $plan_id );
	$plan    = MM_WPFS::getInstance()->get_plan( $plan_id );
	if ( ! is_null( $plan ) ) {
		$valid     = true;
		$setup_fee = MM_WPFS_Utils::get_setup_fee_for_plan( $plan );
	} else {
		$valid = false;
	}
}

?>
<div class="wrap">
	<h2><?php esc_html_e( 'Modify subscription plan', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></h2>

	<div id="updateDiv"><p><strong id="updateMessage"></strong></p></div>
	<?php if ( ! $valid ): ?>
		<p><?php esc_html_e( 'Plan not found!', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
	<?php else: ?>
		<form class="form-horizontal" action="" method="POST" id="edit-subscription-plan">
			<p class="tips"></p>
			<input type="hidden" name="action" value="wp_full_stripe_edit_subscription_plan"/>
			<input type="hidden" name="plan" value="<?php echo esc_attr( $plan->id ); ?>">
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label class="control-label"><?php esc_html_e( 'ID:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
					</th>
					<td>
						<?php echo esc_html( $plan->id ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label class="control-label"><?php esc_html_e( 'Price:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
					</th>
					<td>
						<?php
						$price_label = '';
						if ( $plan->interval_count === 1 ) {
							$price_label = sprintf( '%s / %s', MM_WPFS_Currencies::format( $plan->currency, $plan->amount ), $plan->interval );
						} else {
                            if ( $plan->interval === 'day' ) {
                                $price_label = sprintf( _n( '%s / %d day', '%s / %d days', $plan->interval_count, MM_WPFS::L10N_DOMAIN_ADMIN ), MM_WPFS_Currencies::format( $plan->currency, $plan->amount ), $plan->interval_count );
							} else if ( $plan->interval === 'week' ) {
								$price_label = sprintf( _n( '%s / %d week', '%s / %d weeks', $plan->interval_count, MM_WPFS::L10N_DOMAIN_ADMIN ), MM_WPFS_Currencies::format( $plan->currency, $plan->amount ), $plan->interval_count );
							} else if ( $plan->interval === 'month' ) {
								$price_label = sprintf( _n( '%s / %d month', '%s / %d months', $plan->interval_count, MM_WPFS::L10N_DOMAIN_ADMIN ), MM_WPFS_Currencies::format( $plan->currency, $plan->amount ), $plan->interval_count );
							} else if ( $plan->interval === 'year' ) {
								$price_label = sprintf( _n( '%s / %d year', '%s / %d years', $plan->interval_count, MM_WPFS::L10N_DOMAIN_ADMIN ), MM_WPFS_Currencies::format( $plan->currency, $plan->amount ), $plan->interval_count );
							}
						}
						echo esc_html( $price_label );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label class="control-label"><?php esc_html_e( 'Setup Fee:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
					</th>
					<td>
						<input type="text" class="regular-text" name="plan_setup_fee" id="form_plan_setup_fee" value="<?php echo esc_attr( $setup_fee ); ?>">
						<p class="description"><?php esc_html_e( 'Amount to charge the customer to setup the subscription, in the smallest unit for the currency. i.e. for $10.00 enter 1000, for ¥10 enter 10. Entering 0 will disable.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label class="control-label"><?php esc_html_e( 'Duration:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
					</th>
					<td>
						<?php
						$duration = __( 'Forever', MM_WPFS::L10N_DOMAIN_ADMIN );
						if ( isset( $plan->metadata ) ) {
							if ( isset( $plan->metadata->cancellation_count ) ) {
								if ( is_numeric( $plan->metadata->cancellation_count ) ) {
									$cancellation_count = intval( $plan->metadata->cancellation_count );
									if ( $cancellation_count > 0 ) {
										$cardinality = $cancellation_count;
										if ( isset( $plan->interval_count ) && is_numeric( $plan->interval_count ) ) {
											$cardinality = intval( $plan->interval_count ) * $cardinality;
										}
										$by_interval = null;
                                        if ( $plan->interval === 'day' ) {
                                            $by_interval = sprintf( _n( '%d day', '%d days', $cardinality, MM_WPFS::L10N_DOMAIN_ADMIN ), $cardinality );
                                        } else if ( $plan->interval === 'week' ) {
											$by_interval = sprintf( _n( '%d week', '%d weeks', $cardinality, MM_WPFS::L10N_DOMAIN_ADMIN ), $cardinality );
										} else if ( $plan->interval === 'month' ) {
											$by_interval = sprintf( _n( '%d month', '%d months', $cardinality, MM_WPFS::L10N_DOMAIN_ADMIN ), $cardinality );
										} else if ( $plan->interval === 'year' ) {
											$by_interval = sprintf( _n( '%d year', '%d years', $cardinality, MM_WPFS::L10N_DOMAIN_ADMIN ), $cardinality );
										}
										if ( empty( $by_interval ) ) {
											$duration = sprintf( _n( '%d charge', '%d charges', $cancellation_count, MM_WPFS::L10N_DOMAIN_ADMIN ), $cancellation_count );
										} else {
											$duration = sprintf( _n( '%d charge (%s)', '%d charges (%s)', $cancellation_count, MM_WPFS::L10N_DOMAIN_ADMIN ), $cancellation_count, $by_interval );
										}
									}
								} else {
									$duration = $plan->metadata->cancellation_count;
								}
							}
						}
						echo esc_html( $duration );
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label class="control-label"><?php esc_html_e( 'Trial:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
					</th>
					<td>
						<?php echo isset( $plan->trial_period_days ) ? esc_html( $plan->trial_period_days . ' days' ) : __( 'No trial', MM_WPFS::L10N_DOMAIN_ADMIN ) ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label class="control-label"><?php esc_html_e( 'Display name:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>*</label>
					</th>
					<td>
						<input type="text" class="regular-text" name="plan_display_name" id="form_plan_display_name" value="<?php echo esc_attr( $plan->product->name ); ?>"/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label class="control-label"><?php esc_html_e( 'Statement descriptor:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
					</th>
					<td>
						<input type="text" class="regular-text" name="plan_statement_descriptor" id="form_plan_statement_descriptor" value="<?php echo esc_attr( $plan->product->statement_descriptor ); ?>" maxlength="<?php echo WPFS_PlanValidationData::STATEMENT_DESCRIPTOR_LENGTH; ?>"/>
					</td>
				</tr>
			</table>

			<p class="submit">
				<button class="button button-primary" type="submit"><?php esc_html_e( 'Modify plan', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></button>
				<a href="<?php echo admin_url( 'admin.php?page=fullstripe-subscriptions&tab=plans' ); ?>" class="button"><?php esc_html_e( 'Cancel', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
				<img src="<?php echo MM_WPFS_Assets::images( 'loader.gif' ); ?>" alt="<?php esc_attr_e( 'Loading...', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" class="showLoading"/>
			</p>

		</form>
	<?php endif; ?>
</div>