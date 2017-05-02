Admin Pack with FlexForms Extras
================================

A PHP toolkit designed specifically for programmers to quickly create a nice-looking, custom-built, secure administrative web interface.  Choose from a MIT or LGPL license.  Proven to cut development time by at least 50% over traditional frameworks and template engines.  But it isn't a CMS nor a framework or a template engine.  Admin Pack is very different.  Give it a go to power your next PHP-based administrative backend.

Admin Pack with FlexForms Extras is the functionality of Admin Pack with the powerful extensions of [FlexForms Extras](https://github.com/cubiclesoft/php-flexforms-extras).

Features
--------

* Quick-n-dirty custom administrative interface builder.
* The default templates look nice enough.  Gets the job done.
* Integrated CSRF/XSRF defenses.
* Date picker and fancy multiselect options.
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
* Table row order - Adds drag-and-drop support to an injected column for the 'table' type.  Won't activate in layouts that don't support tables.
* Table sticky headers - Adds sticky header support to the header for the 'table' type.  Won't activate in layouts that don't support tables.

Under the Hood
--------------

Admin Pack uses [FlexForms](https://github.com/cubiclesoft/php-flexforms), which makes it easy to extend Admin Pack with custom functionality (see `support/view_print_layout.php`).  For example, [FlexForms Modules](https://github.com/cubiclesoft/php-flexform-modules) contains several official extensions (e.g. charts, HTML editor, character/word counter).

More Information
----------------

Documentation, demos, examples, and official downloads of this project sit on the Barebones CMS website (Admin Pack does not depend on Barebones CMS):

http://barebonescms.com/documentation/admin_pack/
