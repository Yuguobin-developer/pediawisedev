<?php
$memberID = null;
$valid    = true;
$error = "";

if ( isset( $_GET['member'] ) ) {
	$memberID = $_GET['member'];
}

if ( $memberID ) {
	$member = MM_WPFS_Members::getInstance()->get_member( $memberID );
	if ( ! $member ) {
		$valid = false;
	} else {
		try {
			$subscription   = MM_WPFS_Members::getInstance()->stripe->retrieve_subscription( $member->stripeSubscriptionID );
			$roleNames      = MM_WPFS_Members::getInstance()->get_wp_role_names();
			$rolePlans      = MM_WPFS_Members::getInstance()->get_role_plans();
			$stripePlans    = MM_WPFS_Members::getInstance()->get_stripe_plans_for_active_roles();
			$currencySymbol = MM_WPFS::get_currency_symbol_for( $subscription->plan->currency );
		} catch ( Exception $e ) {
			$valid = false;
			$error = "There was an error: " . $e;
		}
	}
} else {
	$valid = false;
}
?>

<div class="wrap">
	<div id="updateDiv"><p><strong id="updateMessage"></strong></p></div>
	<?php if ( ! $valid ): ?>
		<h3>Member Summary</h3>
		<div class="error"><p>There was an error retrieving the member data: <?php echo( $error ); ?>
                Please make sure your Stripe API keys are
				set in WP Full Stripe Settings.</p></div>
	<?php else: ?>
		<h3>Member Summary</h3>
		<a href="admin.php?page=fullstripe-members" class="button">Back to Members</a>
		<?php if ( defined( 'WPFS_MEMBERS_DEMO' ) ): ?>
			<h3>DEMO VERSION: Editing members is disabled.</h3>
		<?php endif; ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label>Member Email:</label>
				</th>
				<td>
					<?php echo $member->email; ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label>Membership Level:</label>
				</th>
				<td>
					<?php echo $roleNames[ $member->role ]; ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label>Membership Price:</label>
				</th>
				<td>
					<?php
                    $price_label = MM_WPFS_Members::getTranslatedPricingLabel(
                        $subscription->plan->currency,
                        $subscription->plan->amount,
                        $subscription->plan->interval,
                        $subscription->plan->interval_count
                    );

					echo $price_label; ?>
					<p class="description"><?php echo $subscription->plan->product->name; ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label>
						<?php if ( $subscription->cancel_at_period_end ): ?>
							Membership End Date:
						<?php else: ?>
							Next Billing Date:
						<?php endif; ?>
					</label>
				</th>
				<td>
					<?php echo date( 'F jS Y', $subscription->current_period_end ); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label>Membership Status:</label>
				</th>
				<td>
					<?php echo $subscription->cancel_at_period_end ? 'Canceling' : ucfirst( $member->stripeSubscriptionStatus ); ?>
				</td>
			</tr>
		</table>
		<hr/>
		<form action="" method="POST" id="wpfs_members_change_level">
			<p class="tips"></p>
			<input type="hidden" name="action" value="wpfs_members_change_level"/>
			<input type="hidden" name="memberID" value="<?php echo $member->memberID; ?>"/>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label>Change Membership Level:</label>
					</th>
					<td>
						<select id="wpfs_members_level" name="wpfs_members_level">
							<?php foreach ( $stripePlans as $role => $plan ): ?>
								<?php if ( $role !== $member->role ): ?>
									<option
										value="<?php echo $plan->id; ?>"
										data-amount="<?php echo $plan->amount; ?>"
										data-interval="<?php echo $plan->interval; ?>"
										data-interval-count="<?php echo $plan->interval_count; ?>"
										data-role="<?php echo $role; ?>">
										<?php echo $roleNames[ $role ]; ?>
									</option>
								<?php endif; ?>
							<?php endforeach; ?>
						</select>
						<p class="description">WARNING: This will update the members role and associated subscription
							and make a charge to their credit card if necessary. The member will then be billed for this
							new Role in future.</p>
					</td>
				</tr>
			</table>
			<p class="submit">
				<button type="submit" class="button button-primary" <?php echo ( defined( 'WPFS_MEMBERS_DEMO' ) ) ? 'disabled="disabled"' : '' ?> >
					Change Membership Level
				</button>
				<img src="<?php echo plugins_url( '/assets/images/loader.gif', dirname( __FILE__ ) ); ?>" alt="Loading..." class="showLoading"/>
			</p>
		</form>
		<hr/>
		<form action="" method="POST" id="wpfs_members_cancel">
			<p class="delete_tips"></p>
			<input type="hidden" name="action" value="wpfs_members_cancel"/>
			<input type="hidden" name="memberID" value="<?php echo $member->memberID; ?>"/>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label>Cancel Membership:</label>
					</th>
					<td>
						<p class="description">This will cancel the members subscription. They will still have access
							until the end of their last payment period. </p>
					</td>
				</tr>
			</table>
			<p class="submit">
				<button type="submit" class="button button-primary" <?php echo $subscription->cancel_at_period_end ? 'disabled="disabled"' : '' ?> <?php echo ( defined( 'WPFS_MEMBERS_DEMO' ) ) ? 'disabled="disabled"' : '' ?> >
					Cancel Membership
				</button>
				<img src="<?php echo plugins_url( '/assets/images/loader.gif', dirname( __FILE__ ) ); ?>" alt="Loading..." class="delete_showLoading"/>
			</p>
		</form>
		<hr/>
		<form action="" method="POST" id="wpfs_members_change_role">
			<p class="change_tips"></p>
			<input type="hidden" name="action" value="wpfs_members_change_role"/>
			<input type="hidden" name="memberID" value="<?php echo $member->memberID; ?>"/>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label>Change Role:</label>
					</th>
					<td>
						<select id="wpfs_member_role" name="wpfs_member_role">
							<option value="wpfs_no_access">No Access</option>
							<?php foreach ( $rolePlans as $role => $plan ): ?>
								<?php if ( $role !== $member->role ): ?>
									<option value="<?php echo $role; ?>"> <?php echo $roleNames[ $role ]; ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						</select>
						<p class="description">This only changes the members Role, therefore changing their access to
							your protected content.
							<strong>THIS WILL NOT CHANGE THE MEMBERS SUBSCRIPTION.</strong> The member will continue to
							be billed for the role they subscribed to even if you choose a different role here.
							You can block all access to the protected content for this member by selecting 'No Access'.
							Only change the Role if you REALLY know what you are doing. </p>
					</td>
				</tr>
			</table>
			<p class="submit">
				<button id="wpfs_members_block_access" <?php echo ( defined( 'WPFS_MEMBERS_DEMO' ) ) ? 'disabled="disabled"' : '' ?> class="button button-primary">
					Change Role
				</button>
				<img src="<?php echo plugins_url( '/assets/images/loader.gif', dirname( __FILE__ ) ); ?>" alt="Loading..." class="change_showLoading"/>
			</p>
		</form>

	<?php endif; ?>
	