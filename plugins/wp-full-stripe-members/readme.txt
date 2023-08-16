=== WP Full Stripe Members ===
Contributors: Mammothology, freemius
Tags: login, member, members, membership, profile, profiles, protected, register, registration, s2Member, stripe, subscription, user, userpro, users
Requires at least: 4.0.0
Tested up to: 5.4.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Make money from your WordPress website by creating subscriber only content.

== Description ==

A fully featured membership plugin add-on for WP Full Stripe that allows you to create protected content that only subscribed members are allowed to view.
Multiple membership tiers are available so you can offer different levels of membership, each one tied to a different subscription plan. Members have their own 'My Account' page that allows them to update credit card details, change their membership plan and cancel their account.
Be in total control of your protected content by simply selecting the option to make it subscriber only from the edit post page. WP Full Stripe Members seamlessly integrates with WP Full Stripe and will automatically create member accounts for customers who subscribe to membership plans that you define.

###WP Full Stripe Members features:###

* IMPORTANT: Use this version with WP Full Stripe v5.4.0 or greater
* Create member only content available exclusively to paying customers.
* Seamlessly integrates with WP Full Stripe
* Manage member roles and subscription plans from the dashboard
* Multiple tiers of membership levels and option to allow member upgrades
* Members ‘My Account’ page shortcode
* Send custom registration emails emails
* Import subscribers from Stripe with a few clicks
* Fully supported, professionally written and regularly updated software

== Installation ==

1. Uninstall any previous version of the plugin (No data will be lost)
1. Download this plugin.
1. Login to your WordPress admin.
1. Click on the plugins tab.
1. Click the Add New button.
1. Click the Upload button.
1. Click "Install Now", then Activate, then head to the new submenu item on the left labeled "Full Stripe -> Members".

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==
= July 27, 2020 (v1.6.4) =
* Fixed: Plan amounts weren't displayed correctly for the Japanese Yen and some other zero-decimal currencies.
* Fixed: Updating the default card of the customer didn't take effect in some cases.
* Fixed: Some public messages weren't translatable

= May 7, 2020 (v1.6.3) =
* Fixed an issue that cancelling a membership took effect immediately, not at the end of the billing period.
* Fixed an error that copy of the member registration email was not sent to the site administrator.

= April 23, 2020 (v1.6.2) =
* Fixed issues related to using the plugin on Wordpress Multisite.
* Fixed an error Freemius customers experienced when trying to upgrade from v1.6.1 to v1.6.2.
* Upgrading / downgrading the membership level is now properly reflected in WP Full Stripe (requires WP Full Stripe v5.4.0+).
* Changed the auto-update URLs as the auto-updater has been moved to a new domain.

= March 18, 2020 (v1.6.1) =
* IMPORTANT: Upgrade only if you'd like to use this plugin with WP Full Stripe v5.3.0 or greater.
* IMPORTANT: This version is NOT compatible with WP Full Stripe versions earlier than v5.3.0.
* Fixed a compatibility issue with WP Full Stripe v5.3.0 .
* Upgraded Freemius SDK to v2.3.2 .

= October 18, 2019 (v1.6.0) =
* Refactored plugin directory structure.
* Improved Envato and Freemius license management.
* Fixed links pointing to the "Support" page of the website.

= October 4, 2019 (v1.5.3) =
* IMPORTANT: Upgrade only if you'd like to use this plugin with WP Full Stripe v5.0.0 or greater.
* IMPORTANT: This version is NOT compatible with WP Full Stripe versions earlier than v5.0.0.
* Fixed a compatibility issue with WP Full Stripe v5.x .

= August 30, 2019 (v1.5.2) =
* Fixed an issue of members not being created when using popup forms.

= August 5, 2019 (v1.5.1) =
* Upgraded Freemius SDK to v2.3.0

= September 3, 2018 (v1.5.0) =
* IMPORTANT: This version is NOT compatible with WP Full Stripe versions earlier than v3.16.0.
* IMPORTANT: Upgrade to this release if you'd like to use WP Full Stripe v3.16.0 or greater.
* Changed the plugin so that it doesn't remove Wordpress user roles when deactivated (important for manual upgrades).
* Removed the "Create members in test mode?" and "Use default password for new members?" options from the settings.

= November 29, 2017 (v1.4.3) =
* Fixed a sender address of user registration emails.

= November 27, 2017 (v1.4.2) =
* Fixed a bug related to new members not being listed on the Members page.

= November 21, 2017 (v1.4.1) =
* IMPORTANT: This version is NOT compatible with WP Full Stripe versions earlier than v3.12.0.
* IMPORTANT: Upgrade to this release if you'd like to use WP Full Stripe v3.12.0 or greater.

= August 18, 2017 (v1.4.0) =
* IMPORTANT: This release is compatible only with WP Full Stripe v3.10.0 or greater.
* The user registration email can use all the placeholder tokens of WP Full Stripe.
* The Members page has a new layout for easier filtering and discovery.
* Members can be deleted on the "Members" page.

= November 18, 2016 (v1.3.0) =
* Compatibility with WP Full Stripe v3.7.0 or greater.
* The user registration email template is now configurable on the "Full Stripe / Settings / Email Notifications" page in WP admin.
* The Members plugin settings are now on the "Full Stripe / Settings" page in WP admin.
* The "My Account" page now works with any currency, not just with USD.
* The plugin doesn't create session for public pages anymore - public pages work properly with Varnish caching.
* Fixed a bug related to logging in with a member with corrupt data, login no longer crashes.

= May 13, 2016 (v1.2.0) =
* Added the CVC code field to the card update pane of the "My Account" page.
* Now you can translate the plugin to other languages!
* Added plugin version number to the "Help" page.

= August 27, 2015 (v1.1.1) =
* Made the Members plugin compatible with WP Full Stripe v3.2.0 .

= July 23, 2015 (v1.1.0) =
* Now you can import subscribers from Stripe with just a few clicks!

= October 14, 2014 (v1.0) =
* Initial release.
