<?php

$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'subscribers';

global $wpdb;

//Load based on what tab we have open
/** @var $plans array */
$plans                       = array();
$subscription_forms          = array();
$checkout_subscription_forms = array();
if ( $active_tab == 'forms' ) {
	$subscription_forms          = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "fullstripe_subscription_forms;" );
	$checkout_subscription_forms = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "fullstripe_checkout_subscription_forms;" );
} else if ( $active_tab == 'plans' || $active_tab == 'createform' ) {
	$plans = MM_WPFS::getInstance()->get_plans();
}
?>
<div class="wrap">
	<h2> <?php esc_html_e( 'Full Stripe Subscriptions', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </h2>
	<div id="updateDiv"><p><strong id="updateMessage"></strong></p></div>
	<h2 class="nav-tab-wrapper">
		<a href="<?php echo admin_url( 'admin.php?page=fullstripe-subscriptions&tab=subscribers' ); ?>" class="nav-tab <?php echo $active_tab == 'subscribers' ? 'nav-tab-active' : ''; ?>">
			<?php esc_html_e( 'Subscribers', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
		</a>
		<a href="<?php echo admin_url( 'admin.php?page=fullstripe-subscriptions&tab=forms' ); ?>" class="nav-tab <?php echo $active_tab == 'forms' ? 'nav-tab-active' : ''; ?>">
			<?php esc_html_e( 'Subscription Forms', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
		</a>
		<a href="<?php echo admin_url( 'admin.php?page=fullstripe-subscriptions&tab=plans' ); ?>" class="nav-tab <?php echo $active_tab == 'plans' ? 'nav-tab-active' : ''; ?>">
			<?php esc_html_e( 'Subscription Plans', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
		</a>
	</h2>
	<div class="wpfs-tab-content">
		<?php if ( $active_tab == 'subscribers' ): ?>
			<div class="" id="subscribers">
				<h2>
					<img src="<?php echo MM_WPFS_Assets::images( 'loader.gif' ); ?>" alt="<?php esc_attr_e( 'Loading...', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" class="showLoading"/>
				</h2>
				<form method="get">
					<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
					<label><?php _e( 'Subscriber: ', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label><input type="text" name="subscriber" size="35" placeholder="<?php _e( 'Enter name, email address, or stripe ID', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" value="<?php echo isset( $_REQUEST['subscriber'] ) ? $_REQUEST['subscriber'] : ''; ?>">
					<label><?php _e( 'Subscription: ', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label><input type="text" name="subscription" placeholder="<?php _e( 'Enter subscription ID', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" value="<?php echo isset( $_REQUEST['subscription'] ) ? $_REQUEST['subscription'] : ''; ?>">
					<label><?php _e( 'Mode: ', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
					<select name="mode">
						<option value="" <?php echo ! isset( $_REQUEST['mode'] ) || $_REQUEST['mode'] == '' ? 'selected' : ''; ?>><?php _e( 'All', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
						<option value="live" <?php echo isset( $_REQUEST['mode'] ) && $_REQUEST['mode'] == 'live' ? 'selected' : ''; ?>><?php _e( 'Live', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
						<option value="test" <?php echo isset( $_REQUEST['mode'] ) && $_REQUEST['mode'] == 'test' ? 'selected' : ''; ?>><?php _e( 'Test', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
					</select>
					<span class="wpfs-search-actions">
						<button class="button button-primary"><?php _e( 'Search', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></button> <?php _e( 'or', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
						<a href="<?php echo admin_url( 'admin.php?page=fullstripe-subscriptions' ); ?>"><?php _e( 'Reset', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
					</span>
					<?php
					/** @var WP_List_Table $subscribersTable */
					$subscribersTable->prepare_items();
					$subscribersTable->display();
					?>
				</form>
			</div>
		<?php elseif ( $active_tab == 'forms' ): ?>
			<div class="" id="wpfs-subscription-forms">
				<div style="min-height: 200px;">
					<h2><?php esc_html_e( 'Your Inline Forms', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
						<a class="page-title-action" href="<?php echo add_query_arg(
							array(
								'page' => 'fullstripe-create-form',
								'type' => 'subscription'
							),
							admin_url( "admin.php" )
						); ?>" title="<?php esc_attr_e( 'Create Inline Form', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>"><i class="fa fa-plus fa-fw"></i><?php esc_html_e( 'Create Inline Form', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
						</a>
						<img src="<?php echo MM_WPFS_Assets::images( 'loader.gif' ); ?>" alt="<?php esc_attr_e( 'Loading...', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" class="showLoading"/>
					</h2>
					<?php if ( count( $subscription_forms ) === 0 ): ?>
						<p class="alert alert-info">
							<?php esc_html_e( "No inline subscription forms created yet. Use the 'Create Inline Form' button to get started.", MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
						</p>
					<?php else: ?>
						<table class="wp-list-table widefat fixed subscription-forms">
							<thead>
							<tr>
								<th class="manage-column column-action column-primary"><?php esc_html_e( 'Actions', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
								<th class="manage-column column-name"><?php esc_html_e( 'Name', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
								<th class="manage-column column-plan_ids"><?php esc_html_e( 'Plan IDs', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
								<th class="manage-column column-vat-rate"><?php esc_html_e( 'VAT rate', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
							</tr>
							</thead>
							<tbody id="subscriptionFormsTable">
							<?php foreach ( $subscription_forms as $subscription_form ): ?>
								<?php
								$plans_label = null;
								$sf_plans    = json_decode( $subscription_form->plans );
								if ( json_last_error() == JSON_ERROR_NONE ) {
									$plans_label = implode( ', ', $sf_plans );
								}
								?>
								<tr>
									<td class="column-action">
										<?php
										$shortcode = MM_WPFS_Utils::createShortCodeString( $subscription_form );
										?>
										<span id="shortcode-subscription-tooltip__<?php echo $subscription_form->subscriptionFormID; ?>" class="shortcode-tooltip" data-shortcode="<?php echo esc_attr( $shortcode ); ?>"></span>
										<a id="shortcode-subscription__<?php echo $subscription_form->subscriptionFormID; ?>" class="button button-primary shortcode-subscription" data-form-id="<?php echo $subscription_form->subscriptionFormID; ?>" title="<?php _e( 'Shortcode', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>">
											<i class="fa fa-code fa-fw"></i>
										</a>
										<a class="button button-primary" href="<?php echo add_query_arg(
											array(
												'page' => 'fullstripe-edit-form',
												'form' => $subscription_form->subscriptionFormID,
												'type' => 'subscription'
											),
											admin_url( "admin.php" )
										); ?>" title="<?php _e( 'Edit', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>"><i class="fa fa-pencil fa-fw"></i></a>
										<span class="form-action-last">
											<button class="button delete" data-id="<?php echo $subscription_form->subscriptionFormID; ?>" data-type="subscriptionForm" title="<?php _e( 'Delete', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>">
												<i class="fa fa-trash-o fa-fw"></i>
											</button>
										</span>
									</td>
									<td class="column-name"><?php echo esc_html( $subscription_form->name ); ?></td>
									<td class="column-plan_ids"><?php echo esc_html( $plans_label ); ?></td>
									<td class="column-vat-rate">
										<?php
										if ( MM_WPFS::VAT_RATE_TYPE_NO_VAT == $subscription_form->vatRateType ) {
											$vatRateLabel = __( 'No VAT', MM_WPFS::L10N_DOMAIN_ADMIN );
										} elseif ( MM_WPFS::VAT_RATE_TYPE_FIXED_VAT == $subscription_form->vatRateType ) {
											$vatRateLabel = sprintf( '%s%%', round( $subscription_form->vatPercent, 4 ) );
										} elseif ( MM_WPFS::VAT_RATE_TYPE_CUSTOM_VAT == $subscription_form->vatRateType ) {
											$vatRateLabel = __( 'Custom VAT', MM_WPFS::L10N_DOMAIN_ADMIN );
										} else {
											$vatRateLabel = __( 'Unknown VAT rate Type', MM_WPFS::L10N_DOMAIN_ADMIN );
										}
										echo esc_html( $vatRateLabel );
										?>
									</td>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>
				</div>
				<div style="min-height: 200px;">
					<h2><?php esc_html_e( 'Your Checkout Forms', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
						<a class="page-title-action" href="<?php echo add_query_arg(
							array(
								'page' => 'fullstripe-create-form',
								'type' => 'checkout-subscription'
							),
							admin_url( "admin.php" )
						); ?>" title="<?php esc_attr_e( 'Create Checkout Form', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>"><i class="fa fa-plus fa-fw"></i><?php esc_html_e( 'Create Checkout Form', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
						</a>
						<img src="<?php echo MM_WPFS_Assets::images( 'loader.gif' ); ?>" alt="<?php esc_attr_e( 'Loading...', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" class="showLoading"/>
					</h2>
					<?php if ( count( $checkout_subscription_forms ) === 0 ): ?>
						<p class="alert alert-info">
							<?php esc_html_e( "No checkout subscription forms created yet. Use the 'Create Checkout Form' button to get started.", MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
						</p>
					<?php else: ?>
						<table class="wp-list-table widefat fixed subscription-forms">
							<thead>
							<tr>
								<th class="manage-column column-action column-primary"><?php esc_html_e( 'Actions', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
								<th class="manage-column column-name"><?php esc_html_e( 'Name', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
								<th class="manage-column column-plan_ids"><?php esc_html_e( 'Plan IDs', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
								<th class="manage-column column-vat-rate"><?php esc_html_e( 'VAT rate', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
							</tr>
							</thead>
							<tbody id="subscriptionFormsTable">
							<?php foreach ( $checkout_subscription_forms as $checkout_subscription_form ): ?>
								<?php
								$plans_label = null;
								$scf_plans   = json_decode( $checkout_subscription_form->plans );
								if ( json_last_error() == JSON_ERROR_NONE ) {
									$plans_label = implode( ', ', $scf_plans );
								}
								?>
								<tr>
									<td class="column-action">
										<?php
										$shortcode = MM_WPFS_Utils::createShortCodeString( $checkout_subscription_form );
										?>
										<span id="shortcode-checkout-subscription-tooltip__<?php echo $checkout_subscription_form->checkoutSubscriptionFormID; ?>" class="shortcode-tooltip" data-shortcode="<?php echo esc_attr( $shortcode ); ?>"></span>
										<a id="shortcode-checkout-subscription__<?php echo $checkout_subscription_form->checkoutSubscriptionFormID; ?>" class="button button-primary shortcode-checkout-subscription" data-form-id="<?php echo $checkout_subscription_form->checkoutSubscriptionFormID; ?>" title="<?php _e( 'Shortcode', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>">
											<i class="fa fa-code fa-fw"></i>
										</a>
										<a class="button button-primary" href="<?php echo add_query_arg(
											array(
												'page' => 'fullstripe-edit-form',
												'form' => $checkout_subscription_form->checkoutSubscriptionFormID,
												'type' => 'checkout-subscription'
											),
											admin_url( "admin.php" )
										); ?>" title="<?php _e( 'Edit', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>"><i class="fa fa-pencil fa-fw"></i></a>
										<span class="form-action-last">
											<button class="button delete" data-id="<?php echo $checkout_subscription_form->checkoutSubscriptionFormID; ?>" data-type="checkoutSubscriptionForm" title="<?php _e( 'Delete', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>">
												<i class="fa fa-trash-o fa-fw"></i>
											</button>
										</span>
									</td>
									<td class="column-name"><?php echo esc_html( $checkout_subscription_form->name ); ?></td>
									<td class="column-plan_ids"><?php echo esc_html( $plans_label ); ?></td>
									<td class="column-vat-rate">
										<?php
										$vatRateLabel = __( 'No VAT (Not supported)', MM_WPFS::L10N_DOMAIN_ADMIN );
										echo esc_html( $vatRateLabel );
										?>
									</td>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>
				</div>
			</div>
		<?php elseif ( $active_tab == 'plans' ): ?>
			<div class="" id="plans">
				<h2>
					<?php esc_html_e( 'Your Subscription Plans', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					<a class="page-title-action" href="<?php echo add_query_arg(
						array(
							'page' => 'fullstripe-create-plan'
						),
						admin_url( "admin.php" )
					); ?>" title="<?php esc_attr_e( 'Create Subscription Plan', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>"><i class="fa fa-plus fa-fw"></i><?php esc_html_e( 'Create Subscription Plan', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</a>
					<img src="<?php echo MM_WPFS_Assets::images( 'loader.gif' ); ?>" alt="<?php esc_attr_e( 'Loading...', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" class="showLoading"/>
				</h2>
				<?php if ( count( $plans ) === 0 ): ?>
					<p class="alert alert-info">
						<?php esc_html_e( 'You have no subscription plans created yet. Use the Subscription Plans tab to get started', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</p>
				<?php else: ?>
					<table class="wp-list-table widefat fixed subscription-plans">
						<thead>
						<tr>
							<th class="manage-column column-action column-primary"><?php esc_html_e( 'Actions', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
							<th class="manage-column column-id_display_name"><?php esc_html_e( 'ID', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
								/ <?php esc_html_e( 'Display Name', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
							<th class="manage-column column-amount_interval"><?php esc_html_e( 'Amount', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
								/ <?php esc_html_e( 'Setup Fee', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
							<th class="manage-column column-trial_duration"><?php esc_html_e( 'Trial', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
								/ <?php esc_html_e( 'Duration', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
						</tr>
						</thead>
						<tbody id="plansTable">
						<?php foreach ( $plans as $plan ): ?>
							<?php
							$setup_fee      = MM_WPFS_Utils::get_setup_fee_for_plan( $plan );
							$interval_label = MM_WPFS_Admin::formatIntervalLabelAdmin( $plan->interval, $plan->interval_count );
							?>
							<tr>
								<td class="column-action">
									<a class="button button-primary" href="<?php echo add_query_arg( array(
										'page' => 'fullstripe-edit-plan',
										'plan' => rawurlencode( $plan->id )
									), admin_url( "admin.php" ) ); ?>" title="<?php _e( 'Edit', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>"><i class="fa fa-pencil fa-fw"></i></a>
									<span class="form-action-last">
										<button class="button delete" data-id="<?php echo esc_attr( $plan->id ); ?>" data-type="subscriptionPlan" data-confirm="true" title="<?php _e( 'Delete', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>">
											<i class="fa fa-trash-o fa-fw"></i>
										</button>
									</span>
								</td>
								<td class="column-id_display_name">
									<b><?php echo esc_html( $plan->id ); ?></b><br>
									<a href="<?php echo add_query_arg( array(
										'page' => 'fullstripe-edit-plan',
										'plan' => rawurlencode( $plan->id )
									), admin_url( 'admin.php' ) ); ?>"><?php echo esc_html( $plan->product->name ); ?></a>
								</td>
								<td class="column-amount_interval">
									<b><?php
										$amount_label    = MM_WPFS_Utils::formatPlanAmountForPlanList( $plan );
										$setup_fee_label = isset( $setup_fee ) ? MM_WPFS_Currencies::formatAndEscape( $plan->currency, $setup_fee ) : '';
										echo $amount_label; ?>
										/ <?php echo esc_html( $interval_label ); ?></b><br>
									<?php if ( $setup_fee > 0 ): ?>
										<?php echo $setup_fee_label; ?>
									<?php else: ?>
										<?php esc_html_e( 'No setup fee', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
									<?php endif; ?>
								</td>
								<td class="column-trial_duration">
									<?php
									if ( isset( $plan->trial_period_days ) ) {
										echo esc_html( sprintf( _n( "%d day", "%d days", $plan->trial_period_days, MM_WPFS::L10N_DOMAIN_ADMIN ), $plan->trial_period_days ) );
									} else {
										esc_html_e( 'No Trial', MM_WPFS::L10N_DOMAIN_ADMIN );
									}
									?><br>
									<?php
									$duration           = __( 'Forever', MM_WPFS::L10N_DOMAIN_ADMIN );
									$cancellation_count = MM_WPFS_Utils::get_cancellation_count_for_plan( $plan );
									if ( $cancellation_count > 0 ) {
										$duration = sprintf( _n( '%d charge', '%d charges', $cancellation_count, MM_WPFS::L10N_DOMAIN_ADMIN ), $cancellation_count );
									}
									echo esc_html( $duration );
									?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>