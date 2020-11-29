Admin Pack with FlexForms Extras
================================

A PHP toolkit designed specifically for programmers to quickly create a nice-looking, custom-built, secure administrative web interface.  Choose from a MIT or LGPL license.  Proven to cut development time by at least 50% over traditional frameworks and template engines.  But it isn't a CMS nor a framework or a template engine.  Admin Pack is very different.  Give it a go to power your next PHP-based administrative backend.

Admin Pack with FlexForms Extras is the functionality of [Admin Pack](https://github.com/cubiclesoft/admin-pack) with the powerful extensions of [FlexForms Extras](https://github.com/cubiclesoft/php-flexforms-extras).

[![Donate](https://cubiclesoft.com/res/donate-shield.png)](https://cubiclesoft.com/donate/) [![Discord](https://img.shields.io/discord/777282089980526602?label=chat&logo=discord)](https://cubiclesoft.com/product-support/github/)

Features
--------

* Quick-n-dirty custom administrative interface builder.  [Live demo](https://barebonescms.com/demos/admin_pack/admin.php)
* The default templates look nice enough.  Gets the job done.
* Integrated CSRF/XSRF defenses.
* Date picker and fancy multiselect options.
* Supports custom logos/favicon.
* Reasonable size (~1MB).
* Has a liberal open source license.  MIT or LGPL, your choice.
* Designed for relatively painless integration into your project.
* Sits on GitHub for all of that pull request and issue tracker goodness to easily submit changes and ideas respectively.

The Extras
----------

* Date picker - Adds a new field 'type' of 'date' and leverages the jQuery UI datepicker.
* Accordion - Adds the jQuery UI accordion for grouping large sets of associated fields.
* Multiselect tags - Adds select2 in tag mode for selecting multiple items.
* Multiselect dropdown - Adds a jQuery UI widget for selecting multiple items.  If jQuery UI is already loaded or a smaller interface is desired, it can be better than select2.
* Multiselect flat - Adds a jQuery UI widget for selecting and reordering multiple items.
* Table cards - Adds a jQuery plugin [TableCards](https://github.com/cubiclesoft/jquery-tablecards) that uses simple templates to convert tables to mobile-friendly cards on the client.  Won't activate in layouts that don't support tables.
* Table body scrolling - Adds a jQuery plugin [TableBodyScroll](https://github.com/cubiclesoft/jquery-tablebodyscroll) that enables a table's body to be displayed on one screen vertically.  Won't activate in layouts that don't support tables.
* Table row order - Adds drag-and-drop support to an injected column for the 'table' type.  Won't activate in layouts that don't support tables.
* Table sticky headers - Adds sticky header support to the header for the 'table' type.  Won't activate in layouts that don't support tables.

Getting Started
---------------

Download or clone the latest software release.  Transfer the files to a web server.

Next, go over to the Admin Pack [Getting Started](https://github.com/cubiclesoft/admin-pack#getting-started) documentation for the rest of the quick start guide.

Under the Hood
--------------

Admin Pack with FlexForms Extras is a full integration that adds a custom jQuery UI themeroller theme plus a few custom style overrides to create a seamless experience with Admin Pack.

Admin Pack uses [FlexForms](https://github.com/cubiclesoft/php-flexforms), which makes it easy to extend Admin Pack with custom functionality (see `support/view_print_layout.php`).  For example, [FlexForms Modules](https://github.com/cubiclesoft/php-flexforms-modules) contains several official extensions (e.g. charts, HTML editor, character/word counter).
