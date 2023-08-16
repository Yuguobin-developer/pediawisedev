<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2019.02.19.
 * Time: 09:57
 */

/**
 * @var MM_WPFS_CardUpdateModel $model
 */

?>
<div id="wpfs-manage-subscriptions-container" class="wpfs-form wpfs-w-60">
	<div class="wpfs-form-title"><?php esc_html_e( 'Manage your subscriptions', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></div>
	<div class="wpfs-form-lead">
		<div class="wpfs-form-description wpfs-form-description--sm">
			<?php printf( /* translators: p1: Subscriber's email address */ esc_html__( 'You can manage the credit card and subscriptions associated with %s.', MM_WPFS::L10N_DOMAIN_PUBLIC ), $model->getCustomerEmail() ); ?>
			<?php if ( $model->getAuthenticationType() == MM_WPFS_CardUpdateModel::AUTHENTICATION_TYPE_PLUGIN ): ?>
				<a href="" id="wpfs-anchor-logout" class="wpfs-btn wpfs-btn-link"><?php /* translators: Link text for log out link */ esc_html_e( 'Log out', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></a>
			<?php endif; ?>
		</div>
	</div>
	<div class="wpfs-form-subtitle"><?php /* translators: Label for the Update card section */ esc_html_e( 'Credit/debit card', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></div>
	<form id="wpfs-default-card-form">
		<div class="wpfs-credit-cards">
			<div class="wpfs-credit-card">
				<?php if ( empty( $model->getCardNumber() ) ): ?>
					<div><?php /* translators: Message stating that the Stripe customer doesn't have card set as payment method. */ esc_html_e( 'You don\'t have a default card.', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></div>
				<?php else: ?>
					<div class="wpfs-credit-card-logo">
						<img src="<?php echo esc_attr( $model->getCardImageUrl() ); ?>" alt="<?php echo esc_attr( $model->getCardName() ); ?>">
					</div>
					<div class="wpfs-credit-card-data">
						<div class="wpfs-credit-card-name"><?php echo esc_html( $model->getCardName() . ' ' . $model->getFormattedCardNumber() ); ?></div>
						<div class="wpfs-credit-card-expires">
							<?php /* translators: Column header of the card expiry date */ esc_html_e( 'Expires', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?>
							<br><?php echo esc_html( $model->getExpiration() ); ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<a id="wpfs-anchor-update-card" class="wpfs-btn wpfs-btn-link wpfs-btn-link--bold"><?php /* translators: Button label for updating the card stored in Stripe */ esc_html_e( 'Update card', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></a>
		</div>
	</form>
	<form id="wpfs-update-card-form" style="display: none;">
		<div class="wpfs-form-group wpfs-w-45">
			<div class="wpfs-form-control" id="wpfs-card" data-toggle="card"></div>
		</div>
		<div class="wpfs-form-actions wpfs-mt-3 wpfs-mb-4">
			<button class="wpfs-btn wpfs-btn-primary wpfs-mr-2" type="submit"><?php /* translators: Button label for updating the card stored in Stripe */ esc_html_e( 'Update card', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></button>
			<a id="wpfs-anchor-discard-card-changes" class="wpfs-btn wpfs-btn-link"><?php /* translators: Button label for discarding card update */ esc_html_e( 'Discard', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></a>
		</div>
	</form>
	<div class="wpfs-form-subtitle" id="wpfs-subscriptions-subtitle"><?php /* translators: Label for the Subscriptions section */ esc_html_e( 'Subscriptions', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></div>
	<form id="wpfs-cancel-subscription-form">
		<div id="wpfs-subscriptions-table" class="wpfs-subscriptions"></div>
		<div id="wpfs-subscriptions-actions" class="wpfs-form-actions">
			<button id="wpfs-button-cancel-subscription" class="wpfs-btn wpfs-btn-primary" type="submit" disabled><?php /* translators: Button label for cancelling a subscription */ esc_html_e( 'Cancel subscription', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></button>
		</div>
	</form>
    <div class="wpfs-form-subtitle" id="wpfs-invoices-subtitle"><?php /* translators: Label for the Invoices section */ esc_html_e( 'Invoices', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></div>
    <form id="wpfs-view-invoices-form">
        <div id="wpfs-invoices-table" class="wpfs-invoices"></div>
    </form>
</div>
<script type="text/template" id="wpfs-invoice-show-row">
    <div class="wpfs-icon-accounting-document wpfs-invoice-icon"></div>
    <div class="wpfs-invoice-data">
        <div class="wpfs-invoice-name">
            <%- invoiceNumber %> <span class="wpfs-invoice-date">(<%- created %>)</span>
        </div>
        <div class="wpfs-invoice-meta">
            <%- priceLabel %> <% if (planName) { %>• <% if (planQuantity > 1) { %><%- planQuantity %>x <% } %><%- planName %><% } %>
        </div>
        <div class="wpfs-invoice-actions">
            <a class="wpfs-btn wpfs-btn-link wpfs-btn-link--bold" href="<%- invoiceUrl %>" target="_blank"><?php /* translators: Button label for downloading an invoice */ esc_html_e( 'Download', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></a>
        </div>
    </div>
</script>
<script type="text/template" id="wpfs-invoices-actions-show-all" >
    <a id="wpfs-invoices-view-toggle" class="wpfs-btn wpfs-btn-link wpfs-btn-link--bold"><?php /* translators: Button label for showing all invoices, not just the latest ones. */ esc_html_e( 'Show all invoices', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></a>
</script>
<script type="text/template" id="wpfs-invoices-actions-show-all-loading" >
    <a class="wpfs-btn wpfs-btn-link wpfs-btn-link--bold wpfs-btn-link--loader" disabled>
        <?php
        /* translators: Button label for showing all invoices, not just the latest ones. */
        esc_html_e( 'Show all invoices', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?>
    </a>
</script>
<script type="text/template" id="wpfs-invoices-actions-show-latest" >
    <a id="wpfs-invoices-view-toggle" class="wpfs-btn wpfs-btn-link wpfs-btn-link--bold"><?php /* translators: Button label for showing the latest invoices. */ esc_html_e( 'Show only latest invoices', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></a>
</script>
<script type="text/template" id="wpfs-invoices-actions-show-latest-loading" >
    <a class="wpfs-btn wpfs-btn-link wpfs-btn-link--bold wpfs-btn-link--loader" disabled>
        <?php
        /* translators: Button label for showing the latest invoices. */
        esc_html_e( 'Show only latest invoices', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?>
    </a>
</script>
<script type="text/template" id="wpfs-subscription-show-row">
	<div class="wpfs-form-check">
		<input type="checkbox" class="wpfs-form-check-input" id="<%- idAttribute %>" name="<%- nameAttribute %>" value="<%- id %>">
		<label class="wpfs-form-check-label" for="<%- idAttribute %>">
			<div class="wpfs-subscription-data">
				<div class="wpfs-subscription-name">
					<%- planLabel %> - <span class="wpfs-subscription-status <%- statusClass %>"><%- status %></span>
				</div>
            <% if (priceAndIntervalLabel) { %>
				<div class="wpfs-subscription-meta"><%- priceAndIntervalLabel %>
					• <?php
                    /* translators: Label for the creation date of subscription */
                    esc_html_e( 'Created on', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?> <%- created %>
				</div>
				<div class="wpfs-subscription-actions">
					<% if (allowMultipleSubscriptions) { %>
					<a class="wpfs-btn wpfs-btn-link wpfs-btn-link--bold wpfs-subscription-update-quantity-action" href="" data-wpfs-subscription-id="<%- id %>">
						<?php
                        /* translators: Button label for changing subscription quantity */
                        esc_html_e( 'Change quantity', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?>
					</a>
					<% } %>
                    <% if (wpfsMyAccount.options.letSubscribersCancelSubscriptions) { %>
					<a class="wpfs-btn wpfs-btn-link wpfs-btn-link--bold wpfs-subscription-cancel-action" href="" data-wpfs-subscription-id="<%- id %>">
						<?php
                        /* translators: Button label for cancelling subscription */
                        esc_html_e( 'Cancel', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?>
					</a>
                    <% } %>
				</div>
            <% } else { %>
                <div class="wpfs-subscription-meta">
                    <?php esc_html_e( 'Created on', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?> <%- created %>
                </div>
            <% } %>
			</div>
		</label>
	</div>
</script>
<script type="text/template" id="wpfs-subscription-update-row">
	<div class="wpfs-subscription-name"><%- planName %></div>
	<form class="wpfs-form">
		<div class="wpfs-form-group">
			<label class="wpfs-form-label" for="name"><?php
                /* translators: Field label for changing subscription quantity */
                esc_html_e( 'Subscription quantity', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></label>
			<div class="wpfs-stepper wpfs-w-15">
				<input class="wpfs-form-control" type="text" name="wpfs-subscription-plan-quantity" value="<%- planQuantity %>" data-toggle="stepper" data-min="1" data-default-value="1"
				<% if (maximumPlanQuantity > 0) { %>data-max="<%- maximumPlanQuantity %>"<% } %>>
			</div>
		</div>
		<div class="wpfs-subscription-summary-title"><?php
            /* translators: Label for subscription summary with price and interval */
            esc_html_e( 'Summary:', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></div>
		<div class="wpfs-subscription-summary-description">
			<%- summary %>
		</div>
		<div class="wpfs-form-actions wpfs-mt-3 wpfs-mb-4">
			<button class="wpfs-btn wpfs-btn-primary wpfs-mr-2 wpfs-subscription-button-update-quantity" type="submit">
				<?php
                /* translators: Button label for updating subscription quantity */
                esc_html_e( 'Update quantity', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?>
			</button>
			<a href="" class="wpfs-btn wpfs-btn-link wpfs-subscription-link-cancel-update-quantity"><?php /* Translators: Button label for discarding subscription quantity update */ esc_html_e( 'Discard', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></a>
		</div>
	</form>
</script>
<script type="text/template" id="wpfs-subscription-empty-subscription-list">
    <?php esc_html_e( 'You don\'t have any subscriptions.', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?>
</script>
<script type="text/template" id="wpfs-subscription-empty-invoice-list">
    <?php esc_html_e( 'You don\'t have any finalized invoices.', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?>
</script>
<script type="text/template" id="wpfs-subscription-update-quantity-success-message">
	<strong><?php
        /* translators: Banner message for successfully updating subscription quantity */
        esc_html_e( 'Updated subscription quantity.', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></strong>
</script>
<script type="text/template" id="wpfs-subscription-update-quantity-error-message">
	<strong><?php esc_html_e( 'We couldn\'t update subscription. Please try again', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></strong>
</script>
<script type="text/template" id="wpfs-subscription-cancel-success-message">
	<strong><?php
        /* translators: Banner message for successfully cancelling subscription */
        esc_html_e( 'Cancelled subscription.', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></strong>
</script>
<script type="text/template" id="wpfs-subscription-cancel-error-message">
	<strong><?php esc_html_e( 'Failed subscription cancellation, try again.', MM_WPFS::L10N_DOMAIN_PUBLIC ); ?></strong>
</script>
