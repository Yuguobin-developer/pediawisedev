<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2016.04.13.
 * Time: 15:28
 */

$options                 = get_option( 'fullstripe_options' );
$wpfs_main               = MM_WPFS::getInstance();
$email_receipt_templates = $wpfs_main->getAdmin()->get_email_receipt_templates();

?>
<div id="email-receipts-tab">
	<form class="form-horizontal" action="#" method="post" id="settings-email-receipts-form">
		<p class="tips"></p>
		<input type="hidden" name="action" value="wp_full_stripe_update_settings"/>
		<input type="hidden" name="tab" value="email-receipts">
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label class="control-label"><?php _e( "Receipt Email Type: ", MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
				</th>
				<td>
					<label class="radio">
						<input type="radio" name="receiptEmailType" id="receiptEmailTypePlugin" value="plugin" <?php echo ( $options['receiptEmailType'] == 'plugin' ) ? 'checked' : '' ?> >
						<?php _e( 'Plugin', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</label>
					<label class="radio">
						<input type="radio" name="receiptEmailType" id="receiptEmailTypeStripe" value="stripe" <?php echo ( $options['receiptEmailType'] == 'stripe' ) ? 'checked' : '' ?>>
						<?php _e( 'Stripe', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</label>

					<p class="description"><?php _e( 'Choose the type of payment receipt emails. Plugin emails are defined below and Stripe emails can be setup in your Stripe Dashboard.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
				</td>
			</tr>
			<tr id="email_receipt_row" valign="top" <?php echo ( $options['receiptEmailType'] == 'stripe' ) ? 'style="display: none;"' : '' ?>>
				<th scope="row">
					<label class="control-label"><?php _e( "Plugin Email Templates: ", MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
				</th>
				<td>
					<input id="email_receipts" name="email_receipts" type="hidden">
					<table id="email_receipt_templates">
						<tr>
							<td>
								<select id="email_receipt_template" size="20" class="regular-text">
									<?php
									foreach ( $email_receipt_templates as $email_receipt_template ) {
										$an_option = "<option value=\"{$email_receipt_template->id}\">" . esc_html( $email_receipt_template->caption ) . '</option>';
										echo $an_option;
									}
									?>
								</select>
							</td>
							<td>
								<label><?php _e( 'E-mail Subject', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label><br>
								<input id="email_receipt_subject" type="text" class="large-text code"><br>
								<label><?php _e( 'E-mail body (HTML)', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label><br>
								<textarea id="email_receipt_html" class="large-text code" rows="13"></textarea>
								<p class="description"><?php _e( '%CUSTOMERNAME% and %AMOUNT% are replaced with the name of the customer and payment amount, respectively.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
									<?php printf( __( 'See the <a target="_blank" href="%s">Help page</a> for more options.', MM_WPFS::L10N_DOMAIN_ADMIN ), admin_url( "admin.php?page=fullstripe-help#receipt-tokens" ) ); ?></p>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr id="email_receipt_sender_address_row" valign="top" <?php echo ( $options['receiptEmailType'] == 'stripe' ) ? 'style="display: none;"' : '' ?>>
				<th scope="row">
					<label class="control-label" for="email_receipt_sender_address"><?php _e( 'Email Sender Address:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
				</th>
				<td>
					<input id="email_receipt_sender_address" name="email_receipt_sender_address" type="text" class="regular-text" value="<?php echo esc_attr( $options['email_receipt_sender_address'] ); ?>">

					<p class="description"><?php _e( 'The sender address of email receipts. If you leave it empty then the email address of the blog admin will be used.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
				</td>
			</tr>
			<tr id="admin_payment_receipt_row" valign="top" <?php echo ( $options['receiptEmailType'] == 'stripe' ) ? 'style="display: none;"' : '' ?>>
				<th scope="row">
					<label class="control-label"><?php _e( "Send Copy of Emails?: ", MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
				</th>
				<td>
					<label class="radio">
						<input type="radio" name="admin_payment_receipt" id="admin_payment_receipt_no" value="no" <?php echo ( $options['admin_payment_receipt'] == 'no' ) ? 'checked' : '' ?>>
						<?php _e( 'No', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</label>
					<label class="radio">
						<input type="radio" name="admin_payment_receipt" id="admin_payment_receipt_website_admin" value="website_admin" <?php echo ( $options['admin_payment_receipt'] == 'website_admin' ) ? 'checked' : '' ?> >
						<?php _e( 'Yes, to the Website Admin', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</label>
					<label class="radio">
						<input type="radio" name="admin_payment_receipt" id="admin_payment_receipt_sender_address" value="sender_address" <?php echo ( $options['admin_payment_receipt'] == 'sender_address' ) ? 'checked' : '' ?>>
						<?php _e( 'Yes, to the Email Sender Address', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</label>
					<p class="description"><?php _e( 'Send copies of payment/subscription receipts to the website admin as well?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
				</td>
			</tr>
		</table>
		<p class="submit">
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes' ) ?></button>
			<img src="<?php echo MM_WPFS_Assets::images( 'loader.gif' ); ?>" alt="<?php esc_attr_e( 'Loading...', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" class="showLoading"/>
		</p>
	</form>
</div>
