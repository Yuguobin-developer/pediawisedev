<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2017.03.28.
 * Time: 14:08
 */
?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'Send Email Receipt?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
		</th>
		<td>
			<label class="radio inline">
				<input type="radio" name="form_send_email_receipt" value="0" checked="checked">
				<?php esc_html_e( 'No', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			</label>
			<label class="radio inline">
				<input type="radio" name="form_send_email_receipt" value="1">
				<?php esc_html_e( 'Yes', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			</label>
			<p class="description"><?php esc_html_e( 'Send an email receipt on successful payment?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'Redirect On Success?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
		</th>
		<td>
			<label class="radio inline">
				<input type="radio" name="form_do_redirect" id="do_redirect_no" value="0" checked="checked">
				<?php esc_html_e( 'No', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			</label>
			<label class="radio inline">
				<input type="radio" name="form_do_redirect" id="do_redirect_yes" value="1">
				<?php esc_html_e( 'Yes', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			</label>
			<p class="description"><?php esc_html_e( 'When payment is successful you can choose to redirect to another page or post.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
		</td>
	</tr>
	<?php include('redirect_to_for_create.php'); ?>
</table>
