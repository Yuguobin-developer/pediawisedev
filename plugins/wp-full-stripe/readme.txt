=== WP Full Stripe ===
Contributors: Mammothology, freemius
Tags: wordpress payment, wordpress subscription, wordpress, plugin, stripe, payment, payments, subscription, subscribe, donation, credit card, gateway, payment gateway, ecommerce, membership
Requires at least: 4.0.0
Tested up to: 5.5.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Online payments made easy in WordPress.

== Description ==

Full Stripe is a WordPress plugin designed to make it easy for you to accept payments and create subscriptions from your WordPress site. Powered by Stripe, you can embed payment forms into any post or page and take payments directly from your website without making your customers leave for a 3rd party website.

###WP Full Stripe Features:###

* NEW! Donation forms with custom amount recurring donations are here!
* NEW! Form currency format is now configurable.
* Securely take payments from any page or post on your Wordpress website in over 100 currencies
* Allow your users to subscribe for recurring payments (ever-running, or payment in installments -type) with setup fees and non-standard intervals
* Collect one-time payments for set amount, custom amount, or amount selectable from list
* Customize the forms: add custom fields, style the forms with custom CSS
* Send custom payment emails
* Inline and Stripe Checkout-style payment forms
* Easily view, and manage your received payments, subscribers, plans and more
* Form shortcode generator added for embedding forms easily into pages and posts (simple copy'n'paste)!
* The plugin can auto-update to the latest version with the click of a button!
* Fully supported, professionally written and regularly updated software

== Installation ==

1. Uninstall any previous version of the plugin (No data will be lost)
1. Download this plugin.
1. Login to your WordPress admin.
1. Click on the plugins tab.
1. Click the Add New button.
1. Click the Upload button.
1. Click "Install Now", then Activate, then head to the new menu item on the left labeled "Full Stripe".

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==
= v5.5.2 (Nov 25, 2020) =
* Fixed: Save card forms displayed an error when either email receipts or thank you pages were turned on.
* Fixed: The subscription billing anchor day was sometimes off by a day due to timezone calculation issues.
* Fixed: Capturing, refunding, and voiding one-time payments didn't work in WP admin.
* Fixed: The create/edit one-time payment form page broke when the amount list was empty.
* Fixed: The "One-time payment forms" page displayed an error after upgrading from WP Full Stripe Free.
* Fixed: The "One-time payments" page displayed an error after upgrading from WP Full Stripe Free.

= v5.5.1 (Oct 26, 2020) =
* Donation forms with custom amount recurring donations are here!
* Form currency format is now configurable (decimal separator character, currency symbol, currency symbol position).
* Form UI is available in 6 new languages (Danish, French, German, Italian, Japanese, Spanish)
* The Manage Subscription page and payment forms can be used on the same page.
* The Subscription Receipt email template supports the %INVOICE_URL% placeholder.
* New display languages are available for checkout forms.
* The display language of the Card Info field on inline forms is now configurable.
* A description (decorated with placeholder tokens) can be added to saved cards.
* The Card Info field is now full width.
* The Cardholder's Name field is now a required field.
* The subscription plan selector displays the plan recurring fee and interval as well.
* No success message is displayed before redirecting to Thank you pages.
* The Cardholder's Name and Email address fields are moved towards the bottom of the form, just north of the Card Info field.
* Fixed: Some placeholders of the 'Subscription ended' email template didn't work properly.
* Fixed: Logging in to the Manage Subscriptions page didn't work on some Multisite installations.
* Fixed: REST API endpoint registrations complained about missing parameters (introduced in Wordpress v5.5.0).
* Fixed: Reworked some calls to functions deprecated in PHP v7.4 .

= v5.5.0-beta (Oct 10, 2020) =
* Donation forms with custom amount recurring donations are here!
* Form currency format is now configurable (decimal separator character, currency symbol, currency symbol position).
* Form UI is available in 6 new languages (Danish, French, German, Italian, Japanese, Spanish)
* The Manage Subscription page and payment forms can be used on the same page.
* The Subscription Receipt email template supports the %INVOICE_URL% placeholder.
* New display languages are available for checkout forms.
* The display language of the Card Info field on inline forms is now configurable.
* A description (decorated with placeholder tokens) can be added to saved cards.
* The Card Info field is now full width.
* The Cardholder's Name field is now a required field.
* The subscription plan selector displays the plan recurring fee and interval as well.
* No success message is displayed before redirecting to Thank you pages.
* The Cardholder's Name and Email address fields are moved towards the bottom of the form, just north of the Card Info field.
* Fixed: Some placeholders of the 'Subscription ended' email template didn't work properly.
* Fixed: Logging in to the Manage Subscriptions page didn't work on some Multisite installations.
* Fixed: REST API endpoint registrations complained about missing parameters (introduced in Wordpress v5.5.0).
* Fixed: Reworked some calls to functions deprecated in PHP v7.4 .

= v5.4.1 (Jul 27, 2020) =
Added:
* The billing anchor day feature now supports subscription plans with trial.
Changed:
* Fixed: Submitting subscription forms with a billing anchor day later than the current day of the month threw an error.
* Fixed: Custom VAT calculation didn't work when there was only one plan on the form.
* Fixed: When saving cards, metadata for existing customers weren't updated.
* Fixed: Payment in installments -type subscriptions weren't cancelled automatically in some cases.

= v5.4.0 (Apr 23, 2020) =
Added:
* Billing cycle anchor day can be specified for monthly subscriptions.
* Subscribers can download invoices from the "Manage subscriptions" page.
* Administrators can turn off downloading invoices by subscribers.
* Administrators can turn off cancelling subscriptions by subscribers.
* Subscription plans now support day intervals.
* Full Stripe app icon displayed on the WP admin dashboard.
Changed:
* Renamed the "Settings / Users" page to "Settings / Security"
* The "Payment details" popover doesn't display decimal places for amounts in zero-decimal currencies (like JPY).
* The "Payment details" popover doesn't display a quantity label when purchased quantity is one.
* Improved the scheduler that stops subscriptions with cancellation counts (payment in installment plans).

= v5.3.0 (Mar 17, 2020) =
Changed:
* WP Full Stripe can run concurrently with other Stripe plugins.
(Moved the Stripe PHP client library to a custom namespace)
* Fixed conflict of the "Payment details" popover with some Wordpress themes.

= v5.2.0 (Feb 26, 2020) =
Added:
* Customers can purchase subscriptions in bulk.
* Subscribers can change subscription quantity on the "Manage subscriptions" page.
* Subscription plans can be selected by passing an URL parameter to subscription pages.
* Custom amount can be set on one-time payment forms by passing an URL parameter to payment pages.
* Custom placeholder tokens can be added programmatically to email templates.
* Metadata can be added programmatically to transactions (think affiliate id).

Changed:
* Updated Stripe PHP client to v7.24.0 .
* Updated Freemius SDK to v2.3.2 .
* Fixed webhook event processing on checkout forms.

= v5.1.0 (Dec 9, 2019) =
Added:
* Inline forms can collect shipping address.
* Checkout forms can be protected by Google reCaptcha.
* The plan selector is hidden automatically when only one plan is added to the form.
* The "Update card" function of "Manage subscriptions" has become SCA-compliant.

Changed:
* Fixed the auto-update feature because it didn't work for some Envato customers.
* Fixed the "Card update" function on the "Manage subscriptions" page, it didn't update the payment method for some subscribers.
* Fixed a checkout form related periodic task which caused a lot of API errors.

= v5.0.3 (Oct 28, 2019) =
* Bugfix: Checkout subscription forms fail when simple button layout is active.
* Bugfix: Checkout subscription forms crash when simple button layout is active and there is no valid plan selected.
* Bugfix: One-time payments fail when the form has an empty description.
* Bugfix: Leaves subscription status as 'Incomplete' when subscribing to a plan with a trial period.
* Bugfix: The plugin wouldn't let customers pay on inline forms with reCaptcha turned on and 3DS cards.
* Bugfix: Subscription forms with no valid plan don't return a user friendly error message.
* Bugfix: Subscription forms don't validate the email address in certain cases.
* Bugfix: The "Manage subscriptions" page doesn't display the subscriber's active subscriptions.
* Bugfix: The plugin logs errors into the web server log about checkout sessions it cannot verify.

= v5.0.2 (Sep 19, 2019) =
* Fixed issue related to billing address handling on checkout subscription forms.
* Fixed issue of subscription plans with trial period not working on inline subscription forms.
* Fixed issue of database patches not applied correctly on MySql 5.5 database servers.
* Fixed issue of delete local action not working properly for saved cards.

= v5.0.1 (Sep 13, 2019) =
* Fixed issue of checkout forms not redirecting back to the starting page after payment.
* Fixed issue of checkout one-time payment forms displaying an error message when submitting custom donation amounts.
* Now the plugin sets the billing address of customers on checkout subscription forms (because Stripe doesn't).

= v5.0.0 (Sep 12, 2019) =
* The plugin is SCA-compliant, please read our blog post: https://paymentsplugin.com/blog/wp-full-stripe-sca-ready .
* The "Manage subscriptions" feature now works without subscription data in the Wordpress database (no need to import subscriptions).

= v4.2.0 (Aug 12, 2019) =
* Added %TRANSACTION_ID% placeholder to emails and thank you pages of all form types.
* Implemented Wordpress authentication option for the "Manage subscriptions" feature.
* Now firing Wordpress actions before and after cancelling subscriptions.
* Now showing the CSS selector on the "Appearance" tab of all form types for easier CSS customizations.
* Made the plugin demo mode more strict.

= v4.1.2 (Jun 29, 2019) =
* Bugfix: Updated the Freemius SDK to v2.3.0 to avoid a fatal error when installing the plugin on Wordpress v5.2.x.

= v4.1.1 (Jun 25, 2019) =
* Refined form CSS based on feedback from our customers.

= v4.1.0 (Jun 13, 2019) =
* Modified plugin to be compatible with several licensing engines (EDD, Freemius).

= v4.0.3 (Jun 20, 2019) =
* Refined form CSS based on feedback from our customers.

= v4.0.2 (May 28, 2019) =
* IMPORTANT: WP Full Stripe v4.0.2 requires PHP 5.5 or greater
* IMPORTANT: WP Full Stripe v4.0.2 has new a form design, it's not compatible with the old design. Your custom CSS rules won't work.
* UPDATE ONLY IF you have tested v4.0.2 in your test/staging environment.
* Bugfix: Javascript and CSS files of the plugin weren't loaded on some websites
* Bugfix: The "Payment details" popover displayed "Null" when only one plan was added to a subscription form

= v4.0.1 (May 24, 2019) =
* IMPORTANT: WP Full Stripe v4.0.1 requires PHP 5.5 or greater
* IMPORTANT: WP Full Stripe v4.0.1 has new a form design, it's not compatible with the old design. Your custom CSS rules won't work.
* UPDATE ONLY IF you have tested v4.0.1 in your test/staging environment.
* Bugfix: Payment currency was always displayed as USD in popup one-time payment forms of the "Select amount from list" payment type
* Bugfix: Billing and shipping address was not set properly in some cases on popup forms

= v4.0.0 (May 23, 2019) =
* IMPORTANT: WP Full Stripe v4.0.0 requires PHP 5.5 or greater
* IMPORTANT: WP Full Stripe v4.0.0 has new a form design, it's not compatible with the old design. Your custom CSS rules won't work.
* UPDATE ONLY IF you have tested v4.0.0 in your test/staging environment.
* Professional, new look for all form types
* Professional, new look for the "Manage subscriptions" page
* Standard and compact inline one-time payment forms are merged and live on as inline forms with new, unified look
* Inline forms now use Stripe Elements for collecting card details
* Subscription forms can display plans as radio button list or dropdown
* Subscription forms have a new "Payment details" popover for subscription overview (setup fee, plan, VAT, total)
* One-time payment forms can display amounts as radio button list or dropdown
* Added donation look for one-time payments
* Vastly improved error feedback on all forms
* Stripe PHP client upgraded to v6.27.0

= v3.16.3 (Apr 30, 2019) =
* Fixed an issue of not displaying feedback after payment with certain billing countries
* Fixed an issue of not working when other plugins use Google reCaptcha

= v3.16.2 (Feb 14, 2019) =
* Added Google reCaptcha support (v2) for inline forms
* Added support for custom field placeholders in one-time payment form descriptions
* Fixed issue with the %PRODUCT_NAME% placeholder on one-time popup forms with the "Select amount from list" payment type
* Fixed HTML escaping issue for placeholders with single and double quotes in email notifications and on Thank you pages

= v3.16.1 (Oct 8, 2018) =
* Added verifications to make the CVC/CVV code required (even when Stripe doesn't require it)
* You can select more plans on a form due to increased database column size (from 255 characters to 2048 characters)
* Fixed an issue of cancelling subscriptions not working on the "Manage subscriptions" page when being logged in to WordPress
* Fixed an issue of subscription trials not working
* Fixed an issue of website assets not loading when Wordpress is hosted on Windows webservers (css, js, and image files)

= v3.16.0 (Sep 3, 2018) =
* Added "Authorize & capture" support for one-time payments
* Reworked how the setup fee works so it's a proper invoice item now, and tax can be added on top of it
* Fixed an issue of customer name and billing address not set properly on Stripe invoices
* Renamed and moved the "Card captures" menu to the "Saved cards" menu
* Added webhook event handlers for one-time payment state changes (refunded, pending, expired, failed, captured)

= v3.15.1 (Jul 11, 2018) =
* IMPORTANT: Update to this version if you are using webhooks and subscriptions that end after certain number of charges
* Fixed an issue related to subscriptions not ending automatically
* Changed the way javascript files are loaded so the plugin is compatible with more themes

= v3.15.0 (May 25, 2018) =
* GDPR-compatible forms with an option to add a "Terms of use" checkbox
* Self-service area for subscribers to update credit card data, and cancel subscriptions (with Google reCaptcha protection)
* Configurable payment description for all one-time payment forms
* Changed the order of the Stripe API keys (publishable, secret) on the "Settings" page
* Made changes to be compatible with the WP Mandrill plugin

= v3.14.0 (Apr 20, 2018) =
* Card capture forms for customers to submit credit card data (so you can charge them later).
* Error message displayed upon form submit when secret and publishable API keys are entered in the wrong order.
* Updated the Stripe PHP client to v6.4.1
* Made subscription management compatible with v6.0 Stripe API (products as parents of subscription plans).
* Removed Alipay support temporarily.
* Removed Bitcoin support.
* "Lock email address field for logged in users" feature turned into "Fill in email address for logged in users".
* Fixed an issue related to not displaying error message when an empty form was submitted.
* Fixed an issue related to not saving the default billing country on subscription forms.
* Fixed an issue related to not displaying properly popup button labels containing single quote characters.
* Fixed the bug that the "Could not find payment information" error message was not localizable.

= v3.13.1 (Feb 12, 2018) =
* Fixed a subscription plan creation/editing/listing issue caused by a new Stripe API version.
* Fixed an issue related to optional custom fields.

= v3.13.0 (Jan 8, 2018) =
* Added shipping address support to popup forms (both one-time and subscription).
* Increased the maximum number of custom fields per form to 10 (used to be 5).
* The form name is displayed for each payment in WP admin.
* The form name is added as metadata to one-time payments and subscriptions.
* Performed small tweaks to make inline and popup form layouts alike (aligned labels to left, removed fieldset element).
* Removed security verification that caused nonce errors on cached websites.
* Condensed the billing address and the shipping address to one metadata each.
* For one-time payments, all metadata is added to the Stripe charge object (the Stripe customer object is left intact).
* For subscriptions, all metadata is added to the Stripe subscription object (the Stripe customer object is left intact).

= v3.12.1 (Nov 21, 2017) =
* Fixed the product description not being displayed on popup subscription forms.

= v3.12.0 (Nov 13, 2017) =
* Added tax (VAT) support to subscription forms.
* Added option for simple popup subscription button (no plan selector, no plan info label, no custom fields, no coupon field).
* Added option for selecting the display language of popup one-time payment forms, and popup subscription forms.

= v3.11.1 (Sep 5, 2017) =
* Fixed a billing address validation issue on inline one-time forms.

= v3.11.0 (Aug 22, 2017) =
* IMPORTANT! This release contains critical security fixes and critical bugfixes. Please update your Full Stripe installation as soon as possible!!!
* Added support for "custom amount" and "select amount from list" payment types to popup one-time payment forms (Stripe checkout forms).
* Updated the Stripe PHP client to the latest version (v5.1.1)

= v3.10.0 (Aug 18, 2017) =
* Added popup (Stripe checkout) support to subscription forms.
* Added custom field support to all form types (one-time and subscription, inline and popup).
* Fixed issues with zero-decimal currencies (like the Japanese Yen).
* Fixed WP admin URLs linking to Stripe charges and Stripe subscriptions.
* Fixed the value of the %AMOUNT% placeholder on subscription forms where both setup fee and plan fee have to be charged.
* Fixed an issue of not being able to select certain payment confirmation ("Thank you") pages for redirects

= v3.9.1 (April 19, 2017) =
* Fixed a bug on the edit page of popup forms in WP admin

= v3.9.0 (April 18, 2017) =
* Payment currency can be set per form.
* Payment currency can be set per subscription plan.
* Setup fee can be set per subscription plan.
* Split all form editor pages into tabs in order to make room for new features.
* Added the %DATE% placeholder token for email notifications.

= v3.8.2 (March 1, 2017) =
* Fixed a bug related to customizable "Thank you" (payment confirmation) pages

= v3.8.1 (February 26, 2017) =
* Fixed a bug related to PHP 5.3.x compatibility.

= v3.8.0 (February 24, 2017) =
* All forms are now responsive and mobile friendly.
* "Thank you" pages after payment are customizable with placeholder tokens.
* Payment types "Select amount from list" and "Custom amount" can be combined on one-time payment (and donation) forms.
* New option added to make custom fields mandatory.
* Minimum plugin requirements are verified at activation time.
* Added collision prevention code to handle those cases when other plugins load jQuery in a non-standard way.
* Fixed a bug with the %PRODUCT_NAME% placeholder when the payment type is "Select Amount from List".
* Fixed a bug with form names containing only digits.
* Fixed a bug with error messages when invalid card expiry date is provided.

= v3.7.5 (February 15, 2017) =
* Fixed an issue with payment descriptions containing commas when the payment type is "Select Amount from List".

= v3.7.4 (January 23, 2017) =
* Increased amount length from 6 digits to 8.
* The Stripe PHP client has been upgraded to v4.4.0 .
* Fixed a bug that caused the product description not properly being mapped to the %PRODUCT_NAME% placeholder on Stripe checkout forms.

= v3.7.3 (December 2, 2016) =
* The Stripe PHP client has been upgraded to v4.2.0 .

= v3.7.2 (November 24, 2016) =
* Fixed a bug related to missing button icons in WP Admin.
* Fixed a bug that prevented the plugin from being activated (class name collision with other plugins).
* Plan label handling modified to work with themes that remove empty &lt;p&gt;tags.

= v3.7.1 (November 16, 2016) =
* Error pane handling modified to work with themes that remove empty <p> tags.
* Fixed a bug that would prevent the plugin from displaying more than 100 subscription plans.
* Removed placeholders for the card and name fields on subscription forms

= v3.7.0 (November 2, 2016) =
* Any number of forms can be embedded into a page or post!
* The plugin can auto-update to the latest version with the click of a button!
* Form shortcode generator added for embedding forms easily into pages and posts (simple copy'n'paste)!
* AliPay support added for one-time payments on Stripe checkout-style payment forms.
* Subscriptions can now be deleted on the "Subscribers" page.
* Country dropdown has been added to the billing address on all form types.
* The "Action" column has been redesigned on all admin pages (iconified buttons).
* The "Payments" page has a new layout, it is more structured and more spacious.
* The "Payments" page has got a search box. Find payments based on customer's name and email address, Stripe customer id, Stripe charge id, or mode (live/test).
* The "Settings" page can now be extended by add-ons.
* "Newsfeed" tab has been added to the "About" page.
* Fixed an issue related to being unable to save subscription forms with selected subscription plan names containing spaces.
* The "Transfers" feature has been removed due to incompatibility with the latest Stripe API (will be reintroduced later).
* The Stripe client and API used by the plugin has been upgraded to v3.21.0 in order to be compatible with TLS 1.2.

= v3.6.0 (June 3, 2016) =
* Support for subscriptions that terminate after certain number of charges!
* Subscriptions can be cancelled from the “Subscribers” page.
* The “Subscribers” page has a new layout, it is more structured and more spacious.
* The “Subscribers” page has a search box. Find subscriptions based on subscribers’ name and email address, Stripe customer id, Stripe subscription id, or mode (live/test).
* The “Settings / E-mail receipts” page has a new layout for managing e-mail notifications (new email types coming soon).
* Now you can translate form titles and custom field labels to other languages as well.
* Stripe webhook support added for advanced features in the coming releases.
* Fixed an issue related to the value of the PLAN_AMOUNT token when a coupon is applied to the subscription.
* Fixed an issue related to plan ids, now they can contain comma characters.
* Improved error handling and error messages for internal errors.

= v3.5.1 (March 15, 2016) =
* Added PRODUCT_NAME token to email receipts (used when payment type is “Select Amount from List”)
* Added extra error handling for failed cards (declined, expired, invalid CVC).
* Fixed issue with long plan lists on subscription forms.

= v3.5.0 (February 21, 2016) =
* Added Bitcoin support for checkout forms!
* The e-mail field can be locked and filled in automatically for logged in users.
* Success messages and error messages are scrolled into view automatically.
* The spinning wheel has been moved next to the payment button on all form types.
* The lists on the "Payments" and "Subscribers" pages now are descending and ordered by date by default.
* Fixed an issue withX payment forms on Wordpress 4.4.x: the submitted forms never returned.

= v3.4.0 (December 6, 2016) =
* New payment type introduced on payment forms: the customer can select the payment amount from a list.
* The “Settings” page is now easier to use, it has been divided into three tabs: Stripe, Appearance, and Email receipts.
* The e-mail receipt sender address is now configurable.
* All payment forms (payment, checkout, subscription) add the same metadata fields to the Stripe “Payment” and “Customer” objects.
* CSS style improvements to assure compatibility with the KOMetrics plugin.

= v3.3.0 (October 30, 2016) =
* The plugin is translation-ready! You can translate it to your language without touching the plugin code. (Public labels only)
* Usability improvements made to the currency selector on the “Settings” page.
* Improved error handling on all form types (payment, checkout, and subscription).
* Version number of the plugin is displayed on the “About” and “Help” pages in WP Admin.
* Confirmation dialog has been added to delete operations where it was missing.
* Fixed an issue on subscription forms with the progress indicator spinning endlessly, never returning.
* Fixed an issue on checkout forms with the CUSTOMERNAME token not resolved properly in email receipts.

= v3.2.0 (August 22, 2016) =
* Subscription plans on subscription forms can be reordered by using drag and drop!
* Subscription plans can be modified or deleted directly from WP Full Stripe.
* Page or post redirects can be selected using an autocomplete, no time wasted with figuring out post ids.
* Arbitrary URLs can be used as redirect URLs.
* Placeholder tokens for custom fields are available in email receipts.

= v3.1.1 (July 18, 2016) =
* Fixed a bug with Stripe receipt emails on subscription forms.

= v3.1.0 (June 25, 2016) =
* Now you can use plugin email receipts for all form types (payment, checkout, and subscription) !!
* New email receipt tokens: customer email, subscription plan name, subscription plan amount, subscription setup fee.
* Separate email template and subject fields for payment forms and subscription forms.
* Support for all countries supported by Stripe (20 countries currently).
* Support for all currencies supported by Stripe (138 currencies in total, number varies by country).

= December 30, 2014 =
* You can now use multiple checkout buttons on the same page!
* Checkout button styling can now be disabled (useful for theme conflicts).
* Some minor changes added for future extensions.

= December 5, 2014 =
* Removing form input placeholders as they conflict with some themes.
* SSN is no longer a required field for transfer forms.
* Support for KO Metrics added.
* Bugfix: settings upgrade properly when installing a new version of the plugin.

= November 4, 2014 =
* You can now add up to 5 custom input fields to payment & subscription forms!
* Subscribers and payment records can now be deleted locally (they remain in your Stripe dashboard).
* Lots of UI/UX improvements including appropriate table styling and useful redirects.
* Added livemode status to subscribers.
* Cardholder name correctly added to payment details.
