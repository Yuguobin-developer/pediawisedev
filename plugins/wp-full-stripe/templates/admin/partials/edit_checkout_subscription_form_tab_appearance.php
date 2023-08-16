<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2017.08.25.
 * Time: 15:33
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
			<label class="control-label" for=""><?php esc_html_e( 'Plan Selector Style: ', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
		</th>
		<td>
			<select name="plan_selector_style">
				<option value="<?php echo MM_WPFS::PLAN_SELECTOR_STYLE_DROPDOWN; ?>" <?php echo ( $form->planSelectorStyle == MM_WPFS::PLAN_SELECTOR_STYLE_DROPDOWN ) ? 'selected' : '' ?>><?php esc_html_e( 'Dropdown', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
				<option value="<?php echo MM_WPFS::PLAN_SELECTOR_STYLE_LIST; ?>" <?php echo ( $form->planSelectorStyle == MM_WPFS::PLAN_SELECTOR_STYLE_LIST ) ? 'selected' : '' ?>><?php esc_html_e( 'List', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
			</select>

			<p class="description"><?php esc_html_e( 'Style of the plan selector component.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
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
	<tr valign="top">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'Simple Button Layout?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
		</th>
		<td>
			<label class="radio inline">
				<input type="radio" name="form_simple_button_layout" id="form_simple_button_layout_no" value="0" <?php echo ( $form->simpleButtonLayout == '0' ) ? 'checked' : '' ?>>
				<?php esc_html_e( 'No', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			</label>
			<label class="radio inline">
				<input type="radio" name="form_simple_button_layout" id="form_simple_button_layout_yes" value="1" <?php echo ( $form->simpleButtonLayout == '1' ) ? 'checked' : '' ?>>
				<?php esc_html_e( 'Yes', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			</label>

			<p class="description"><?php esc_html_e( "Display only a 'Subscribe' button. It hides the plan selector, the custom input fields, and the coupon field.", MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
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
