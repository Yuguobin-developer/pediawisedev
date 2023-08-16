<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2017.02.23.
 * Time: 16:21
 */

$form_type = isset( $_GET['type'] ) ? $_GET['type'] : 'payment';

/**
 * @var WPFS_FormValidationData
 */
$form_data = MM_WPFS::getInstance()->get_form_validation_data();

?>
<div class="wrap">
	<h2> <?php esc_html_e( 'Full Stripe Create Form', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </h2>

	<div id="updateDiv"><p><strong id="updateMessage"></strong></p></div>

	<div id="create">
		<?php if ( $form_type == 'payment' ): ?>
			<?php include MM_WPFS_Assets::templates( 'admin/partials/create_payment_form.php' ); ?>
		<?php elseif ( $form_type == 'inline_card_capture' ): ?>
			<?php include MM_WPFS_Assets::templates( 'admin/partials/create_inline_card_capture_form.php' ); ?>
		<?php elseif ( $form_type == 'popup_card_capture' ): ?>
			<?php
			$open_form_button_text_value = __( 'Save Card Details', MM_WPFS::L10N_DOMAIN_ADMIN );
			$button_title                = __( 'Save Card Details', MM_WPFS::L10N_DOMAIN_ADMIN );
			include MM_WPFS_Assets::templates( 'admin/partials/create_popup_card_capture_form.php' );
			?>
		<?php elseif ( $form_type == 'checkout' ): ?>
			<?php include MM_WPFS_Assets::templates( 'admin/partials/create_checkout_form.php' ); ?>
		<?php elseif ( $form_type == 'subscription' ): ?>
			<?php include MM_WPFS_Assets::templates( 'admin/partials/create_subscription_form.php' ); ?>
		<?php elseif ( $form_type == 'checkout-subscription' ): ?>
			<?php include MM_WPFS_Assets::templates( 'admin/partials/create_checkout_subscription_form.php' ); ?>
        <?php elseif ( $form_type == 'inline_donation' ): ?>
            <?php include MM_WPFS_Assets::templates( 'admin/partials/create_donation_form.php' ); ?>
        <?php elseif ( $form_type == 'checkout_donation' ): ?>
            <?php include MM_WPFS_Assets::templates( 'admin/partials/create_checkout_donation_form.php' ); ?>
		<?php endif; ?>
	</div>

</div>