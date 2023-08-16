<?php

$supportUrl = esc_url(
	add_query_arg(
		array(
			'utm_source'   => 'plugin-wpfsm',
			'utm_medium'   => 'help-page',
			'utm_campaign' => 'v' . MM_WPFS_Members::VERSION,
			'utm_content'  => 'support-url'
		),
		'https://paymentsplugin.com/support'
	)
);

?>
<div id="contextual-help-columns">
	<div class="contextual-help-tabs">
		<ul>
			<li id="tab-link-quick_start" class="active">
				<a href="#tab-panel-quick_start" aria-controls="tab-panel-quick_start"> Quick Start</a>
			</li>
			<li id="tab-link-import" class="">
				<a href="#tab-panel-import" aria-controls="tab-panel-import"> Importing subscribers from Stripe</a>
			</li>
			<li id="tab-link-localization" class="">
				<a href="#tab-panel-localization" aria-controls="tab-panel-localization"> How to translate the
					plugin</a>
			</li>
			<li id="tab-link-glossary" class="">
				<a href="#tab-panel-glossary" aria-controls="tab-panel-glossary"> Glossary</a>
			</li>
			<li id="tab-link-misc" class="">
				<a href="#tab-panel-misc" aria-controls="tab-panel-misc"> Premium support</a>
			</li>
		</ul>
	</div>
	<div class="contextual-help-tabs-wrap">
		<div id="tab-panel-quick_start" class="help-tab-content active" style="">
			<h2>Quick Start</h2>
			<p>Welcome to WP Full Stripe Members (WPFSM). WPFSM is a fully featured membership plugin add-on for WP Full
				Stripe. Using WPFSM you can allow your subscribers
				exclusive tiered access to your WordPress content. WPFSM also allows your members to manage their
				membership from the built in "my account" page.<br/>
				Using WPFSM you can quickly and easily choose which content you'd like to protect and what tier of
				membership you would like to allow access to it.</p>
			<ol>
				<li>Ensure WP Full Stripe is installed and setup (API keys etc.)</li>
				<li>Create subscription plan(s) in WP Full Stripe for each Role</li>
				<li>Create subscription form and add new plans in WP Full Stripe</li>
				<li>Update Roles tab to link Roles to subscription plans</li>
				<li>Create new page/post and place subscription form shortcode to allow users to subscribe</li>
				<li>Create new page/post and place [wpfs_members_account] to allow members to manage their account</li>
				<li>Update Settings tab to your own preferences</li>
				<li>Go through each page/post you would like protected and check 'protected' box and choose Role.</li>
				<li>Now when users subscribe to plans with roles associated, they will have access to protected
					content.
				</li>
			</ol>
			<h2>More Details</h2>
			<h3>Roles</h3>
			<p>Roles are how we determine membership access levels. You assign a role to a subscription plan you have
				created via your Stripe dashboard or through the WP Full Stripe 'Subscriptions' section.
				There are currently 5 roles included with WPFSM: Basic, Bronze, Silver, Gold and All Access.<br/>
				Roles are ranked in the same order with Basic being the lowest and All Access being the highest. The
				rank determines what content a member can access. For example, a member with the Silver role
				can access all content marked as Basic, Bronze and Silver but has no access to content marked Gold or
				All Access.</p>
			<h3>Setting up Subscription Plans</h3>
			<p>You should create a new subscription plan for each role and use the 'Roles' tab in the members section to
				assign each role to it's own subscription plan. If you don't wish to use a Role, simply select
				'Not Used' from the drop-down list and that Role will not be available.</p>
			<p>You can now create your subscription forms in WP Full Stripe as usual. If you include a subscription plan
				that has been assigned a role, and your customer subscribes to that role, WPFSM will create
				a WordPress account for that customer and assign them the role you chose. This customer is now a Member
				of your website and can log in using the email address they used to subscribe. They
				will also be able to access all content you have marked protected as long as their Role is ranked high
				enough.</p>
			<h3>Marking Content Access Roles</h3>
			<p>WPFSM adds a new meta box to your create/edit post page. It simply contains a checkbox and a drop-down
				list. The checkbox will enable/disable protection of the post/page you are editing.
				Protection means that only members with the appropriate Role can view the page. <br/> The drop-down list
				contains the available Roles. Setting the Role here to 'Gold' would mean only members
				with 'Gold' or above could access the page. Similarly, setting the Role here to 'Basic' would mean all
				subscribing members could access the page.</p>
			<h3>Members Login</h3>
			<p>Members log into WordPress using the standard "wp-login.php" method for any WordPress user. WPFSM updates
				the login process to allow users to log in using their email address as the username.
				Members will be considered the lowest WordPress role, 'Subscriber' and only have read access levels.
				They can not edit content, view settings or anything else that requires higher access levels.</p>
			<h3>Members 'My Account' Page</h3>
			<p>WPFSM adds a new WordPress shortcode which allows you to place a 'My Account' section on any post or
				page. The shortcode is: <code>[wpfs_members_account]</code>. <br/>
				From this page, a logged in and subscribing member can view their membership status, update their credit
				card details, change their subscription plan to a higher or lower role plan and cancel their account.
				You can choose in the 'Settings' tab if you would like to allow some of these abilities on the 'My
				Account' page.</p>
			<h3>Edit Member Page</h3>
			<p>From the View tab you can see a list of all members, alongside each is an 'Edit' button which will take
				you to the edit members page. This page allows you to edit the members role, change their subscription
				plan
				and even cancel their membership. Descriptions of each of the options are below:</p>
			<h4>Change Membership Level</h4>
			<p>You change membership level by picking another Role with an associated plan (setup in the Roles tab),
				much like how Members can change their plan from the 'My Account' page. Changing this will update the
				members Role in the system, plus it will change their subscription plan in Stripe to match the plan for
				the new Role. Stripe prorates the difference in price between the plans so this may trigger either a new
				charge
				to the member or a credit on their account.</p>
			<h4>Cancel Membership</h4>
			<p>You can cancel membership outright and this will ensure the member is no longer subscribed to their
				subscription plan. It will keep the member active until the end of their payment period.</p>
			<h4>Change Role</h4>
			<p>This will ONLY change the members Role and nothing else. This means if you wish to instantly block a
				member from accessing protected content you can change the role to 'No Access' here.
				<strong>They will still be billed for the subscription plan they subscribed to.</strong> To stop this
				you would need to manually cancel their subscription from the Stripe dashboard.</p>
			<h3>Plugin Settings</h3>
			<p>The Settings tab allows you to configure some aspects of WPFSM behavior. Each option is explained
				below:</p>
			<h4>Block members who are past due</h4>
			<p>When Stripe attempts to take payment and it fails, it first marks the account as "past due". Based on
				your Stripe account settings (set in your Stripe Dashboard), Stripe will attempt to retry payment a few
				times.
				Once it has reached the limit of retries you set, it will then mark the account as either "unpaid" or
				"canceled". This option allows you to block members access as soon as the initial payment fails and the
				account becomes
				"past due". If you leave this setting off, members will still have access until Stripe marks their
				account "unpaid" or "canceled".</p>
			<h4>Allow members change membership level</h4>
			<p>This option enables your members to upgrade or downgrade their membership subscription from the 'My
				Account' page. When a member does this, Stripe will prorate the difference in costs i.e.
				if the new plan costs less, the member will only pay the difference on the next billing date. If it
				costs more, they will pay the difference right away.</p>
			<h4>Allow members to cancel membership</h4>
			<p>This option allows members to cancel their membership from the 'My Account' page. If you disable this
				then you must handle membership cancellation manually. When a member cancels, they will
				continue to have access until the end of the period paid for.</p>
			<h4>Turn on member status cron job</h4>
			<p>Member status is always checked when a user logs in. If WPFSM finds the subscription unpaid/canceled (or
				past due depending on settings) it will update the user and remove access at this time.
				The member status cron job is a daily check of all member statuses and will go through all members in
				your system to check their subscription status, removing access if needed.
				Turn this on if you would like extra checking of member status but be aware on sites with a large (500+)
				amount of members this may cause a short period of slowdown while processing.</p>
			<h4>Member login cookie timeout</h4>
			<p>By default, WordPress allows users to stay logged in for 48 hours (or 14 days if 'remember me' is
				checked) before they must log in again. You can change this timeout here. This can be useful if
				you want to avoid unpaid members remaining logged in for several days by leaving their browser open on
				your website.</p>
		</div>
		<div id="tab-panel-import" class="help-tab-content" style="">
			<h2>Importing subscribers from Stripe</h2>
			<p>
				If you've got subscribers in Stripe already when you start using Full Stripe Members then you need to
				import those subscribers for the plugin to function properly.<br/>
			</p>
			<h3>Prerequisites</h3>
			<p>Before importing subscribers from Stripe, please read the following list of requirements and todo items
				carefully:</p>
			<p>
			<ol>
				<li>Make sure you have linked subscription plans to roles on the
					<a target="_blank" href="<?php echo admin_url( "admin.php?page=fullstripe-members&tab=roles" ); ?>">Full
						Stripe -> Members -> Roles</a> page.
				</li>
				<li>The importer tries to fetch only customers with at least one subscription from Stripe.</li>
				<li><b>You can assign only a single role to a member.</b> If a customer has several subscriptions
					running, you have to choose the one to be imported manually.
				</li>
				<li>Stripe subscribers must have their email address filled in, otherwise the import will not work.</li>
				<li>The importer uses the "customer_name" metadata field as the full name of the customer. If this field
					is not set, the name of the member will not be set, either.
				</li>
			</ol>
			</p>
			<h3>How to import subscribers</h3>
			<p>
				You can import subscribers from Stripe by pressing the "Import subscribers from Stripe" button on the
				<a target="_blank" href="<?php echo admin_url( "admin.php?page=fullstripe-members" ); ?>">Full Stripe ->
					Members</a> page.
				It opens a dialog which guides you through the 5-step process with a wizard:
			</p>
			<ol>
				<li>
					<span style="text-decoration: underline;">Step 1 - Gathering information from Stripe</span><br/>
					In this automated step the importer counts the number of subscribers to be imported (subscribers
					with at least one subscription).<br/>
					<br/>
				</li>
				<li>
					<span style="text-decoration: underline;">Step 2 - Welcome</span><br/>
					The number of subscriber candidates to be imported is displayed on this page.<br/>
					You can proceed by pressing "Import >>>", or you can opt out by pressing "Close".<br/>
					<br/>
				</li>
				<li>
					<span style="text-decoration: underline;">Step 3 - Importing subscribers</span><br/>
					In this automated step subscribers without issues are imported silently. It might take a while.<br/>
					(Note: Subscribers without issues = they have only one subscription plan which is linked to a member
					role)<br/>
					<br/>
				</li>
				<li>
					<span style="text-decoration: underline;">Step 4 - Import feedback and reviewing conflicting subscribers</span><br/>
					You get a summary about:
					<ul>
						<li>The number of customers imported successfully</li>
						<li>The list of customers that cannot be imported (customers without any subscription plan
							linked to a member role)
						</li>
						<li>The list of customers that can be imported by manual intervention (the customer has several
							subscriptions running, you have to choose a member role )
						</li>
					</ul>
					You can proceed by pressing "Import selected >>>", or terminate the import by pressing "Close".<br/>
					<br/>
				</li>
				<li>
					<span style="text-decoration: underline;">Step 5 - Importing subscribers</span><br/>
					In this automated step subscribers to whom you've assigned a member role are imported silently. It
					might take a while.<br/>
					<br/>
				</li>
				<li>
					<span style="text-decoration: underline;">Step 6 - Importing finished</span><br/>
					You get a summary about the number of customers imported successfully.<br/>
					You can finish the import by pressing "Complete".<br/>
					<br/>
				</li>
			</ol>
			<h3>What happens during import?</h3>
			<p>For each Stripe subscriber the following steps are performed during import:</p>
			<ol>
				<li>
					<span style="text-decoration: underline;">Creating a Wordpress user</span><br/>
					The importer tries to create a Wordpress user for each member. The email address of the Stripe
					subscriber is matched against the email address of Wordpress users. If a Wordpress user already
					exists with that email address then this user will be used, otherwise a new user is created.<br/>
					The newly created Wordpress user has the following fields set:<br/>
					<ul>
						<li>User name: Set to the email address</li>
						<li>Email address: Set to the email address</li>
						<li>
							Password: Strong, random password generated by the importer.<br/>
							(Note: The member can log in by resetting her password first)
						</li>
					</ul>
				</li>
				<li>
					<span style="text-decoration: underline;">Adding a member role to the Wordpress user</span><br/>
					The import removes all roles of the Wordpress user, and adds the role pertaining to the member role.
				</li>
				<li>
					<span style="text-decoration: underline;">Creating a member record</span><br/>
					The importer creates a member record which associates the Wordpress user, the member role, and the
					subscription plan.
				</li>
			</ol>
			<h3>How can I remove imported members?</h3>
			<b>Note: Before proceeding with member removal, please back up your Wordpress database!</b><br/>
			<br/>
			If you need to remove members from Full Stripe Members then perform the following steps:<br/>
			<ol>
				<li>
					<span style="text-decoration: underline;">Remove the Wordpress user</span><br/>
					Go to the <a target="_blank" href="<?php echo admin_url( "users.php" ); ?>">Users</a> page and
					remove the user manually.
				</li>
				<li>
					<span style="text-decoration: underline;">Remove the member record from the database</span><br/>
					You have to remove the member row from the <b>wp_fullstripe_members</b> table in the database.
				</li>
			</ol>

		</div>
		<div id="tab-panel-localization" class="help-tab-content" style="">
			<h2>How to translate the plugin</h2>
			<p>You can translate the labels of the "My Account" page by following these steps:
			<ol>
				<li>
					Copy the "wp-content/plugins/wp-full-stripe-members/languages/wp-full-stripe-members.pot" file to
					"wp-content/plugins/wp-full-stripe-members/languages/wp-full-stripe-members-&lt;language code&gt;_&lt;country
					code&gt;.po" file<br/>
					where &lt;language code&gt; is the two-letter ISO language code and &lt;country code&gt; is the
					two-letter ISO country code.<br/>
					Please refer to
					<a href="http://www.gnu.org/software/gettext/manual/gettext.html#Locale-Names" target="_blank">Locale
						names</a> section of the <code>gettext</code> utility manual for more information.
				</li>
				<li>
					Edit the "wp-content/plugins/wp-full-stripe-members/languages/wp-full-stripe-members-&lt;language
					code&gt;_&lt;country code&gt;.po" file and add your translations to it.
				</li>
				<li>
					Use the <a href="http://po2mo.net" target="_blank">http://po2mo.net</a> website or a similar service
					to convert your .po file to an .mo file.
				</li>
				<li>
					If you don't want to upload your .po file to an external service, then run the <code>msgfmt</code>
					utility (part of the gettext distribution) to convert the .po file to an
					.mo file, for example:<br/><br/>
					<code>msgfmt -cv -o \<br/>
						wp-content/plugins/wp-full-stripe-members/languages/wp-full-stripe-members-de_DE.mo \<br/>
						wp-content/plugins/wp-full-stripe-members/languages/wp-full-stripe-members-de_DE.po
					</code>
				</li>
				<li>
					Make sure that the newly created .mo file is in the
					"wp-content/plugins/wp-full-stripe-members/languages" folder and its name conforms to
					"wp-full-stripe-members-&lt;language code&gt;_&lt;country code&gt;.mo".
				</li>
			</ol>
			</p>

		</div>
		<div id="tab-panel-glossary" class="help-tab-content" style="">
			<h2>Glossary</h2>
			<h3>Member</h3>
			<p>A user who has subscribed to a plan giving access to a role. Members can log in and access protected
				content appropriate for their role.</p>
			<h3>Role</h3>
			<p>A role is how content is tiered and how members are given access to those tiers. Both content and members
				may have roles associated with them.</p>
			<h3>Access Level</h3>
			<p>Roles have different access levels (or 'ranks') associated with them. For example, the Basic role has a
				lower access level than the Gold role.</p>
			<h3>My Account Page</h3>
			<p>The page or post where the <code>[wpfs_members_account]</code> shortcode is place. It allows members to
				manage their account settings.</p>
			<h3>Protected Content</h3>
			<p>Pages or posts that can not be accessed by non-members or by members with access levels (Roles) below the
				page/post access level.</p>
			<h3>Stripe Dashboard</h3>
			<p>Your Stripe account dashboard located at: <a href="https://dashboard.stripe.com/">https://dashboard.stripe.com/</a>
			</p>
		</div>
		<div id="tab-panel-misc" class="help-tab-content" style="">
			<h2>Premium support</h2>
			<p>You can visit our <a target="_blank" href="<?php echo $supportUrl; ?>">Support page</a> if you
				have a question. You can also subscribe for premium support for FREE by
				<a target="_blank" href="http://eepurl.com/5zJG1">adding your email address to our mailing list.</a></p>
			<a class="button button-primary" target="_blank" href="http://eepurl.com/5zJG1">Subscribe for premium
				support</a>
		</div>
	</div>
</div>
