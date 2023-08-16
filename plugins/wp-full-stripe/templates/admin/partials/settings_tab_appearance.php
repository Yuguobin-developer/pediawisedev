<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2016.04.13.
 * Time: 15:27
 */

$options = get_option( 'fullstripe_options' );

?>
<div id="appearance-tab">
	<form class="form-horizontal" action="#" method="post" id="settings-appearance-form">
		<p class="tips"></p>
		<input type="hidden" name="action" value="wp_full_stripe_update_settings"/>
		<input type="hidden" name="tab" value="appearance">
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label class="control-label"><?php esc_html_e( 'Format decimals with:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
				</th>
				<td>
					<label class="checkbox">
						<input type="radio" name="<?php echo MM_WPFS::OPTION_DECIMAL_SEPARATOR_SYMBOL ?>" id="decimal_separator_dot" value="<?php echo MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT; ?>" <?php echo ( $options[ MM_WPFS::OPTION_DECIMAL_SEPARATOR_SYMBOL ] == MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT ) ? 'checked' : '' ?>>
						<?php esc_html_e( '$10.99 (dot)', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</label>
					<label class="checkbox">
						<input type="radio" name="<?php echo MM_WPFS::OPTION_DECIMAL_SEPARATOR_SYMBOL ?>" id="decimal_separator_comma" value="<?php echo MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_COMMA; ?>" <?php echo ( $options[ MM_WPFS::OPTION_DECIMAL_SEPARATOR_SYMBOL ] == MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_COMMA ) ? 'checked' : '' ?>>
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
						<input type="radio" name="<?php echo MM_WPFS::OPTION_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE ?>" id="use_currency_symbol" value="1" <?php echo ( $options[ MM_WPFS::OPTION_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE ] == '1' ) ? 'checked' : '' ?>>
						<?php esc_html_e( '$10.99 (symbol)', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</label>
					<label class="checkbox">
						<input type="radio" name="<?php echo MM_WPFS::OPTION_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE ?>" id="use_currency_code" value="0" <?php echo ( $options[ MM_WPFS::OPTION_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE ] == '0' ) ? 'checked' : '' ?>>
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
						<input type="radio" name="<?php echo MM_WPFS::OPTION_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION ?>" id="put_currency_identifier_to_the_left" value="1" <?php echo ( $options[ MM_WPFS::OPTION_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION ] == '1' ) ? 'checked' : '' ?>>
						<?php esc_html_e( '€10.99 (left)', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</label>
					<label class="checkbox">
						<input type="radio" name="<?php echo MM_WPFS::OPTION_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION ?>" id="put_currency_identifier_to_the_right" value="0" <?php echo ( $options[ MM_WPFS::OPTION_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION ] == '0' ) ? 'checked' : '' ?>>
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
						<input type="radio" name="<?php echo MM_WPFS::OPTION_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT ?>" id="do_not_put_whitespace_between_currency_and_amount" value="0" <?php echo ( $options[ MM_WPFS::OPTION_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT ] == '0' ) ? 'checked' : '' ?>>
						<?php esc_html_e( '$10.99 (no)', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</label>
					<label class="checkbox">
						<input type="radio" name="<?php echo MM_WPFS::OPTION_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT ?>" id="put_whitespace_between_currency_and_amount" value="1" <?php echo ( $options[ MM_WPFS::OPTION_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT ] == '1' ) ? 'checked' : '' ?>>
						<?php esc_html_e( 'USD 10.99 (yes)', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label class="control-label" for="form_css"><?php _e( "Custom Form CSS: ", MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
				</th>
				<td>
					<textarea name="form_css" id="form_css" class="large-text code" rows="10" cols="50"><?php echo $options['form_css']; ?></textarea>

					<p class="description"><?php _e( 'Add extra styling to the form. NOTE: if you don\'t know what this is do not change it.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label class="control-label"><?php _e( "Include Default Styles: ", MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
				</th>
				<td>
					<label class="radio">
						<input type="radio" name="includeStyles" id="includeStylesY" value="1" <?php echo ( $options['includeStyles'] == '1' ) ? 'checked' : '' ?> >
						<?php _e( 'Include', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</label>
					<label class="radio">
						<input type="radio" name="includeStyles" id="includeStylesN" value="0" <?php echo ( $options['includeStyles'] == '0' ) ? 'checked' : '' ?>>
						<?php _e( 'Exclude', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
					</label>

					<p class="description"><?php _e( 'Exclude styles if the payment forms do not appear properly. This can indicate a conflict with your theme.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
				</td>
			</tr>
		</table>
		<p class="submit">
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes' ) ?></button>
			<img src="<?php echo MM_WPFS_Assets::images( 'loader.gif' ); ?>" alt="<?php esc_attr_e( 'Loading...', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" class="showLoading"/>
		</p>
	</form>
</div>
