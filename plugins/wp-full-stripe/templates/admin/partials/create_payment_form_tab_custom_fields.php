<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2017.03.28.
 * Time: 14:05
 */

$customInputFieldMaxCount = MM_WPFS::get_custom_input_field_max_count();

?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'Include Terms of Use Checkbox?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
		</th>
		<td>
			<label class="radio inline">
				<input type="radio" name="show_terms_of_use" id="show_terms_of_use_no" value="0" checked="checked">
				<?php esc_html_e( 'No', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			</label>
			<label class="radio inline">
				<input type="radio" name="show_terms_of_use" id="show_terms_of_use_yes" value="1">
				<?php esc_html_e( 'Yes', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			</label>
			<p class="description"><?php esc_html_e( 'You can ask the customer to accept the Terms of Use.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
		</td>
	</tr>
</table>
<table id="termsOfUseSection" class="form-table" style="display: none;">
	<tr valign="top">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'Checkbox Label: ', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
		</th>
		<td>
			<input id="terms_of_use_label" type="text" class="large-text" name="terms_of_use_label" value="<?php echo esc_attr( MM_WPFS_Utils::get_default_terms_of_use_label() ); ?>"/>
			<p class="description"><?php esc_html_e( 'The label which is displayed next to the checkbox.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'Not Checked Error Message: ', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
		</th>
		<td>
			<input id="terms_of_use_not_checked_error_message" type="text" class="large-text" name="terms_of_use_not_checked_error_message" value="<?php echo esc_attr( MM_WPFS_Utils::get_default_terms_of_use_not_checked_error_message() ); ?>"/>
			<p class="description"><?php esc_html_e( 'The error message to display if the checkbox is not checked when the form is submitted.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
		</td>
	</tr>
</table>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'Include Custom Input Fields?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
		</th>
		<td>
			<label class="radio inline">
				<input type="radio" name="form_include_custom_input" id="noinclude_custom_input" value="0" checked="checked">
				<?php esc_html_e( 'No', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			</label>
			<label class="radio inline">
				<input type="radio" name="form_include_custom_input" id="include_custom_input" value="1">
				<?php esc_html_e( 'Yes', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			</label>
			<p class="description"><?php esc_html_e( 'You can ask for extra information from the customer to be included in the payment details.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
		</td>
	</tr>
</table>
<table id="customInputSection" class="form-table" style="display: none;">
	<tr valign="top">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'Make Custom Input Fields Required?', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
		</th>
		<td>
			<label class="radio inline">
				<input type="radio" name="form_custom_input_required" id="custom_input_required_no" value="0" checked="checked">
				<?php esc_html_e( 'No', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			</label>
			<label class="radio inline">
				<input type="radio" name="form_custom_input_required" id="custom_input_required_yes" value="1">
				<?php esc_html_e( 'Yes', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'Number of inputs:', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </label>
		</th>
		<td>
			<select id="customInputNumberSelect">
				<?php
				for ( $i = 1; $i <= $customInputFieldMaxCount; $i ++ ) {
					$option = '<option';
					$option .= " value=\"{$i}\"";
					$option .= '>';
					$option .= $i;
					$option .= '</option>';
					echo $option;
				}
				?>
			</select>
		</td>
	</tr>
	<?php for ( $i = 1; $i <= $customInputFieldMaxCount; $i ++ ): ?>
		<?php
		$customInputFieldRowAttributes = ' class="wpfs-admin-form-custom-field"';
		$customInputFieldRowAttributes .= " data-row-number=\"$i\"";
		if ( $i > 1 ) {
			$customInputFieldRowAttributes .= ' style="display: none;"';
		}

		$labelId = "form_custom_input_label_$i";
		?>
		<tr valign="top" <?php echo $customInputFieldRowAttributes; ?>>
			<th scope="row">
				<label class="control-label"><?php echo esc_html( sprintf( __( 'Custom Input Label %d:', MM_WPFS::L10N_DOMAIN_ADMIN ), $i ) ); ?> </label>
			</th>
			<td>
				<input type="text" class="regular-text" name="<?php echo $labelId; ?>" id="<?php echo $labelId; ?>" maxlength="<?php echo MM_WPFS_Utils::STRIPE_METADATA_KEY_MAX_LENGTH; ?>"/>
				<?php if ( $i == 1 ): ?>
					<p class="description"><?php esc_html_e( 'The text for the label next to the custom input field.', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
				<?php endif; ?>
			</td>
		</tr>
	<?php endfor; ?>
</table>
