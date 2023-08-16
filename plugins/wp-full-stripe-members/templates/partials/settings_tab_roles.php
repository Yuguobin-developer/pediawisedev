<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2016.04.15.
 * Time: 16:07
 */

$plans = MM_WPFS::getInstance()->get_plans();
if ( array_key_exists( 'data', $plans ) ) {
	$plans = $plans['data'];
}
$role_plans = MM_WPFS_Members::getInstance()->get_role_plans();
$role_names = MM_WPFS_Members::getInstance()->get_wp_role_names();
$options    = get_option( 'fullstripe_options' );

?>
<h2>Roles</h2>
<p>Roles are how you limit access to your content. We link roles to subscription plans so you can set what plan
	allows access to which role.
	For example, you could create a Gold subscription plan for $29.99/month and link it to the Gold role here.
	Then all subscribers of the Gold plan would
	gain access to content you have marked as Gold access or below.</p>
<?php if ( count( $plans ) == 0 ): ?>
	<div class="error"><p>No plans defined! Please create subscription plans first & make sure your Stripe API
			keys are set in WP Full Stripe Settings.</p></div>
<?php else: ?>
	<form action="" method="POST" id="wpfs-members-role-plans-form">
		<p class="tips"></p>
		<input type="hidden" name="action" value="wpfs_members_set_role_plans"/>
		<table class="form-table">
			<?php foreach ( $role_plans as $role => $plan ): ?>
				<tr valign="top">
					<th scope="row">
						<?php if ( array_key_exists( $role, $role_names ) ): ?>
							<label><?php echo $role_names[ $role ]; ?></label>
						<?php endif; ?>
					</th>
					<td>
						<select name="<?php echo $role; ?>">
							<option value="none">Not Used</option>
							<?php foreach ( $plans as $p ): ?>
								<option value="<?php echo $p->id; ?>" <?php echo ( $plan === $p->id ) ? 'selected="selected"' : '' ?> ><?php echo $p->product->name; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<p class="submit">
			<button class="button button-primary" type="submit">Save Roles</button>
			<img src="<?php echo plugins_url( '../assets/images/loader.gif', dirname( __FILE__ ) ); ?>" alt="Loading..." class="showLoading"/>
		</p>
	</form>
<?php endif; ?>
