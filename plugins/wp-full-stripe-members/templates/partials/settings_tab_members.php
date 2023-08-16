<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2016.04.15.
 * Time: 16:07
 */

$options = get_option( 'fullstripe_options' );

?>
<h2>Settings</h2>
<form action="" method="post" id="wpfs-members-settings-form">
	<p class="tips"></p>
	<input type="hidden" name="action" value="wpfs_members_update_settings"/>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">
				<label>Block members who are past due?</label>
			</th>
			<td>
				<label class="radio">
					<input type="radio" name="wpfs_members_block_past_due" value="0" <?php echo ( $options['wpfs_members_block_past_due'] == '0' ) ? 'checked' : '' ?> >
					No
				</label> <label class="radio">
					<input type="radio" name="wpfs_members_block_past_due" value="1" <?php echo ( $options['wpfs_members_block_past_due'] == '1' ) ? 'checked' : '' ?>>
					Yes
				</label>

				<p class="description">You can choose to block members considered "past due" on their
					subscription payments. Stripe usually allows for several retries (see your Stripe dashboard)
					before marking
					payments as "unpaid" or "canceled". NOTE: we'll always block unpaid and canceled
					members.</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label>Allow members change membership level?</label>
			</th>
			<td>
				<label class="radio">
					<input type="radio" name="wpfs_members_allow_change_level" value="0" <?php echo ( $options['wpfs_members_allow_change_level'] == '0' ) ? 'checked' : '' ?> >
					No
				</label> <label class="radio">
					<input type="radio" name="wpfs_members_allow_change_level" value="1" <?php echo ( $options['wpfs_members_allow_change_level'] == '1' ) ? 'checked' : '' ?>>
					Yes
				</label>

				<p class="description">This allows members to upgrade/downgrade their membership level (and
					therefore related subscription plan) on their "My Account" page.</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label>Allow members to cancel membership?</label>
			</th>
			<td>
				<label class="radio">
					<input type="radio" name="wpfs_members_allow_cancel_membership" value="0" <?php echo ( $options['wpfs_members_allow_cancel_membership'] == '0' ) ? 'checked' : '' ?> >
					No
				</label> <label class="radio">
					<input type="radio" name="wpfs_members_allow_cancel_membership" value="1" <?php echo ( $options['wpfs_members_allow_cancel_membership'] == '1' ) ? 'checked' : '' ?>>
					Yes
				</label>

				<p class="description">This allows members to cancel their membership level (and therefore
					related subscription plan) on their "My Account" page.</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label>Turn on member status cron job?</label>
			</th>
			<td>
				<label class="radio">
					<input type="radio" name="wpfs_members_member_status_cron" value="0" <?php echo ( $options['wpfs_members_member_status_cron'] == '0' ) ? 'checked' : '' ?> >
					No
				</label> <label class="radio">
					<input type="radio" name="wpfs_members_member_status_cron" value="1" <?php echo ( $options['wpfs_members_member_status_cron'] == '1' ) ? 'checked' : '' ?>>
					Yes
				</label>

				<p class="description">Turn on a cron job that goes through all members daily and checks &
					updates their subscription status. </p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label>Member login cookie timeout:</label>
			</th>
			<td>
				<input type="text" name="wpfs_members_cookie_expiration" value="<?php echo $options['wpfs_members_cookie_expiration']; ?>" class="regular-text code">

				<p class="description">The amount of time (in seconds) a member can be logged in before having
					to re-log again. Default is 2 days.</p>
			</td>
		</tr>
	</table>
	<p class="submit">
		<button class="button button-primary" type="submit">Update Settings</button>
		<img src="<?php echo plugins_url( '../assets/images/loader.gif', dirname( __FILE__ ) ); ?>" alt="Loading..." class="showLoading"/>
	</p>
</form>
