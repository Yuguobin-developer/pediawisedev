<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2016.04.04.
 * Time: 9:52
 */
?>
<div class="changelog">
	<div class="feature-section images-stagger-right">
		<p>Below is a list of the most recent plugin updates. We are committed to continually improving WP Full Stripe.</p>
		<div class="changelog-updates">
            <strong>Nov 25, 2020 (v5.5.2)</strong>
            <blockquote>
                <p>Changed:</p>
                <ul>
                    <li>Fixed: Save card forms displayed an error when either email receipts or thank you pages were turned on.</li>
                    <li>Fixed: The subscription billing anchor day was sometimes off by a day due to timezone calculation issues.</li>
                    <li>Fixed: Capturing, refunding, and voiding one-time payments didn't work in WP admin.</li>
                    <li>Fixed: The create/edit one-time payment form page broke when the amount list was empty.</li>
                    <li>Fixed: The "One-time payment forms" page displayed an error after upgrading from WP Full Stripe Free.</li>
                    <li>Fixed: The "One-time payments" page displayed an error after upgrading from WP Full Stripe Free.</li>
                </ul>
            </blockquote>
            <strong>Oct 26, 2020 (v5.5.1)</strong>
            <blockquote>
                <p>Added:</p>
                <ul>
                    <li>Donation forms with custom amount recurring donations are here!</li>
                    <li>Form currency format is now configurable (decimal separator character, currency symbol, currency symbol position).</li>
                    <li>Form UI is available in 6 new languages (Danish, French, German, Italian, Japanese, Spanish)</li>
                    <li>The Manage Subscription page and payment forms can be used on the same page.</li>
                    <li>The Subscription Receipt email template supports the %INVOICE_URL% placeholder.</li>
                    <li>New display languages are available for checkout forms.</li>
                    <li>The display language of the Card Info field on inline forms is now configurable.</li>
                    <li>A description (decorated with placeholder tokens) can be added to saved cards.</li>
                </ul>
                <p>Changed:</p>
                <ul>
                    <li>The Card Info field is now full width.</li>
                    <li>The Cardholder's Name field is now a required field.</li>
                    <li>The subscription plan selector displays the plan recurring fee and interval as well.</li>
                    <li>No success message is displayed before redirecting to Thank you pages.</li>
                    <li>The Cardholder's Name and Email address fields are moved towards the bottom of the form, just north of the Card Info field.</li>
                    <li>Fixed: Some placeholders of the 'Subscription ended' email template didn't work properly.</li>
                    <li>Fixed: Logging in to the Manage Subscriptions page didn't work on some Multisite installations.</li>
                    <li>Fixed: REST API endpoint registrations complained about missing parameters (introduced in Wordpress v5.5.0).</li>
                    <li>Fixed: Reworked some calls to functions deprecated in PHP v7.4 .</li>
                </ul>
            </blockquote>
            <strong>Oct 10, 2020 (v5.5.0)</strong>
            <blockquote>
                <p>Added:</p>
                <ul>
                    <li>Donation forms with custom amount recurring donations are here!</li>
                    <li>Form currency format is now configurable (decimal separator character, currency symbol, currency symbol position).</li>
                    <li>Form UI is available in 6 new languages (Danish, French, German, Italian, Japanese, Spanish)</li>
                    <li>The Manage Subscription page and payment forms can be used on the same page.</li>
                    <li>The Subscription Receipt email template supports the %INVOICE_URL% placeholder.</li>
                    <li>New display languages are available for checkout forms.</li>
                    <li>The display language of the Card Info field on inline forms is now configurable.</li>
                    <li>A description (decorated with placeholder tokens) can be added to saved cards.</li>
                </ul>
                <p>Changed:</p>
                <ul>
                    <li>The Card Info field is now full width.</li>
                    <li>The Cardholder's Name field is now a required field.</li>
                    <li>The subscription plan selector displays the plan recurring fee and interval as well.</li>
                    <li>No success message is displayed before redirecting to Thank you pages.</li>
                    <li>The Cardholder's Name and Email address fields are moved towards the bottom of the form, just north of the Card Info field.</li>
                    <li>Fixed: Some placeholders of the 'Subscription ended' email template didn't work properly.</li>
                    <li>Fixed: Logging in to the Manage Subscriptions page didn't work on some Multisite installations.</li>
                    <li>Fixed: REST API endpoint registrations complained about missing parameters (introduced in Wordpress v5.5.0).</li>
                    <li>Fixed: Reworked some calls to functions deprecated in PHP v7.4 .</li>
                </ul>
            </blockquote>
            <strong>Jul 27, 2020 (v5.4.1)</strong>
            <blockquote>
                <p>Added:</p>
                <ul>
                    <li>The billing anchor day feature now supports subscription plans with trial.</li>
                </ul>
                <p>Changed:</p>
                <ul>
                    <li>Fixed: Submitting subscription forms with a billing anchor day later than the current day of the month threw an error.</li>
                    <li>Fixed: Custom VAT calculation didn't work when there was only one plan on the form.</li>
                    <li>Fixed: When saving cards, metadata for existing customers weren't updated.</li>
                    <li>Fixed: Payment in installments -type subscriptions weren't cancelled automatically in some cases.</li>
                </ul>
            </blockquote>
            <strong>Apr 23, 2020 (v5.4.0)</strong>
            <blockquote>
                <p>Added:</p>
                <ul>
                    <li>Billing cycle anchor day can be specified for monthly subscriptions.</li>
                    <li>Subscribers can download invoices from the "Manage subscriptions" page.</li>
                    <li>Administrators can turn off downloading invoices by subscribers.</li>
                    <li>Administrators can turn off cancelling subscriptions by subscribers.</li>
                    <li>Subscription plans now support day intervals.</li>
                    <li>Full Stripe app icon displayed on the WP admin dashboard.</li>
                </ul>
                <p>Changed:</p>
                <ul>
                    <li>Improved the scheduler that stops subscriptions with cancellation counts (payment in installment plans).</li>
                    <li>The "Payment details" popover doesn't display decimal places for amounts in zero-decimal currencies (like JPY).</li>
                    <li>The "Payment details" popover doesn't display a quantity label when purchased quantity is one.</li>
                    <li>Renamed the "Settings / Users" page to "Settings / Security".</li>
                </ul>
            </blockquote>
            <strong>Mar 17, 2020 (v5.3.0)</strong>
            <blockquote>
                <p>Changed:</p>
                <ul>
                    <li>WP Full Stripe can run concurrently with other Stripe plugins.<br/>
                        (Moved the Stripe PHP client library to a custom namespace)</li>
                    <li>Fixed conflict of the "Payment details" popover with some Wordpress themes.</li>
                </ul>
            </blockquote>
            <strong>Feb 26, 2020 (v5.2.0)</strong>
            <blockquote>
                <p>Added:</p>
                <ul>
                    <li>Customers can purchase subscriptions in bulk.</li>
                    <li>Subscribers can change subscription quantity on the "Manage subscriptions" page.</li>
                    <li>Subscription plans can be selected by passing an URL parameter to subscription pages.</li>
                    <li>Custom amount can be set on one-time payment forms by passing an URL parameter to payment pages.</li>
                    <li>Custom placeholder tokens can be added programmatically to email templates.</li>
                    <li>Metadata can be added programmatically to transactions (think affiliate id).</li>
                </ul>
                <p>Changed:</p>
                <ul>
                    <li>Updated Stripe PHP client to v7.24.0 .</li>
                    <li>Updated Freemius SDK to v2.3.2 .</li>
                    <li>Fixed webhook event processing on checkout forms.</li>
                </ul>
            </blockquote>
            <strong>Dec 9, 2019 (v5.1.0)</strong>
            <blockquote>
                <p>Added:</p>
                <ul>
                    <li>Inline forms can collect shipping address.</li>
                    <li>Checkout forms can be protected by Google reCaptcha.</li>
                    <li>The plan selector is hidden automatically when only one plan is added to the form.</li>
                    <li>The "Update card" function of "Manage subscriptions" has become SCA-compliant.</li>
                </ul>
                <p>Changed:</p>
                <ul>
                    <li>Fixed the auto-update feature because it didn't work for some Envato customers.</li>
                    <li>Fixed the "Card update" function on the "Manage subscriptions" page, it didn't update the payment method for some subscribers.</li>
                    <li>Fixed a checkout form related periodic task which caused a lot of API errors.</li>
                </ul>
            </blockquote>
            <strong>Oct 28, 2019 (v5.0.3)</strong>
            <blockquote>
                <ul>
                    <li>Fix: Checkout subscription forms fail when simple button layout is active.</li>
                    <li>Fix: Checkout subscription forms crash when simple button layout is active and there is no valid plan selected.</li>
                    <li>Fix: One-time payments fail when the form has an empty description.</li>
                    <li>Fix: Leaves subscription status as 'Incomplete' when subscribing to a plan with a trial period.</li>
                    <li>Fix: The plugin wouldn't let customers pay on inline forms with reCaptcha turned on and 3DS cards.</li>
                    <li>Fix: Subscription forms with no valid plan don't return a user friendly error message.</li>
                    <li>Fix: Subscription forms don't validate the email address in certain cases.</li>
                    <li>Fix: The "Manage subscriptions" page doesn't display the subscriber's active subscriptions.</li>
                    <li>Fix: The plugin logs errors into the web server log about checkout sessions it cannot verify.</li>
                </ul>
            </blockquote>
            <strong>Sep 19, 2019 (v5.0.2)</strong>
            <blockquote>
                <ul>
                    <li>Fixed issue related to billing address handling on checkout subscription forms.</li>
                    <li>Fixed issue of subscription plans with trial period not working on inline subscription forms.</li>
                    <li>Fixed issue of database patches not applied correctly on MySql 5.5 database servers.</li>
                    <li>Fixed issue of delete local action not working properly for saved cards.</li>
                </ul>
            </blockquote>
            <strong>Sep 13, 2019 (v5.0.1)</strong>
            <blockquote>
                <ul>
                    <li>Fixed an issue of checkout forms not redirecting back to the starting page after payment.</li>
                    <li>Fixed an issue of checkout one-time payment forms displaying an error message when submitting custom donation amounts.</li>
                    <li>Now the plugin sets the billing address of customers on checkout subscription forms (because Stripe doesn't).</li>
                </ul>
            </blockquote>
            <strong>Sep 12, 2019 (v5.0.0)</strong>
            <blockquote>
                <ul>
                    <li>The plugin is now SCA-compliant, please read our blog post: <a target="_blank" href="https://paymentsplugin.com/blog/wp-full-stripe-sca-ready">https://paymentsplugin.com/blog/wp-full-stripe-sca-ready</a> .</li>
                    <li>The "Manage subscriptions" feature now works without subscription data in the Wordpress database (no need to import subscriptions).</li>
                </ul>
            </blockquote>
            <strong>Aug 12, 2019 (v4.2.0)</strong>
            <blockquote>
                <ul>
                    <li>Added %TRANSACTION_ID% placeholder to emails and thank you pages of all form types.</li>
                    <li>Implemented Wordpress authentication option for the "Manage subscriptions" feature.</li>
                    <li>Now firing Wordpress actions before and after cancelling subscriptions.</li>
                    <li>Now showing the CSS selector on the "Appearance" tab of all form types for easier CSS customizations.</li>
                    <li>Made the plugin demo mode more strict.</li>
                </ul>
            </blockquote>
            <strong>Jun 29, 2019 (v4.1.2)</strong>
            <blockquote>
                <ul>
                    <li>Bugfix: Updated the Freemius SDK to v2.3.0 to avoid a fatal error when installing the plugin on Wordpress v5.2.x.</li>
                </ul>
            </blockquote>
            <strong>Jun 25, 2019 (v4.1.1)</strong>
            <blockquote>
                <ul>
                    <li>Refined form CSS based on feedback from our customers.</li>
                </ul>
            </blockquote>
            <strong>Jun 13, 2019 (v4.1.0)</strong>
            <blockquote>
                <ul>
                    <li>Modified plugin to be compatible with several licensing engines (EDD, Freemius).</li>
                </ul>
            </blockquote>
            <strong>Jun 20, 2019 (v4.0.3)</strong>
            <blockquote>
                <ul>
                    <li>Refined form CSS based on feedback from our customers.</li>
                </ul>
            </blockquote>
            <strong>May 28, 2019 (v4.0.2)</strong>
            <blockquote>
                <ul>
                    <li>IMPORTANT: WP Full Stripe v4.0.2 requires PHP 5.5 or greater</li>
                    <li>IMPORTANT: WP Full Stripe v4.0.2 has new a form design, it's not compatible with the old design. Your custom CSS rules won't work.</li>
                    <li>UPDATE ONLY IF you have tested v4.0.2 in your test/staging environment.</li>
                    <li>Bugfix: Javascript and CSS files of the plugin weren't loaded on some websites</li>
                    <li>Bugfix: The "Payment details" popover displayed "Null" when only one plan was added to a subscription form</li>
                </ul>
            </blockquote>
            <strong>May 24, 2019 (v4.0.1)</strong>
            <blockquote>
                <ul>
                    <li>IMPORTANT: WP Full Stripe v4.0.1 requires PHP 5.5 or greater</li>
                    <li>IMPORTANT: WP Full Stripe v4.0.1 has new a form design, it's not compatible with the old design. Your custom CSS rules won't work.</li>
                    <li>UPDATE ONLY IF you have tested v4.0.1 in your test/staging environment.</li>
                    <li>Bugfix: Payment currency was always displayed as USD in popup one-time payment forms of the "Select amount from list" payment type</li>
                    <li>Bugfix: Billing and shipping address was not set properly in some cases on popup forms</li>
                </ul>
            </blockquote>
            <strong>May 23, 2019 (v4.0.0)</strong>
            <blockquote>
                <ul>
                    <li>IMPORTANT: WP Full Stripe v4.0.0 requires PHP 5.5 or greater</li>
                    <li>IMPORTANT: WP Full Stripe v4.0.0 has new a form design, it's not compatible with the old design. Your custom CSS rules won't work.</li>
                    <li>UPDATE ONLY IF you have tested v4.0.0 in your test/staging environment.</li>
                    <li>Professional, new look for all form types</li>
                    <li>Professional, new look for the "Manage subscriptions" page</li>
                    <li>Standard and compact inline one-time payment forms are merged and live on as inline forms with new, unified look</li>
                    <li>Inline forms now use Stripe Elements for collecting card details</li>
                    <li>Subscription forms can display plans as radio button list or dropdown</li>
                    <li>Subscription forms have a new "Payment details" popover for subscription overview (setup fee, plan, VAT, total)</li>
                    <li>One-time payment forms can display amounts as radio button list or dropdown</li>
                    <li>Added donation look for one-time payments</li>
                    <li>Vastly improved error feedback on all forms</li>
                    <li>Stripe PHP client upgraded to v6.27.0</li>
                </ul>
            </blockquote>
            <strong>Apr 30, 2019 (v3.16.3)</strong>
            <blockquote>
                <ul>
                    <li>Fixed an issue of not displaying feedback after payment with certain billing countries</li>
                    <li>Fixed an issue of not working when other plugins use Google reCaptcha</li>
                </ul>
            </blockquote>
            <strong>Feb 14, 2019 (v3.16.2)</strong>
            <blockquote>
                <ul>
                    <li>Added Google reCaptcha support (v2) for inline forms</li>
                    <li>Added support for custom field placeholders in one-time payment form descriptions</li>
                    <li>Fixed issue with the %PRODUCT_NAME% placeholder on one-time popup forms with the "Select amount from list" payment type</li>
                    <li>Fixed HTML escaping issue for placeholders with single and double quotes in email notifications and on Thank you pages</li>
                </ul>
            </blockquote>
            <strong>Oct 8, 2018 (v3.16.1)</strong>
            <blockquote>
                <ul>
                    <li>Added verifications to make the CVC/CVV code required (even when Stripe doesn't require it)</li>
                    <li>You can select more plans on a form due to increased database column size (from 255 characters to 2048 characters)</li>
                    <li>Fixed an issue of cancelling subscriptions not working on the "Manage subscriptions" page when being logged in to WordPress</li>
                    <li>Fixed an issue of subscription trials not working</li>
                    <li>Fixed an issue of website assets not loading when Wordpress is hosted on Windows webservers (css, js, and image files)</li>
                </ul>
            </blockquote>
            <strong>Sep 3, 2018 (v3.16.0)</strong>
            <blockquote>
                <ul>
                    <li>Added "Authorize & capture" support for one-time payments</li>
                    <li>Reworked how the setup fee works so it's a proper invoice item now, and tax can be added on top of it</li>
                    <li>Fixed an issue of customer name and billing address not set properly on Stripe invoices</li>
                    <li>Renamed and moved the "Card captures" menu to the "Saved cards" menu</li>
                    <li>Added webhook event handlers for one-time payment state changes (refunded, pending, expired, failed, captured)</li>
                </ul>
            </blockquote>
            <strong>Jul 11, 2018 (v3.15.1)</strong>
            <blockquote>
                <ul>
                    <li>IMPORTANT: Update to this version if you are using webhooks and subscriptions that end after certain number of charges</li>
                    <li>Fixed an issue related to subscriptions not ending automatically</li>
                    <li>Changed the way javascript files are loaded so the plugin is compatible with more themes</li>
                </ul>
            </blockquote>
            <strong>May 25, 2018 (v3.15.0)</strong>
            <blockquote>
                <ul>
                    <li>GDPR-compatible forms with an option to add a "Terms of use" checkbox</li>
                    <li>Self-service area for subscribers to update credit card data, and cancel subscriptions (with Google reCaptcha protection)</li>
                    <li>Configurable payment description for all one-time payment forms</li>
                    <li>Changed the order of the Stripe API keys (publishable, secret) on the "Settings" page</li>
                    <li>Made changes to be compatible with the WP Mandrill plugin</li>
                </ul>
            </blockquote>
            <strong>Apr 20, 2018 (v3.14.0)</strong>
            <blockquote>
                <ul>
                    <li>Card capture forms for customers to submit credit card data (so you can charge them later).</li>
                    <li>Error message displayed upon form submit when secret and publishable API keys are entered in the wrong order.</li>
                    <li>Updated the Stripe PHP client to v6.4.1</li>
                    <li>Made subscription management compatible with v6.0 Stripe API (products as parents of subscription plans).</li>
                    <li>Removed Alipay support temporarily.</li>
                    <li>Removed Bitcoin support.</li>
                    <li>"Lock email address field for logged in users" feature turned into "Fill in email address for logged in users".</li>
                    <li>Fixed an issue related to not displaying error message when an empty form was submitted.</li>
                    <li>Fixed an issue related to not saving the default billing country on subscription forms.</li>
                    <li>Fixed an issue related to not displaying properly popup button labels containing single quote characters.</li>
                    <li>Fixed the bug that the "Could not find payment information" error message was not localizable.</li>
                </ul>
            </blockquote>
            <strong>Feb 12, 2018 (v3.13.1)</strong>
            <blockquote>
                <ul>
                    <li>Fixed a subscription plan creation/editing/listing issue caused by a new Stripe API version.</li>
                    <li>Fixed an issue related to optional custom fields.</li>
                </ul>
            </blockquote>
            <strong>Jan 8, 2018 (v3.13.0)</strong>
            <blockquote>
                <ul>
                    <li>Added shipping address support to popup forms (both one-time and subscription).</li>
                    <li>Increased the maximum number of custom fields per form to 10 (used to be 5).</li>
                    <li>The form name is displayed for each payment in WP admin.</li>
                    <li>The form name is added as metadata to one-time payments and subscriptions.</li>
                    <li>Performed small tweaks to make inline and popup form layouts alike (aligned labels to left, removed fieldset element).</li>
                    <li>Removed security verification that caused nonce errors on cached websites.</li>
                    <li>Condensed the billing address and the shipping address to one metadata each.</li>
                    <li>For one-time payments, all metadata is added to the Stripe charge object (the Stripe customer object is left intact).</li>
                    <li>For subscriptions, all metadata is added to the Stripe subscription object (the Stripe customer object is left intact).</li>
                </ul>
            </blockquote>
            <strong>November 21, 2017 (v3.12.1)</strong>
            <blockquote>
                <ul>
                    <li>Fixed the product description not being displayed on popup subscription forms.</li>
                </ul>
            </blockquote>
            <strong>November 13, 2017 (v3.12.0)</strong>
            <blockquote>
                <ul>
                    <li>Added tax (VAT) support to subscription forms.</li>
                    <li>Added option for simple popup subscription button (no plan selector, no plan info label, no custom fields, no coupon field).</li>
                    <li>Added option for selecting the display language of popup one-time payment forms, and popup subscription forms.</li>
                </ul>
            </blockquote>
            <strong>September 5, 2017 (v3.11.1)</strong>
            <blockquote>
                <ul>
                    <li>Fixed a billing address validation issue on inline one-time forms.</li>
                </ul>
            </blockquote>
            <strong>August 22, 2017 (v3.11.0)</strong>
            <blockquote>
                <ul>
                    <li><b>IMPORTANT! This release contains critical security fixes and critical bugfixes. Please update your Full Stripe installation as soon as possible!!!</b></li>
                    <li>Added support for "custom amount" and "select amount from list" payment types to popup one-time payment forms (Stripe checkout forms).</li>
                    <li>Updated the Stripe PHP client to the latest version (v5.1.1)</li>
                </ul>
            </blockquote>
            <strong>August 18, 2017 (v3.10.0)</strong>
            <blockquote>
                <ul>
                    <li>Added popup (Stripe checkout) support to subscription forms.</li>
                    <li>Added custom field support to all form types (one-time and subscription, inline and popup).</li>
                    <li>Fixed issues with zero-decimal currencies (like the Japanese Yen).</li>
                    <li>Fixed WP admin URLs linking to Stripe charges and Stripe subscriptions.</li>
                    <li>Fixed the value of the %AMOUNT% placeholder on subscription forms where both setup fee and plan fee have to be charged.</li>
                    <li>Fixed an issue of not being able to select certain payment confirmation ("Thank you") pages for redirects.</li>
                </ul>
            </blockquote>
            <strong>April 19, 2017 (v3.9.1)</strong>
            <blockquote>
                <ul>
                    <li>Fixed a bug on the edit page of popup forms in WP admin.</li>
                </ul>
            </blockquote>
            <strong>April 18, 2017 (v3.9.0)</strong>
            <blockquote>
                <ul>
                    <li>Payment currency can be set per form.</li>
                    <li>Payment currency can be set per subscription plan.</li>
                    <li>Setup fee can be set per subscription plan.</li>
                    <li>Added the %DATE% placeholder token to all email notifications.</li>
                    <li>Split all form editor pages into tabs in order to make room for new features.</li>
                </ul>
            </blockquote>
            <strong>March 1, 2017 (v3.8.2)</strong>
            <blockquote>
                <ul>
                    <li>Fixed a bug related to customizable "Thank you" (payment confirmation) pages.</li>
                </ul>
            </blockquote>
            <strong>February 26, 2017 (v3.8.1)</strong>
            <blockquote>
                <ul>
                    <li>Fixed a bug related to PHP 5.3.x compatibility.</li>
                </ul>
            </blockquote>
            <strong>February 24, 2017 (v3.8.0)</strong>
            <blockquote>
                <ul>
                    <li>All forms are responsive and mobile friendly.</li>
                    <li>"Thank you" pages after payment are customizable with placeholder tokens.</li>
                    <li>Payment types "Select amount from list" and "Custom amount" can be combined on one-time payment (and donation) forms.</li>
                    <li>New option added to make custom fields mandatory.</li>
                    <li>Minimum plugin requirements are verified at activation time.</li>
                    <li>Added collision prevention code to handle those cases when other plugins load jQuery in a non-standard way.</li>
                    <li>Fixed a bug with the %PRODUCT_NAME% placeholder when the payment type is "Select Amount from List".</li>
                    <li>Fixed a bug with form names containing only digits.</li>
                    <li>Fixed a bug with error messages when invalid card expiry date is provided.</li>
                </ul>
            </blockquote>
            <strong>February 15, 2017 (v3.7.5)</strong>
            <blockquote>
                <ul>
                    <li>Fixed an issue with payment descriptions containing commas when the payment type is "Select Amount from List".</li>
                </ul>
            </blockquote>
            <strong>January 23, 2017 (v3.7.4)</strong>
            <blockquote>
                <ul>
                    <li>Increased amount length from 6 digits to 8.</li>
                    <li>The Stripe PHP client has been upgraded to v4.4.0 .</li>
                    <li>Fixed a bug that caused the product description not properly being mapped to the %PRODUCT_NAME% placeholder on Stripe checkout forms.</li>
                </ul>
            </blockquote>
			<strong>December 2, 2016 (v3.7.3)</strong>
			<blockquote>
				<ul>
					<li>The Stripe PHP client has been upgraded to v4.2.0 .</li>
				</ul>
			</blockquote>
			<strong>November 24, 2016 (v3.7.2)</strong>
			<blockquote>
				<ul>
					<li>Fixed a bug related to missing button icons in WP Admin.</li>
					<li>Fixed a bug that prevented the plugin from being activated (class name collision with other plugins).</li>
					<li>Plan label handling modified to work with themes that remove empty &lt;p&gt;tags.</li>
				</ul>
			</blockquote>
			<strong>November 15, 2016 (v3.7.1)</strong>
			<blockquote>
				<ul>
					<li>Error pane handling modified to work with themes that remove empty &lt;p&gt;tags.</li>
					<li>Fixed a bug that would prevent the plugin from displaying more than 100 subscription plans.</li>
					<li>Removed placeholders for the card and name fields on subscription forms</li>
				</ul>
			</blockquote>
			<strong>November 2, 2016 (v3.7.0)</strong>
			<blockquote>
				<ul>
					<li>Any number of forms can be embedded into a page or post!</li>
					<li>The plugin can auto-update to the latest version with the click of a button!</li>
					<li>Form shortcode generator added for embedding forms easily into pages and posts (simple copy'n'paste)!</li>
					<li>AliPay support added for one-time payments on Stripe checkout-style payment forms.</li>
					<li>Subscriptions can now be deleted on the "Subscribers" page.</li>
					<li>Country dropdown has been added to the billing address on all form types.</li>
					<li>The "Action" column has been redesigned on all admin pages (iconified buttons).</li>
					<li>The "Payments" page has a new layout, it is more structured and more spacious.</li>
					<li>The "Payments" page has got a search box. Find payments based on customer's name and email address, Stripe customer id, Stripe charge id, or mode (live/test).</li>
					<li>The "Settings" page can now be extended by add-ons.</li>
					<li>"Newsfeed" tab has been added to the "About" page.</li>
					<li>Fixed an issue related to being unable to save subscription forms with selected subscription plan names containing spaces.</li>
					<li>The "Transfers" feature has been removed due to incompatibility with the latest Stripe API (will be reintroduced later).</li>
					<li>The Stripe client and API used by the plugin has been upgraded to v3.21.0 in order to be compatible with TLS 1.2.</li>
				</ul>
			</blockquote>
			<strong>June 3, 2016 (v3.6.0)</strong>
			<blockquote>
				<ul>
					<li>Support for subscriptions that terminate after certain number of charges!</li>
					<li>Subscriptions can be cancelled from the "Subscribers" page.</li>
					<li>The "Subscribers" page has a new layout, it is more structured and more spacious.</li>
					<li>The "Subscribers" page has a search box. Find subscriptions based on subscribers' name and email address, Stripe customer id, Stripe subscription id, or mode (live/test).</li>
					<li>The "Settings / E-mail receipts" page has a new layout for managing e-mail notifications (new email types coming soon).</li>
					<li>Now you can translate form titles and custom field labels to other languages as well.</li>
					<li>Stripe webhook support added for advanced features in the coming releases.</li>
					<li>Fixed an issue related to the value of the %PLAN_AMOUNT% token when a coupon is applied to the subscription.</li>
					<li>Fixed an issue related to plan ids, now they can contain comma characters.</li>
					<li>Improved error handling and error messages for internal errors.</li>
				</ul>
			</blockquote>
			<strong>March 15, 2016 (v3.5.1)</strong>
			<blockquote>
				<ul>
					<li>Added %PRODUCT_NAME% token to email receipts (used when payment type is "Select Amount from List")</li>
					<li>Added extra error handling for failed cards (declined, expired, invalid CVC).</li>
					<li>Fixed issue with long plan lists on subscription forms.</li>
				</ul>
			</blockquote>
			<strong>February 21, 2016 (v3.5.0)</strong>
			<blockquote>
				<ul>
					<li>Added Bitcoin support for checkout forms!</li>
					<li>The e-mail field can be locked and filled in automatically for logged in users.</li>
					<li>Success messages and error messages are scrolled into view automatically.</li>
					<li>The spinning wheel has been moved next to the payment button on all form types.</li>
					<li>The lists on the "Payments" and "Subscribers" pages now are descending and ordered by date by default.</li>
					<li>Fixed an issue with payment forms on Wordpress 4.4.x: the submitted forms never returned.</li>
				</ul>
			</blockquote>
			<strong>December 6, 2015 (v3.4.0)</strong>
			<blockquote>
				<ul>
					<li>New payment type introduced on payment forms: the customer can select the payment amount from a list.</li>
					<li>The "Settings" page is now easier to use, it has been divided into three tabs: Stripe, Appearance, and Email receipts.</li>
					<li>The e-mail receipt sender address is now configurable.</li>
					<li>All payment forms (payment, checkout, subscription) add the same metadata fields to the Stripe "Payment" and "Customer" objects.</li>
					<li>CSS style improvements to assure compatibility with the KOMetrics plugin.</li>
				</ul>
			</blockquote>
			<strong>October 30, 2015 (v3.3.0)</strong>
			<blockquote>
				<ul>
					<li>The plugin is translation-ready! You can translate it to your language without touching the plugin code. (Public labels only)</li>
					<li>Usability improvements made to the currency selector on the "Settings" page.</li>
					<li>Improved error handling on all form types (payment, checkout, and subscription).</li>
					<li>Version number of the plugin is displayed on the "About" and "Help" pages in WP Admin.</li>
					<li>Confirmation dialog has been added to delete operations where it was missing.</li>
					<li>Fixed an issue on subscription forms with the progress indicator spinning endlessly, never returning.</li>
					<li>Fixed an issue on checkout forms with the %CUSTOMERNAME% token not resolved properly in email receipts.</li>
				</ul>
			</blockquote>
			<strong>August 22, 2015</strong>
			<blockquote>
				<ul>
					<li>Subscription plans on subscription forms can be reordered by using drag and drop!</li>
					<li>Subscription plans can be modified or deleted directly from WP Full Stripe.</li>
					<li>Page or post redirects can be selected using an autocomplete, no time wasted with figuring out post ids.</li>
					<li>Arbitrary URLs can be used as redirect URLs.</li>
					<li>Placeholder tokens for custom fields are available in email receipts.</li>
				</ul>
			</blockquote>
			<strong>July 18, 2015</strong>
			<blockquote>
				<ul>
					<li>Fixed a bug with Stripe receipt emails on subscription forms.</li>
				</ul>
			</blockquote>
			<strong>June 23, 2015</strong>
			<blockquote>
				<ul>
					<li>Now you can use plugin email receipts for all form types (payment, checkout, and subscription) !!!</li>
					<li>New email receipt tokens: customer email, subscription plan name, subscription plan amount, subscription setup fee.</li>
					<li>Separate email template and subject field for payment forms and subscription forms.</li>
					<li>Support for all countries supported by Stripe (20 countries currently).</li>
					<li>Support for all currencies supported by Stripe (138 currencies in total, number varies by country).</li>
				</ul>
			</blockquote>
			<strong>December 30, 2014</strong>
			<blockquote>
				<ul>
					<li>You can now use multiple checkout buttons on the same page!</li>
					<li>Checkout button styling can now be disabled (useful for theme conflicts).</li>
					<li>Some minor changes added for future extensions.</li>
				</ul>
			</blockquote>
			<strong>December 5, 2014</strong>
			<blockquote>
				<ul>
					<li>Removing form input placeholders as they conflict with some themes.</li>
					<li>SSN is no longer a required field for transfer forms.</li>
					<li>Support for KO Metrics added.</li>
					<li>Bugfix: settings upgrade properly when installing a new version of the plugin.</li>
				</ul>
			</blockquote>
			<strong>November 4, 2014 - We're now at version 3.0! Over 1 years worth of regular updates & new features</strong>
			<blockquote>
				<ul>
					<li>You can now add up to 5 custom input fields to payment & subscription forms!</li>
					<li>Subscribers and payment records can now be deleted locally (they remain in your Stripe dashboard).</li>
					<li>Lots of UI/UX improvements including appropriate table styling and useful redirects.</li>
					<li>Added livemode status to subscribers.</li>
					<li>Cardholder name correctly added to payment details.</li>
				</ul>
			</blockquote>
			<strong>October 16, 2014</strong>
			<blockquote>
				<ul>
					<li>Email address is now a required field on payment forms.</li>
					<li>We now check for existing Stripe Customers before creating new ones.</li>
					<li>Updated the Stripe PHP Bindings to the latest version.</li>
					<li>Fixed deprecated warnings on payment and subscription table pages.</li>
					<li>Fixed a bug with trying to redirect to post ID 0 following payment.</li>
					<li>Hook and function updates to support upcoming Members add-on.</li>
				</ul>
			</blockquote>
			<strong>October 7, 2014</strong>
			<blockquote>
				<ul>
					<li>Updated bank transfers feature to include ability to transfer to debit cards as well as bank accounts.</li>
					<li>Fixed a bug with checkout forms not displaying.</li>
				</ul>
			</blockquote>
			<strong>September 6, 2014</strong>
			<blockquote>
				<ul>
					<li>Bugfix: Subscriptions create Stripe customer objects correctly again.</li>
				</ul>
			</blockquote>
			<strong>August 29, 2014</strong>
			<blockquote>
				<ul>
					<li>Stripe Customer objects are now created for charges, meaning better information about customers in your Stripe dashboard</li>
					<li>Custom input has been moved from the description field to a charge metadata value</li>
					<li>Fixed Stripe link on payments history tables</li>
					<li>Stripe checkout forms now correctly save customer email</li>
					<li>Locale strings for CAD accounts have been added</li>
				</ul>
			</blockquote>
			<strong>July 23, 2014</strong>
			<blockquote>
				<ul>
					<li>Hotfix to update transfers parameter due to Stripe API update</li>
				</ul>
			</blockquote>
			<strong>July 20, 2014</strong>
			<blockquote>
				<ul>
					<li>Added option to use Stripe emails for payment receipts</li>
					<li>Fixed issue with redirect ID field on edit forms</li>
					<li>Added customer name to metatdata sent to Stripe on successful payment</li>
				</ul>
			</blockquote>
			<strong>June 23, 2014</strong>
			<blockquote>
				<ul>
					<li>New tabbed design on payment and subscription pages</li>
					<li>New sortable table for subscriber list</li>
					<li>Choose to show remember me option on checkout forms</li>
					<li>Ability to choose custom image for checkout form</li>
				</ul>
			</blockquote>
			<strong>June 21, 2014</strong>
			<blockquote>
				<ul>
					<li>You can now specify setup fees for subscriptions!</li>
				</ul>
			</blockquote>
			<strong>June 18, 2014</strong>
			<blockquote>
				<ul>
					<li>Added ability to customize subscription form button text</li>
					<li>Currency symbol now shows for plan summary price text</li>
					<li>Some typos have been fixed & other UI improvements.</li>
					<li>New About page.</li>
				</ul>
			</blockquote>
			<strong>May 5, 2014</strong>
			<blockquote>
				<ul>
					<li>New system allows selection of different form styles</li>
					<li>New 'compact' style for payment forms. More to come!</li>
					<li>Tidy up of form code to allow easier creation of new form styles.</li>
				</ul>
			</blockquote>
			<strong>Apr 20, 2014</strong>
			<blockquote>
				<ul>
					<li>Checkout form now uses currency set in the plugin options</li>
					<li>Updated currency symbols throughout admin sections</li>
					<li>Tested to work with latest release, WordPress 3.9</li>
				</ul>
			</blockquote>
			<strong>Apr 19, 2014</strong>
			<blockquote>
				<ul>
					<li>Added address line 2 and state fields to billing address portion of forms</li>
					<li>Used metadata parameter with Stripe API to better store customer email and address fields</li>
					<li>Address fields on forms now take locale into account (Zip/Postcode, State/Region etc.)</li>
					<li>Added new fields to customize email receipts</li>
				</ul>
			</blockquote>
			<strong>Apr 13, 2014</strong>
			<blockquote>
				<ul>
					<li>New form type! Stripe Checkout forms are now supported. These are pre-styled, responsive forms.</li>
					<li>You can now select to receive a copy of email receipts that are sent after successful payments.</li>
					<li>More email validation added.</li>
				</ul>
			</blockquote>
			<strong>Mar 21, 2014</strong>
			<blockquote>
				<ul>
					<li>You can now customize payment email receipts in the settings page</li>
					<li>Stage 1 of major refactor of code, making it easier & faster to provide future updates.</li>
					<li>Loads more action and filter hooks added to make plugin more extendable.</li>
				</ul>
			</blockquote>
			<strong>Feb 17, 2014</strong>
			<blockquote>
				<ul>
					<li>Subscription forms now show total price at the bottom</li>
					<li>Coupon codes can now be applied, showing total price to the customer</li>
				</ul>
			</blockquote>
			<strong>Feb 15, 2014</strong>
			<blockquote>
				<ul>
					<li>Added option to send email receipts to your customers after successful payment</li>
				</ul>
			</blockquote>
			<strong>Jan 26, 2014</strong>
			<blockquote>
				<ul>
					<li>Fixed an issue with copy/pasting Stripe API keys sometimes including extra spaces</li>
				</ul>
			</blockquote>
			<strong>Jan 15, 2014</strong>
			<blockquote>
				<ul>
					<li>You can now edit your payment and subscription forms!</li>
					<li>Improved table added for viewing payments which allows sorting by amount, date and more.</li>
					<li>General code tidy up. More coming soon.</li>
				</ul>
			</blockquote>
		</div>
	</div>
</div>
