<?php
$plans      = MM_WPFS::getInstance()->get_plans();
$role_plans = MM_WPFS_Members::getInstance()->get_role_plans();
$roleNames  = MM_WPFS_Members::getInstance()->get_wp_role_names();
$options    = get_option( 'fullstripe_options' );

$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'members';

?>
<div class="wrap">
	<h2> <?php echo __( 'Full Stripe Members', 'wp-full-stripe-members' ); ?> </h2>

	<div id="wpfsm-import-wizard" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="wpfsm-import-button">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
					<h4 class="js-title-step"></h4>
				</div>
				<div class="modal-body">
					<div class="row hide" data-step="1" data-title="Gathering data from Stripe">
						<div class="col-md-12">
							<div id="spinner1"></div>
							<div class="well text-center" style="min-height: 250px; overflow: auto">
								<div style="margin-top: 170px;">Gathering data from Stripe, please wait...</div>
							</div>
						</div>
					</div>
					<div class="row hide" data-step="2" data-title="Welcome">
						<div class="col-md-12">
							<div class="well" style="min-height: 250px; overflow: auto">
								<p>Welcome to the WP Full Stripe Members import wizard!</p>

								<p id="pre_import_summary"></p>
							</div>
						</div>
					</div>
					<div class="row hide" data-step="3" data-title="Import in progress">
						<div class="col-md-12">
							<div id="spinner3"></div>
							<div class="well text-center" style="min-height: 250px; overflow: auto">
								<div style="margin-top: 170px;">Importing subscribers, please wait...</div>
							</div>
						</div>
					</div>
					<div class="row hide" data-step="4" data-title="Import feedback and reviewing conflicting subscribers">
						<div class="col-md-12">
							<div class="well" style="min-height: 250px; overflow: auto">
								<p id="post_import_summary_imported_successfully"></p>

								<p id="post_import_summary_test_mode_count"></p>

								<div class="collapse" id="post_import_summary_test_mode_collapse">
									<table id="post_import_summary_test_mode" class="table table-condensed table-bordered table-striped">
										<thead>
										<tr>
											<th>Name and email</th>
											<th>Reason</th>
										</tr>
										</thead>
										<tbody></tbody>
									</table>
								</div>

								<p id="post_import_summary_cannot_import_count"></p>

								<div class="collapse" id="post_import_summary_cannot_import_collapse">
									<table id="post_import_summary_cannot_import" class="table table-condensed table-bordered table-striped">
										<thead>
										<tr>
											<th>Name and email</th>
											<th>Reason</th>
										</tr>
										</thead>
										<tbody></tbody>
									</table>
								</div>

								<p id="post_import_summary_can_import_manually_count"></p>

								<form id="plan_selector_form">
									<table id="post_import_summary_can_import_manually" class="table table-condensed table-bordered table-striped">
										<thead>
										<tr>
											<th>Customer</th>
											<th>Membership level</th>
										</tr>
										</thead>
										<tbody></tbody>
									</table>
								</form>
							</div>
						</div>
					</div>
					<div class="row hide" data-step="5" data-title="Import in progress">
						<div class="col-md-12">
							<div id="spinner5"></div>
							<div class="well text-center" style="min-height: 250px; overflow: auto">
								<div style="margin-top: 170px;">Importing subscribers, please wait...</div>
							</div>
						</div>
					</div>
					<div class="row hide" data-step="6" data-title="Import finished">
						<div class="col-md-12">
							<div class="well" style="min-height: 250px; overflow: auto">
								<p id="post_manual_import_summary_imported_successfully"></p>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default js-btn-step pull-left" data-orientation="cancel" data-dismiss="modal"></button>
					<button type="button" class="btn btn-primary js-btn-step" data-orientation="next"></button>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- /.modal -->

	<div id="updateDiv"><p><strong id="updateMessage"></strong></p></div>
	<h2 class="nav-tab-wrapper">
		<a href="?page=fullstripe-members&tab=members" class="nav-tab <?php echo $active_tab == 'members' ? 'nav-tab-active' : ''; ?>">Members</a>
		<a href="?page=fullstripe-members&tab=roles" class="nav-tab <?php echo $active_tab == 'roles' ? 'nav-tab-active' : ''; ?>">Roles</a>
		<a href="?page=fullstripe-members&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
		<a href="?page=fullstripe-members&tab=help" class="nav-tab <?php echo $active_tab == 'help' ? 'nav-tab-active' : ''; ?>">Help</a>
	</h2>

	<div class="wpfs-tab-content">
		<?php if ( $active_tab == 'members' ): ?>
		<div class="" id="members">
			<h2>
				<a class="button button-primary" href="<?php echo admin_url( "admin.php?page=wpfs-members-create" ); ?>">Create
					Member</a>
	            <span class="alignright">
		            <button id="wpfsm-import-button" class="button button-primary" data-toggle="modal" data-target="#wpfsm-import-wizard">Import subscribers from Stripe</button>
	            </span>
				<img src="<?php echo plugins_url( '/assets/images/loader.gif', dirname( __FILE__ ) ); ?>" alt="<?php esc_attr_e( 'Loading...', 'wp-full-stripe-members' ); ?>" class="showLoading"/>
			</h2>
			<form method="get">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
				<label><?php _e( 'Member: ', 'wp-full-stripe-members' ); ?></label><input type="text" name="member" size="35" placeholder="<?php _e( 'Enter user, member id, or email address', 'wp-full-stripe-members' ); ?>" value="<?php echo isset( $_REQUEST['member'] ) ? $_REQUEST['member'] : ''; ?>">
				<label><?php _e( 'Status: ', 'wp-full-stripe-members' ); ?></label>
				<select name="status">
					<option value="" <?php echo ! isset( $_REQUEST['status'] ) || $_REQUEST['status'] == '' ? 'selected' : ''; ?>><?php _e( 'All', 'wp-full-stripe-members' ); ?></option>
					<option value="active" <?php echo isset( $_REQUEST['status'] ) && $_REQUEST['status'] == 'active' ? 'selected' : ''; ?>><?php _e( 'Active', 'wp-full-stripe-members' ); ?></option>
					<option value="canceled" <?php echo isset( $_REQUEST['status'] ) && $_REQUEST['status'] == 'canceled' ? 'selected' : ''; ?>><?php _e( 'Canceled', 'wp-full-stripe-members' ); ?></option>
					<option value="unpaid" <?php echo isset( $_REQUEST['status'] ) && $_REQUEST['status'] == 'unpaid' ? 'selected' : ''; ?>><?php _e( 'Unpaid', 'wp-full-stripe-members' ); ?></option>
					<option value="past_due" <?php echo isset( $_REQUEST['status'] ) && $_REQUEST['status'] == 'past_due' ? 'selected' : ''; ?>><?php _e( 'Past due', 'wp-full-stripe-members' ); ?></option>
					<option value="trialing" <?php echo isset( $_REQUEST['status'] ) && $_REQUEST['status'] == 'trialing' ? 'selected' : ''; ?>><?php _e( 'Trialing', 'wp-full-stripe-members' ); ?></option>
					<option value="deleted" <?php echo isset( $_REQUEST['status'] ) && $_REQUEST['status'] == 'deleted' ? 'selected' : ''; ?>><?php _e( 'Deleted', 'wp-full-stripe-members' ); ?></option>
					<option value="unknown" <?php echo isset( $_REQUEST['status'] ) && $_REQUEST['status'] == 'unknown' ? 'selected' : ''; ?>><?php _e( 'Unknown', 'wp-full-stripe-members' ); ?></option>
				</select>
				<label><?php _e( 'Mode: ', 'wp-full-stripe-members' ); ?></label>
				<select name="mode">
					<option value="" <?php echo ! isset( $_REQUEST['mode'] ) || $_REQUEST['mode'] == '' ? 'selected' : ''; ?>><?php _e( 'All', 'wp-full-stripe-members' ); ?></option>
					<option value="live" <?php echo isset( $_REQUEST['mode'] ) && $_REQUEST['mode'] == 'live' ? 'selected' : ''; ?>><?php _e( 'Live', 'wp-full-stripe-members' ); ?></option>
					<option value="test" <?php echo isset( $_REQUEST['mode'] ) && $_REQUEST['mode'] == 'test' ? 'selected' : ''; ?>><?php _e( 'Test', 'wp-full-stripe-members' ); ?></option>
				</select>
					<span class="wpfs-search-actions">
						<button class="button button-primary"><?php _e( 'Search', 'wp-full-stripe-members' ); ?></button> <?php _e( 'or', 'wp-full-stripe-members' ); ?>
						<a href="<?php echo add_query_arg( array( 'page' => 'fullstripe-members' ), admin_url( 'admin.php' ) ); ?>"><?php _e( 'Reset', 'wp-full-stripe-members' ); ?></a>
					</span>
				<?php
				/** @var WP_List_Table $membersTable */
				$membersTable->prepare_items();
				$membersTable->display();
				?>
			</form>
		</div>
	</div>
	<?php elseif ( $active_tab == 'roles' ): ?>
		<?php include( 'partials/settings_tab_roles.php' ); ?>
	<?php elseif ( $active_tab == 'settings' ): ?>
		<?php include( 'partials/settings_tab_members.php' ); ?>
		<?php
	elseif ( $active_tab == 'help' ): ?>
		<h2><?php echo sprintf(
			/* translators: 1: current version */
				__( 'Full Stripe Members Help (v%s)', 'wp-full-stripe-members' ), MM_WPFS_Members::VERSION ); ?></h2>
		<?php include 'help.php'; ?>
	<?php endif; ?>
</div>
