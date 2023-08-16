<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2017.04.05.
 * Time: 10:33
 */

$form_id_css = MM_WPFS_Utils::generate_form_hash( MM_WPFS_Utils::getFormType( $form ), MM_WPFS_Utils::getFormId( $form ), $form->name );
?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'Form ID for Custom CSS:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
		</th>
		<td>
			<p>
				<input class="wpfsadm-ro-clipboard" type="text" size="30" value="<?php echo( $form_id_css ); ?>" readonly>
				<a class="wpfsadm-copy-to-clipboard" data-form-id="<?php echo( $form_id_css ); ?>">Copy to clipboard</a>
			</p>
			<p class="description"><?php esc_html_e( 'Use this CSS selector to add custom styles to this form.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'Product Name:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
		</th>
		<td>
			<input type="text" class="regular-text" name="prod_desc" id="prod_desc" value="<?php echo esc_attr( $form->productDesc ); ?>" maxlength="<?php echo $form_data::PRODUCT_DESCRIPTION_LENGTH; ?>">

			<p class="description"><?php esc_html_e( 'The name of the product or service sold using this form. When using "Select Amount from List", the selected amount\'s description will replace this value.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'Product Description:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
		</th>
		<td>
			<input type="text" class="regular-text" name="company_name" id="company_name" value="<?php echo esc_attr( $form->companyName ); ?>" maxlength="<?php echo $form_data::COMPANY_NAME_LENGTH; ?>">

			<p class="description"><?php esc_html_e( 'A short description (one line) about the product or service sold using this form. When using "Select Amount from List", you may provide your Company\'s name here.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'Product Image:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
		</th>
		<td>
			<input id="form_checkout_image" type="text" name="form_checkout_image" value="<?php echo $form->image; ?>" maxlength="<?php echo $form_data::IMAGE_LENGTH; ?>" placeholder="<?php esc_attr_e( 'Enter image URL', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>">
			<button id="upload_image_button" class="button" type="button" value="Upload Image"><?php esc_html_e( 'Upload Image', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></button>
			<p class="description"><?php esc_html_e( 'A square image of your brand or product which is shown on the form. Min size 128px x 128px.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'Open Form Button Text:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
		</th>
		<td>
			<input type="text" class="regular-text" name="open_form_button_text" id="open_form_button_text" value="<?php echo esc_attr( $form->openButtonTitle ); ?>" maxlength="<?php echo $form_data::OPEN_BUTTON_TITLE_LENGTH; ?>">

			<p class="description"><?php esc_html_e( 'The text on the button used to pop open this form.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
		</td>
	</tr>
	<tr valign="top" id="include_amount_on_button_row" <?php echo $form->customAmount == MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE ? 'style="display: none;"' : '' ?>>
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'Include Amount on Button?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
		</th>
		<td>
			<label class="radio inline">
				<input type="radio" name="form_button_amount" id="hide_button_amount" value="0" <?php echo ( $form->showButtonAmount == '0' ) ? 'checked' : '' ?> >
				<?php esc_html_e( 'Hide', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			</label>
			<label class="radio inline">
				<input type="radio" name="form_button_amount" id="show_button_amount" value="1" <?php echo ( $form->showButtonAmount == '1' ) ? 'checked' : '' ?> >
				<?php esc_html_e( 'Show', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			</label>

			<p class="description"><?php esc_html_e( 'For set amount forms, choose to show/hide the amount on the payment button.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
		</td>
	</tr>
	<?php if ( MM_WPFS::FORM_TYPE_POPUP_SAVE_CARD !== MM_WPFS_Utils::getFormType( $form ) ): ?>
		<tr valign="top">
			<th scope="row">
				<label class="control-label"><?php esc_html_e( 'Amount Selector Style:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
			</th>
			<td>
				<select class="regular-text" name="amount_selector_style" id="amount_selector_style">
					<option value="dropdown" <?php echo( MM_WPFS::AMOUNT_SELECTOR_STYLE_DROPDOWN === $form->amountSelectorStyle ? 'selected="selected"' : '' ); ?>><?php esc_html_e( 'Product selector dropdown', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
					<option value="radio-buttons" <?php echo( MM_WPFS::AMOUNT_SELECTOR_STYLE_RADIO_BUTTONS === $form->amountSelectorStyle ? 'selected="selected"' : '' ); ?>><?php esc_html_e( 'List of products', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
					<option value="button-group" <?php echo( MM_WPFS::AMOUNT_SELECTOR_STYLE_BUTTON_GROUP === $form->amountSelectorStyle ? 'selected="selected"' : '' ); ?>><?php esc_html_e( 'Donation button group', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
				</select>

				<p class="description"><?php esc_html_e( 'Choose how you\'d like the amount(s) of the form to look.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
			</td>
		</tr>
	<?php endif; ?>
	<tr valign="top">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'Collect Billing Address?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
		</th>
		<td>
			<label class="radio inline">
				<input type="radio" name="form_show_address_input" id="hide_address_input" value="0" <?php echo ( $form->showBillingAddress == '0' ) ? 'checked' : '' ?> >
				<?php esc_html_e( 'Hide', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			</label>
			<label class="radio inline">
				<input type="radio" name="form_show_address_input" id="show_address_input" value="1" <?php echo ( $form->showBillingAddress == '1' ) ? 'checked' : '' ?> >
				<?php esc_html_e( 'Show', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			</label>

			<p class="description"><?php esc_html_e( 'Should this payment form also ask for the customers billing address?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label class="control-label" for=""><?php esc_html_e( "Checkout Form Language: ", MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
		</th>
		<td>
			<select name="form_preferred_language">
				<option value="<?php echo MM_WPFS::PREFERRED_LANGUAGE_AUTO; ?>"><?php esc_html_e( 'Auto', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
				<?php
				foreach ( MM_WPFS::get_available_checkout_languages() as $language ) {
					$option = '<option value="' . $language['value'] . '"';
					if ( $form->preferredLanguage == $language['value'] ) {
						$option .= ' selected="selected"';
					}
					$option .= '>';
					$option .= $language['name'];
					$option .= '</option>';
					echo $option;
				}
				?>
			</select>

			<p class="description"><?php esc_html_e( "Display the checkout form in the selected language. Use 'Auto' to determine the language from the locale sent by the customer's browser.", MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
		</td>
	</tr>
    <tr valign="top">
        <th scope="row">
            <label class="control-label"><?php esc_html_e( 'Format decimals with:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
        </th>
        <td>
            <label class="checkbox">
                <input type="radio" name="decimal_separator" id="decimal_separator_dot" value="<?php echo MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT; ?>" <?php echo ( $form->decimalSeparator == MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT ) ? 'checked' : '' ?>>
                <?php esc_html_e( '$10.99 (dot)', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
            </label>
            <label class="checkbox">
                <input type="radio" name="decimal_separator" id="decimal_separator_comma" value="<?php echo MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_COMMA; ?>" <?php echo ( $form->decimalSeparator == MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_COMMA ) ? 'checked' : '' ?>>
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
                <input type="radio" name="show_currency_symbol_instead_of_code" id="use_currency_symbol" value="1" <?php echo ( $form->showCurrencySymbolInsteadOfCode == '1' ) ? 'checked' : '' ?>>
                <?php esc_html_e( '$10.99 (symbol)', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
            </label>
            <label class="checkbox">
                <input type="radio" name="show_currency_symbol_instead_of_code" id="use_currency_code" value="0" <?php echo ( $form->showCurrencySymbolInsteadOfCode == '0' ) ? 'checked' : '' ?>>
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
                <input type="radio" name="show_currency_sign_at_first_position" id="put_currency_identifier_to_the_left" value="1" <?php echo ( $form->showCurrencySignAtFirstPosition == '1' ) ? 'checked' : '' ?>>
                <?php esc_html_e( '€10.99 (left)', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
            </label>
            <label class="checkbox">
                <input type="radio" name="show_currency_sign_at_first_position" id="put_currency_identifier_to_the_right" value="0" <?php echo ( $form->showCurrencySignAtFirstPosition == '0' ) ? 'checked' : '' ?>>
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
                <input type="radio" name="put_whitespace_between_currency_and_amount" id="do_not_put_whitespace_between_currency_and_amount" value="0" <?php echo ( $form->putWhitespaceBetweenCurrencyAndAmount == '0' ) ? 'checked' : '' ?>>
                <?php esc_html_e( '$10.99 (no)', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
            </label>
            <label class="checkbox">
                <input type="radio" name="put_whitespace_between_currency_and_amount" id="put_whitespace_between_currency_and_amount" value="1" <?php echo ( $form->putWhitespaceBetweenCurrencyAndAmount == '1' ) ? 'checked' : '' ?>>
                <?php esc_html_e( 'USD 10.99 (yes)', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
            </label>
        </td>
    </tr>
</table>
