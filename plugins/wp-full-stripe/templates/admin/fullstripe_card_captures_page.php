<?php

global $wpdb;

$payment_forms  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}fullstripe_payment_forms WHERE customAmount=%s;", MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE ) );
$checkout_forms = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}fullstripe_checkout_forms WHERE customAmount=%s;", MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE ) );
$active_tab     = isset( $_GET['tab'] ) ? $_GET['tab'] : 'saved_cards';
$options        = get_option( 'fullstripe_options' );

?>
<div class="wrap">
	<h2> <?php esc_html_e( 'Full Stripe Saved Cards', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </h2>

	<div id="updateDiv"><p><strong id="updateMessage"></strong></p></div>

	<h2 class="nav-tab-wrapper">
		<a href="<?php echo admin_url( 'admin.php?page=fullstripe-saved-cards&tab=saved_cards' ); ?>" class="nav-tab <?php echo $active_tab == 'saved_cards' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Saved Cards', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
		<a href="<?php echo admin_url( 'admin.php?page=fullstripe-saved-cards&tab=forms' ); ?>" class="nav-tab <?php echo $active_tab == 'forms' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Save Card Forms', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
	</h2>

	<div class="wpfs-tab-content">
		<?php if ( $active_tab == 'saved_cards' ): ?>
			<div class="" id="saved_cards">
				<h2>
					<img src="<?php echo MM_WPFS_Assets::images( 'loader.gif' ); ?>" alt="<?php esc_attr_e( 'Loading...', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" class="showLoading"/>
				</h2>
				<form method="get">
					<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
					<input type="hidden" name="tab" value="<?php echo 'saved_cards' ?>"/>
					<label><?php esc_html_e( 'Customer: ', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label><input type="text" name="customer" size="35" placeholder="<?php esc_attr_e( 'Enter name, email address, or stripe ID', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" value="<?php echo isset( $_REQUEST['customer'] ) ? $_REQUEST['customer'] : ''; ?>">
					<label><?php esc_html_e( 'Mode: ', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
					<select name="mode">
						<option value="" <?php echo ! isset( $_REQUEST['mode'] ) || $_REQUEST['mode'] == '' ? 'selected' : ''; ?>><?php esc_html_e( 'All', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
						<option value="live" <?php echo isset( $_REQUEST['mode'] ) && $_REQUEST['mode'] == 'live' ? 'selected' : ''; ?>><?php esc_html_e( 'Live', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
						<option value="test" <?php echo isset( $_REQUEST['mode'] ) && $_REQUEST['mode'] == 'test' ? 'selected' : ''; ?>><?php esc_html_e( 'Test', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
					</select>
					<span class="wpfs-search-actions">
						<button class="button button-primary"><?php esc_html_e( 'Search', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></button> <?php esc_html_e( 'or', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
						<a href="<?php echo admin_url( 'admin.php?page=fullstripe-saved-cards&tab=saved_cards' ); ?>"><?php esc_html_e( 'Reset', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
					</span>
					<?php
					/** @var WPFS_Card_Captures_Table $cardCapturesTable */
					$cardCapturesTable->prepare_items();
					$cardCapturesTable->display();
					?>
				</form>
			</div>
		<?php elseif ( $active_tab == 'forms' ): ?>
			<div class="" id="forms">
				<div style="min-height: 200px;">
					<h2><?php esc_html_e( 'Your Inline Forms for Saving Cards', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
						<a class="page-title-action" href="<?php echo add_query_arg(
							array(
								'page' => 'fullstripe-create-form',
								'type' => 'inline_card_capture'
							),
							admin_url( "admin.php" )
						); ?>" title="<?php esc_attr_e( 'Create Inline Form', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>"><i class="fa fa-plus fa-fw"></i><?php esc_html_e( 'Create Inline Form', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
						</a>
						<img src="<?php echo MM_WPFS_Assets::images( 'loader.gif' ); ?>" alt="<?php esc_attr_e( 'Loading...', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" class="showLoading"/>
					</h2>
					<?php if ( count( $payment_forms ) === 0 ): ?>
						<p class="alert alert-info">
							<?php esc_html_e( "You have created no inline forms for saving cards yet. Use the 'Create Inline Saved Card Form' button to get started.", MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
						</p>
					<?php else: ?>
						<table class="wp-list-table widefat fixed payment-forms">
							<thead>
							<tr>
								<th class="manage-column column-action column-primary"><?php esc_html_e( 'Actions', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
								<th class="manage-column column-name"><?php esc_html_e( 'Name', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
								<th class="manage-column column-amount"><?php esc_html_e( 'Amount', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
							</tr>
							</thead>
							<tbody id="paymentFormsTable">
							<?php foreach ( $payment_forms as $payment_form ): ?>
								<tr>
									<td class="column-action">
										<?php
										$shortcode = MM_WPFS_Utils::createShortCodeString( $payment_form );
										?>
										<span id="shortcode-payment-tooltip__<?php echo $payment_form->paymentFormID; ?>" class="shortcode-tooltip" data-shortcode="<?php echo esc_attr( $shortcode ); ?>"></span>
										<a id="shortcode-payment__<?php echo $payment_form->paymentFormID; ?>" class="button button-primary shortcode-payment" data-form-id="<?php echo $payment_form->paymentFormID; ?>" title="<?php esc_attr_e( 'Shortcode', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>">
											<i class="fa fa-code fa-fw"></i>
										</a>
										<a class="button button-primary" href="<?php echo add_query_arg(
											array(
												'page' => 'fullstripe-edit-form',
												'form' => $payment_form->paymentFormID,
												'type' => 'inline_card_capture'
											),
											admin_url( "admin.php" )
										); ?>" title="<?php esc_attr_e( 'Edit', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>"><i class="fa fa-pencil fa-fw"></i></a>
										<span class="form-action-last">
											<button class="button delete" data-id="<?php echo $payment_form->paymentFormID; ?>" data-type="inlineCardCaptureForm" title="<?php esc_attr_e( 'Delete', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>">
												<i class="fa fa-trash-o fa-fw"></i>
											</button>
										</span>
									</td>
									<td class="column-name"><?php echo esc_html( $payment_form->name ); ?></td>
									<?php if ( $payment_form->customAmount == MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE ): ?>
										<td class="column-amount"><?php esc_html_e( 'Save card only', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></td>
									<?php else: ?>
										<td class="column-amount"><?php esc_html_e( 'Unknown', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></td>
									<?php endif; ?>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>
				</div>
				<div style="min-height: 200px;">
					<h2>
						<?php esc_html_e( 'Your Checkout Forms for Saving Cards', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
						<a class="page-title-action" href="<?php echo add_query_arg(
							array(
								'page' => 'fullstripe-create-form',
								'type' => 'popup_card_capture'
							),
							admin_url( "admin.php" )
						); ?>" title="<?php esc_attr_e( 'Create Checkout Form', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>"><i class="fa fa-plus fa-fw"></i><?php esc_html_e( 'Create Checkout Form', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
						</a>
					</h2>
					<?php if ( count( $checkout_forms ) === 0 ): ?>
						<p class="alert alert-info">
							<?php esc_html_e( "You have created no checkout forms for saving cards yet. Use the 'Create Checkout Save Card Form' button to get started.", MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
						</p>
					<?php else: ?>
						<table class="wp-list-table widefat fixed checkout-forms">
							<thead>
							<tr>
								<th class="manage-column column-action column-primary"><?php esc_html_e( 'Actions', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
								<th class="manage-column column-name"><?php esc_html_e( 'Name', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
								<th class="manage-column column-amount"><?php esc_html_e( 'Amount', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
							</tr>
							</thead>
							<tbody id="checkoutFormsTable">
							<?php foreach ( $checkout_forms as $checkout_form ): ?>
								<tr>
									<td>
										<?php
										$shortcode = MM_WPFS_Utils::createShortCodeString( $checkout_form );
										?>
										<span id="shortcode-checkout-tooltip__<?php echo $checkout_form->checkoutFormID; ?>" class="shortcode-tooltip" data-shortcode="<?php echo esc_attr( $shortcode ); ?>"></span>
										<a id="shortcode-checkout__<?php echo $checkout_form->checkoutFormID; ?>" class="button button-primary shortcode-checkout" data-form-id="<?php echo $checkout_form->checkoutFormID; ?>" title="<?php esc_attr_e( 'Shortcode', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>">
											<i class="fa fa-code fa-fw"></i>
										</a>
										<a class="button button-primary" href="<?php echo add_query_arg(
											array(
												'page' => 'fullstripe-edit-form',
												'form' => $checkout_form->checkoutFormID,
												'type' => 'popup_card_capture'
											),
											admin_url( "admin.php" )
										); ?>" title="<?php esc_attr_e( 'Edit', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>"><i class="fa fa-pencil fa-fw"></i></a>
										<span class="form-action-last">
											<button class="button delete" data-id="<?php echo $checkout_form->checkoutFormID; ?>" data-type="popupCardCaptureForm" title="<?php esc_attr_e( 'Delete', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>">
												<i class="fa fa-trash-o fa-fw"></i>
											</button>
										</span>
									</td>
									<td class="column-name"><?php echo esc_html( $checkout_form->name ); ?></td>
									<?php if ( $checkout_form->customAmount == MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE ): ?>
										<td class="column-amount"><?php esc_html_e( 'Save card only', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></td>
									<?php else: ?>
										<td class="column-amount"><?php esc_html_e( 'Unknown', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></td>
									<?php endif; ?>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
