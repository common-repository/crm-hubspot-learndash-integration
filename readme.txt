=== CRM HubSpot LearnDash Integration ===
Contributors: qfnetwork, rahilwazir, zeeshanalam
Tags: learning, lms, learndash, hubspot, crm, deals, leads, elearning, accounts, education, learning, accounting
Requires at least: 5.0
Tested up to: 5.4
Requires PHP: 7.0
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Integrates your course enrollments with HubSpot CRM

== Description ==

When user enrolls for a course

- The user information is sent to HubSpot Contacts. If a user already exists, it reuses the contact information.
- The course information is sent to HubSpot Deals. A deal is created with assigned contact (user).

= Prerequisites: =

* [LearnDash](https://www.learndash.com/)

== Installation ==

Before installation please make sure you have latest LearnDash installed.

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

== Frequently Asked Questions ==

= Does it sync when user is enrolled or removed from course?

No.

= Can I change the fields mapping?

Not yet but it is in the roadmap.

= What type of course it will work with?

It works with all course types except with status closed.

= I have a feature request. Where can I contact you?

If you have any idea please shoot us email at [info@qfnetwork.org](mailto:info@qfnetwork.org)

= Where can I get support?

You can post your questions in the [support thread](https://wordpress.org/support/plugin/crm-salesforce-learndash-integration/). For priority support, please contact us via [https://www.qfnetwork.org](https://www.qfnetwork.org)

== Screenshots ==

1. HubSpot settings
2. User enrolls for a course
3. User as Contact entry
4. Course as Deal entry

== Changelog ==

= 1.1.0 =

* Added additional fields for mapping to the Contact and Deal
* Compatibility with LearnDash 3.x

= 1.0.2 =

* Missing vendor/ directory

= 1.0.1 =

* Typo in plugin name

= 1.0.0 =

* Initial