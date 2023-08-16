<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2017.09.06.
 * Time: 16:30
 */
?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'Collect Billing Address?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
		</th>
		<td>
			<label class="radio inline">
				<input type="radio" name="form_show_address_input" id="hide_address_input" value="0" checked="checked">
				<?php esc_html_e( 'Hide', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			</label>
			<label class="radio inline">
				<input type="radio" name="form_show_address_input" id="show_address_input" value="1">
				<?php esc_html_e( 'Show', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			</label>
			<p class="description"><?php esc_html_e( 'Should this form also ask for the customers billing address?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
		</td>
	</tr>
</table>
