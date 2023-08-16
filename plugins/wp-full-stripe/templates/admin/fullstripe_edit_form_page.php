<?php

/**
 * @var WPFS_FormValidationData
 */
$form_data = MM_WPFS::getInstance()->get_form_validation_data();

global $wpdb;

$form_id   = - 1;
$form_type = "";
if ( isset( $_GET['form'] ) ) {
	$form_id = $_GET['form'];
}
if ( isset( $_GET['type'] ) ) {
	$form_type = $_GET['type'];
}

$valid = true;
if ( $form_id == - 1 || $form_type == "" ) {
	$valid = false;
}

/** @var $plans array */
$plans = array();
$form  = null;

if ( $valid ) {

	if ( $form_type == "payment" ) {
		$form = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "fullstripe_payment_forms WHERE paymentFormID=%d", $form_id ) );
	} else if ( $form_type == "inline_card_capture" ) {
		$form = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "fullstripe_payment_forms WHERE paymentFormID=%d", $form_id ) );
	} else if ( $form_type == "popup_card_capture" ) {
		$form = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "fullstripe_checkout_forms WHERE checkoutFormID=%d", $form_id ) );
	} else if ( $form_type == "subscription" ) {
		$form  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "fullstripe_subscription_forms WHERE subscriptionFormID=%d", $form_id ) );
		$plans = MM_WPFS::getInstance()->get_plans();
	} else if ( $form_type == "checkout" ) {
		$form = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "fullstripe_checkout_forms WHERE checkoutFormID=%d", $form_id ) );
	} else if ( $form_type == "checkout-subscription" ) {
		$form  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "fullstripe_checkout_subscription_forms WHERE checkoutSubscriptionFormID=%d", $form_id ) );
		$plans = MM_WPFS::getInstance()->get_plans();
	} else if ( $form_type == "inline_donation" ) {
        $form = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "fullstripe_donation_forms WHERE donationFormID=%d", $form_id ) );
    } else if ( $form_type == "checkout_donation" ) {
        $form = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "fullstripe_checkout_donation_forms WHERE checkoutDonationFormID=%d", $form_id ) );
    }else {
		$valid = false;
	}

	if ( $form == null ) {
		$valid = false;
	}
}

?>
<div class="wrap">
	<h2><?php esc_html_e( 'Full Stripe Edit Form', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></h2>

	<div id="updateDiv"><p><strong id="updateMessage"></strong></p></div>
	<?php if ( ! $valid ): ?>
		<p><?php esc_html_e( 'Form not found!', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></p>
	<?php else: ?>
		<?php if ( $form_type == "payment" ): ?>
			<?php include( MM_WPFS_Assets::templates( 'admin/partials/edit_payment_form.php' )); ?>
		<?php elseif ( $form_type == "inline_card_capture" ): ?>
			<?php include( MM_WPFS_Assets::templates( 'admin/partials/edit_inline_card_capture_form.php' )); ?>
		<?php elseif ( $form_type == "popup_card_capture" ): ?>
			<?php include( MM_WPFS_Assets::templates( 'admin/partials/edit_popup_card_capture_form.php' )); ?>
		<?php elseif ( $form_type == "subscription" ): ?>
			<?php include( MM_WPFS_Assets::templates( 'admin/partials/edit_subscription_form.php' )); ?>
		<?php elseif ( $form_type == "checkout" ): ?>
			<?php include( MM_WPFS_Assets::templates( 'admin/partials/edit_checkout_form.php' )); ?>
		<?php elseif ( $form_type == "checkout-subscription" ): ?>
			<?php include( MM_WPFS_Assets::templates( 'admin/partials/edit_checkout_subscription_form.php' )); ?>
		<?php elseif ( $form_type == "inline_donation" ): ?>
            <?php include( MM_WPFS_Assets::templates( 'admin/partials/edit_inline_donation_form.php' )); ?>
        <?php elseif ( $form_type == "checkout_donation" ): ?>
            <?php include( MM_WPFS_Assets::templates( 'admin/partials/edit_checkout_donation_form.php' )); ?>
        <?php endif;?>
	<?php endif; ?>
</div>
