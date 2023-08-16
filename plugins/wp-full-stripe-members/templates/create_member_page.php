<?php
$user_query = new WP_User_Query( array( 'orderby' => 'registered', 'order' => 'ASC' ) );
$plans      = MM_WPFS::getInstance()->get_plans();
if ( array_key_exists( 'data', $plans ) ) {
	$plans = $plans['data'];
}
$roles     = MM_WPFS_Members::getInstance()->get_wp_roles();
$roleNames = MM_WPFS_Members::getInstance()->get_wp_role_names();
?>
<div class="wrap">
	<div id="updateDiv"><p><strong id="updateMessage"></strong></p></div>
	<h3>Create Member</h3>
	<a href="admin.php?page=fullstripe-members" class="button">Back to Members</a>
	<p>Use this form if you'd like to manually create a new member. Note this is not the recommended way, you should use
		the subscription forms and roles to create members when users subscribe.
		Use this if you must import members from before WP Full Stripe Members was installed. Please remember that
		manually created members will not automatically update or synchronize with Stripe -
		it is assumed you will manually control this members subscription.</p>
	<form action="" method="post" id="wpfs-members-create-form">
		<p class="tips"></p>
		<input type="hidden" name="action" value="wpfs_members_manual_create_member"/>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label>WordPress User:</label>
				</th>
				<td>
					<select name="wpfs_members_wp_user_id">
						<?php foreach ( $user_query->results as $wpUser ): ?>
							<option value="<?php echo $wpUser->ID; ?>"><?php echo $wpUser->display_name . ' (' . $wpUser->user_email . ')'; ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description">Members must be associated to a current WordPress User</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label class="control-label">Email Address: </label>
				</th>
				<td>
					<input type="text" class="regular-text" name="wpfs_members_email" id="wpfs_members_email">
					<p class="description">This will be used for the member login</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label>Plan: </label>
				</th>
				<td>
					<select name="wpfs_members_plan">
						<option value="none">Not Used</option>
						<?php foreach ( $plans as $p ): ?>
							<option value="<?php echo $p->id; ?>"><?php echo $p->product->name; ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description">What plan is this user subscribed to?</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label>Role: </label>
				</th>
				<td>
					<select name="wpfs_members_role">
						<?php foreach ( $roles as $roleID => $role ): ?>
							<option value="<?php echo $roleID; ?>"><?php echo $roleNames[ $roleID ]; ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description">What role do you wish to give this member?</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label class="control-label">Stripe Customer ID: </label>
				</th>
				<td>
					<input type="text" class="regular-text" name="wpfs_members_customer_id" id="wpfs_members_customer_id">
					<p class="description">If you know it, enter the Stripe Customer ID for this member, otherwise leave
						blank.</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label class="control-label">Stripe Subscription ID: </label>
				</th>
				<td>
					<input type="text" class="regular-text" name="wpfs_members_subscription_id" id="wpfs_members_subscription_id">
					<p class="description">If they are already subscribed to a Stripe plan enter the Stripe Subscription
						ID for this member, otherwise leave blank.</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label>Live or Test Mode?</label>
				</th>
				<td>
					<label class="radio">
						<input type="radio" name="wpfs_members_live_mode" id="wpfsmLiveN" value="0"> Test
					</label> <label class="radio">
						<input type="radio" name="wpfs_members_live_mode" id="wpfsmLiveY" value="1" checked="checked">
						Live
					</label>
					<p class="description">Is this a Test mode or Live mode member? (applies to Stripe customer &
						subscription IDs)</p>
				</td>
			</tr>
		</table>
		<p class="submit">
			<button class="button button-primary" type="submit">Create Member</button>
			<img src="<?php echo plugins_url( '/assets/images/loader.gif', dirname( __FILE__ ) ); ?>" alt="Loading..." class="showLoading"/>
		</p>
	</form>
</div>
