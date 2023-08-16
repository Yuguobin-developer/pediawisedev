<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2017.08.25.
 * Time: 15:30
 */
?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'VAT Rate:' ); ?></label>
		</th>
		<td>
			<select id="formVATRateTypeSelect" name="form_vat_rate_type">
				<?php
				$vatRateTypeValues = MM_WPFS_Admin::get_vat_rate_type_values();
				foreach ( $vatRateTypeValues as $vatRateId => $vatRateLabel ) {
					$optionRow = '<option';
					$optionRow .= ' value="' . esc_attr( $vatRateId ) . '"';
					$optionRow .= '>';
					$optionRow .= esc_html( $vatRateLabel );
					$optionRow .= '</option>';
					echo $optionRow;
				}
				?>
			</select>
			<p class="description"><?php esc_html_e( 'Should this form add VAT to the subscription plan amount and to the setup fee?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
		</td>
	</tr>
	<tr id="formVATPercentRow" valign="top" style="display: none;">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'VAT Percent:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
		</th>
		<td>
			<input type="text" class="wpfs-vat-percent" name="form_vat_percent" id="form_vat_percent">%
			<p class="description"><?php esc_html_e( 'VAT Percent with up to 4 decimal places.' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'Collect Billing Address?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
		</th>
		<td>
			<label class="radio inline">
				<input type="radio" name="form_show_address_input" id="hide_address_input" value="0" checked="checked">
				<?php esc_html_e( 'No', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			</label>
			<label class="radio inline">
				<input type="radio" name="form_show_address_input" id="show_address_input" value="1">
				<?php esc_html_e( 'Yes', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			</label>
			<p class="description"><?php esc_html_e( 'Should this form also ask for the customers\' billing address?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
		</td>
	</tr>
	<tr id="defaultBillingCountryRow" valign="top" style="display: none;">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'Default Billing Country:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
		</th>
		<td>
			<select name="form_default_billing_country" class="fullstripe-form-input input-xlarge">
				<?php
				foreach ( MM_WPFS_Countries::get_available_countries() as $countryKey => $countryObject ) {
					$option = '<option';
					$option .= " value=\"{$countryKey}\"";
					if ( $countryKey == MM_WPFS::DEFAULT_BILLING_COUNTRY_INITIAL_VALUE ) {
						$option .= ' selected="selected"';
					}
					$option .= '>';
					$option .= MM_WPFS_Admin::translateLabelAdmin($countryObject['name']);
					$option .= '</option>';
					echo $option;
				}
				?>
			</select>
			<p class="description"><?php esc_html_e( "It's the selected country when the form is rendered for the first time, and is used also as the supplier's country for custom VAT calculation.", MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'Collect Shipping Address?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
		</th>
		<td>
			<label class="radio inline">
				<input type="radio" name="form_show_shipping_address_input" id="hide_shipping_address_input" value="0" checked="checked">
				<?php esc_html_e( 'No', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			</label>
			<label class="radio inline">
				<input type="radio" name="form_show_shipping_address_input" id="show_shipping_address_input" value="1">
				<?php esc_html_e( 'Yes', 'wp-full-stripe-admin-admin' ); ?>
			</label>
			<p class="description"><?php esc_html_e( 'Should this form also ask for the customers\' shipping address?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
		</td>
	</tr>
</table>
