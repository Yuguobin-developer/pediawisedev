<?php

$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'donations';

global $wpdb;

/** @var $donationForms array */
$donationForms          = array();
/** @var $checkoutDonationForms array */
$checkoutDonationForms = array();
if ( $active_tab == 'forms' ) {
    $donationForms          = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "fullstripe_donation_forms;" );
    $checkoutDonationForms = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "fullstripe_checkout_donation_forms;" );
}
?>
<div class="wrap">
    <h2> <?php esc_html_e( 'Full Stripe Donations', MM_WPFS::L10N_DOMAIN_ADMIN ); ?> </h2>
    <div id="updateDiv"><p><strong id="updateMessage"></strong></p></div>
    <h2 class="nav-tab-wrapper">
        <a href="<?php echo admin_url( 'admin.php?page=fullstripe-donations&tab=donations' ); ?>" class="nav-tab <?php echo $active_tab == 'donations' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e( 'Donations', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
        </a>
        <a href="<?php echo admin_url( 'admin.php?page=fullstripe-donations&tab=forms' ); ?>" class="nav-tab <?php echo $active_tab == 'forms' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e( 'Donation Forms', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
        </a>
    </h2>
    <div class="wpfs-tab-content">
        <?php if ( $active_tab == 'donations' ): ?>
            <div class="" id="donations">
                <h2>
                    <img src="<?php echo MM_WPFS_Assets::images( 'loader.gif' ); ?>" alt="<?php esc_attr_e( 'Loading...', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" class="showLoading"/>
                </h2>
                <form method="get">
                    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
                    <label><?php _e( 'Donor: ', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label><input type="text" name="donor" size="35" placeholder="<?php _e( 'Enter name, email address, or stripe ID', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" value="<?php echo isset( $_REQUEST['donor'] ) ? $_REQUEST['donor'] : ''; ?>">
                    <label><?php _e( 'Donation: ', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label><input type="text" name="donation" placeholder="<?php _e( 'Enter payment ID', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" value="<?php echo isset( $_REQUEST['donation'] ) ? $_REQUEST['donation'] : ''; ?>">
                    <label><?php _e( 'Mode: ', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
                    <select name="mode">
                        <option value="" <?php echo ! isset( $_REQUEST['mode'] ) || $_REQUEST['mode'] == '' ? 'selected' : ''; ?>><?php _e( 'All', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
                        <option value="live" <?php echo isset( $_REQUEST['mode'] ) && $_REQUEST['mode'] == 'live' ? 'selected' : ''; ?>><?php _e( 'Live', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
                        <option value="test" <?php echo isset( $_REQUEST['mode'] ) && $_REQUEST['mode'] == 'test' ? 'selected' : ''; ?>><?php _e( 'Test', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
                    </select>
                    <span class="wpfs-search-actions">
						<button class="button button-primary"><?php _e( 'Search', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></button> <?php _e( 'or', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
						<a href="<?php echo admin_url( 'admin.php?page=fullstripe-donations' ); ?>"><?php _e( 'Reset', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
					</span>
                    <?php
                    /** @var WP_List_Table $donationsTable */
                    $donationsTable->prepare_items();
                    $donationsTable->display();
                    ?>
                </form>
            </div>
        <?php elseif ( $active_tab == 'forms' ): ?>
            <div class="" id="wpfs-donation-forms">
                <div style="min-height: 200px;">
                    <h2><?php esc_html_e( 'Your Inline Forms', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                        <a class="page-title-action" href="<?php echo add_query_arg(
                            array(
                                'page' => 'fullstripe-create-form',
                                'type' => 'inline_donation'
                            ),
                            admin_url( "admin.php" )
                        ); ?>" title="<?php esc_attr_e( 'Create Inline Form', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>"><i class="fa fa-plus fa-fw"></i><?php esc_html_e( 'Create Inline Form', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                        </a>
                        <img src="<?php echo MM_WPFS_Assets::images( 'loader.gif' ); ?>" alt="<?php esc_attr_e( 'Loading...', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" class="showLoading"/>
                    </h2>
                    <?php if ( count( $donationForms ) === 0 ): ?>
                        <p class="alert alert-info">
                            <?php esc_html_e( "No inline donation forms created yet. Use the 'Create Inline Form' button to get started.", MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                        </p>
                    <?php else: ?>
                        <table class="wp-list-table widefat fixed donation-forms">
                            <thead>
                            <tr>
                                <th class="manage-column column-action column-primary"><?php esc_html_e( 'Actions', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
                                <th class="manage-column column-name"><?php esc_html_e( 'Name', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
                                <th class="manage-column column-plan_ids"><?php esc_html_e( 'Donation amounts', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
                            </tr>
                            </thead>
                            <tbody id="donationFormsTable">
                            <?php foreach ($donationForms as $donationForm ): ?>
                                <?php
                                $donationAmountsLabel = MM_WPFS_Utils::generateDonationAmountsLabel( $donationForm );
                                ?>
                                <tr>
                                    <td class="column-action">
                                        <?php
                                        $shortcode = MM_WPFS_Utils::createShortCodeString( $donationForm );
                                        ?>
                                        <span id="shortcode-donation-tooltip__<?php echo $donationForm->donationFormID; ?>" class="shortcode-tooltip" data-shortcode="<?php echo esc_attr( $shortcode ); ?>"></span>
                                        <a id="shortcode-donation__<?php echo $donationForm->donationFormID; ?>" class="button button-primary shortcode-donation" data-form-id="<?php echo $donationForm->donationFormID; ?>" title="<?php _e( 'Shortcode', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>">
                                            <i class="fa fa-code fa-fw"></i>
                                        </a>
                                        <a class="button button-primary" href="<?php echo add_query_arg(
                                            array(
                                                'page' => 'fullstripe-edit-form',
                                                'form' => $donationForm->donationFormID,
                                                'type' => 'inline_donation'
                                            ),
                                            admin_url( "admin.php" )
                                        ); ?>" title="<?php _e( 'Edit', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>"><i class="fa fa-pencil fa-fw"></i></a>
                                        <span class="form-action-last">
											<button class="button delete" data-id="<?php echo $donationForm->donationFormID; ?>" data-type="inlineDonationForm" title="<?php _e( 'Delete', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>">
												<i class="fa fa-trash-o fa-fw"></i>
											</button>
										</span>
                                    </td>
                                    <td class="column-name"><?php echo esc_html( $donationForm->name ); ?></td>
                                    <td class="column-plan_ids"><?php echo esc_html( $donationAmountsLabel ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                <div style="min-height: 200px;">
                    <h2><?php esc_html_e( 'Your Checkout Forms', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                        <a class="page-title-action" href="<?php echo add_query_arg(
                            array(
                                'page' => 'fullstripe-create-form',
                                'type' => 'checkout_donation'
                            ),
                            admin_url( "admin.php" )
                        ); ?>" title="<?php esc_attr_e( 'Create Checkout Form', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>"><i class="fa fa-plus fa-fw"></i><?php esc_html_e( 'Create Checkout Form', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                        </a>
                        <img src="<?php echo MM_WPFS_Assets::images( 'loader.gif' ); ?>" alt="<?php esc_attr_e( 'Loading...', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" class="showLoading"/>
                    </h2>
                    <?php if ( count( $checkoutDonationForms ) === 0 ): ?>
                        <p class="alert alert-info">
                            <?php esc_html_e( "No checkout donation forms created yet. Use the 'Create Checkout Form' button to get started.", MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
                        </p>
                    <?php else: ?>
                        <table class="wp-list-table widefat fixed donation-forms">
                            <thead>
                            <tr>
                                <th class="manage-column column-action column-primary"><?php esc_html_e( 'Actions', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
                                <th class="manage-column column-name"><?php esc_html_e( 'Name', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
                                <th class="manage-column column-plan_ids"><?php esc_html_e( 'Donation amounts', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></th>
                            </tr>
                            </thead>
                            <tbody id="subscriptionFormsTable">
                            <?php foreach ($checkoutDonationForms as $checkoutDonationForm ): ?>
                                <?php
                                $donationAmountsLabel = MM_WPFS_Utils::generateDonationAmountsLabel( $checkoutDonationForm );
                                ?>
                                <tr>
                                    <td class="column-action">
                                        <?php
                                        $shortcode = MM_WPFS_Utils::createShortCodeString( $checkoutDonationForm );
                                        ?>
                                        <span id="shortcode-checkout-donation-tooltip__<?php echo $checkoutDonationForm->checkoutDonationFormID; ?>" class="shortcode-tooltip" data-shortcode="<?php echo esc_attr( $shortcode ); ?>"></span>
                                        <a id="shortcode-checkout-donation__<?php echo $checkoutDonationForm->checkoutDonationFormID; ?>" class="button button-primary shortcode-checkout-donation" data-form-id="<?php echo $checkoutDonationForm->checkoutDonationFormID; ?>" title="<?php _e( 'Shortcode', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>">
                                            <i class="fa fa-code fa-fw"></i>
                                        </a>
                                        <a class="button button-primary" href="<?php echo add_query_arg(
                                            array(
                                                'page' => 'fullstripe-edit-form',
                                                'form' => $checkoutDonationForm->checkoutDonationFormID,
                                                'type' => 'checkout_donation'
                                            ),
                                            admin_url( "admin.php" )
                                        ); ?>" title="<?php _e( 'Edit', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>"><i class="fa fa-pencil fa-fw"></i></a>
                                        <span class="form-action-last">
											<button class="button delete" data-id="<?php echo $checkoutDonationForm->checkoutDonationFormID; ?>" data-type="checkoutDonationForm" title="<?php _e( 'Delete', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>">
												<i class="fa fa-trash-o fa-fw"></i>
											</button>
										</span>
                                    </td>
                                    <td class="column-name"><?php echo esc_html( $checkoutDonationForm->name ); ?></td>
                                    <td class="column-plan_ids"><?php echo esc_html( $donationAmountsLabel ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif ?>
    </div>
</div>