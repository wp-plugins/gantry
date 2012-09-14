=== Gantry Template Framework ===
Contributors: gantry
Author URI: http://gantry-framework.org
Tags: gantry, framework, template, theme, widgets, flexible, extensible, configurable, 960px, grid, columns, powerful
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: 1.26

Gantry is a comprehensive set of building blocks to enable the rapid development and realization of a design into a flexible and powerful web platform

== Description ==

Gantry is a comprehensive set of building blocks to enable the rapid development and realization of a design into a flexible and powerful web platform theme.

Gantry is packed full of features to empower the development of designs into fully functional layouts with the absolute minimum of effort and fuss.

* True "Drag and Drop" page builder
* 960 Grid System
* Stunning Administrator interface
* XML driven and with overrides for unprecedented levels of customization
* Per override level control over any configuration parameter
* Preset any combination of configuration parameters, and save custom presets
* RTL language support
* Built-in CSS and JS compression and combination
* Flexible grid layout system for unparalleled control over block sizes
* Optimized codebase with speed, size, and reuse core tenets of the framework design
* 65 base widget positions
* 38 possible layout combinations for mainbody and sidebars
* Source-ordered 4 column mainbody
* Many built-in widgets and gizmos such as font-sizer, Google Analytics, to-top smooth slider, IE6 warning message, etc.
* Ability to force 'blank' widget positions for even more advanced layout customization
* Flexible parameter system with ability to set parameters via URL, Cookie, Session, Presets, etc.
* Table-less HTML overrides
* Standard typography and WordPress core element styling
* ini-based configuration storage for easy portability
* Automatic per-browser-level CSS and JS control

For video tutorials and documentation please visit official [Gantry Framework](http://gantry-framework.org/ "Gantry Framework") site.

== Installation ==

This section describes how to install the plugin and get it working.

Using WordPress plugin installer :

1. Go to the Admin Dashboard > Plugins > Add New
1. From the top list select 'Upload'
1. Point the Browse window to the plugin zip package
1. Activate the plugin in Admin Dashboard > Plugins 

Using FTP :

1. Extract the zip package with the plugin
1. Upload the plugin directory to the wp-content/plugins/
1. Activate the plugin in Admin Dashboard > Plugins

Please note that Gantry Framework plugin doesn't come up with the default theme. In order to download the Gantry Default theme please go to the [Gantry Framework](http://gantry-framework.org/ "Gantry Framework") site.

== Frequently Asked Questions ==

= What are the requirements of the plugin ? =

We try to ensure that any Gantry theme and the Gantry Plugin specifically will work with any modern and secure server environment. The recommended minimum requirements are :

* WordPress 3.0 or higher
* PHP 5.2+
* MySQL 3.23 (5+ recommended)
* Apache 1.3 (2.2+ recommended)
* mod_mysql
* mod_xml
* mod_zlib

= Where are the options of the plugin ? =

The plugin itself doesn't have any options as everything is theme powered. Gantry Framework plugin is working in the background providing the functionality to the Gantry powered themes allowing them to control everything per-theme basis . You can download a basic Gantry theme from the official [Gantry Framework](http://gantry-framework.org/ "Gantry Framework") site.

= How can I test it ? =

Once you downloaded and installed Gantry Framework plugin, please download also a default theme (that is intended to be used as a basis for building your own themes) from [Gantry Framework](http://gantry-framework.org/ "Gantry Framework") site.

== Changelog ==

= 1.26 =
* Gantry now properly loads the widget_admin.css file
* Added missing CSS and JS code for the "selectedset" field type
* Added missing charset <meta> tag in the displayHead function
* Proper fix for the Children items in the Mobile Menu
* Display Single Post Category in Breadcrumbs widget is on by default

= 1.25 =
* Fix for custom post types and loop hierarchy not properly supported
* Fix for missing $this in the gantrybodylayout.class.php
* Custom Post Type archive theme files should be now loading properly
* Fix a bug when certain options wouldn't be activated after reloading override settings
* Fix for the overflow in the admin area
* Fix for CSS overflow in the Mobile Menu
* Added WooCommerce support

= 1.24 =
* Fix for the Warning:preg_match() on the Widgets page when The Events Calendar plugin was activated (possible solution for similar issues with other plugins)
* Added some small CSS fixes for font-size of the meta elements
* Gantry iPhone Menu is now a Gantry Mobile Menu as it works both on iPhone and Android platforms
* Fix for the same domain check which could cause issues ie. with Wordpress MU Domain Mapping
* Fixed some Ajax behavior on the widget overrides page

= 1.23 =
* Fixed the z-index of WordPress screen meta tabs on the widgets page
* Added CSS code used for multi-column blog view

= 1.22 =
* Added support for the custom variations in widgets (custom CSS classes)
* Fixed the count widgets in WP 3.3 theme settings page
* Fixed the z-index value of the WP 3.3 flyout menus on admin pages with Gantry Overrides bar
* Fixed Clear Cache button and Presets switcher

= 1.21 =
* Fixed the incrementation bug in the bugfix.php script which could cause a widgets id conflict

= 1.20 =
* Added a fix to prevent frozen widgets and widgets appearing in wrong overrides
* Added a script that should automatically fix all existing frozen widgets and overrides
* Added support for WordPress Multi Site installations
* Added check for instance settings of WordPress widget classes
* The $ signs in page titles should be now displayed properly
* Added require_once parameter to the locate_type function
* Updated the SmartLoad gizmo JS file to fix XPath Ignores
* Fixed the situation when the MooTools would only get loaded when the Build Spans gizmo is enabled
* Fixed the situation when front-page.php file wouldn't get loaded from the proper location
* Modified the MU Register gizmo to add very basic width settings to the activate and signup pages

= 1.19 =
* Adjusted for WordPress plugin directory

= 1.18 =
* Fixed oddity in PHP 5.2.9 where some settings in the backend wouldn't load
* Added support for additional content type dirs
* Added ability to filter out page title in Title gizmo
* Added support for the 9 grid layout
* Ability to define author name to appear as the page title in the theme settings page

= 1.17 =
* Added widgets tooltips support
* Fixed styling for the multiple instances of inner-tabs
* Multiple instances of inner-tabs are now having default item selected
* Changed Cache classname to RokCache for better compatibility
* Upgraded mootools version
* Upgraded RokNavMenu version to fix couple issues

= 1.16 Release =
* Added WP version check for compatibility with WordPress 3.2
* Added new Google WebFonts
* Added support for All In One SEO home page title in Title gizmo

= 1.15 =
* Added support for the All In One SEO single post and single page titles
* Added check to see if there's more than 1 widget before any of the dividers in position
* Fix for bad URL generated by the getCurrentUrl

= 1.14 Release =
* Fixed support for the ordered body layouts

= 1.13 =
* Fix for fusion menu dropdowns
* JS Speed Optimization: Toggles, SelectBoxes, Gradients, ColorChooser, use a more smart way to get initialized and on load time nothing is initialized until you interact with a field (backward compatible).
* Admin Tips now allow IDs to better manage the tips XML files (backward compatible).
* Load page size saved by 35-40%
* Ability to display single post category in breadcrumbs
* Added styling for textarea admin field
* Removed Recent Comments default avatar styling in favor of rt-image class
* Fix for widget divider first with nothing above in sidebar
* Added LoadPosition gizmo which lets you to load different widget positions in your content using shortcode and positions ID ie. [loadposition id="showcase"]
* Fix for nested layout object reverts

= 1.12 Release =
* Added Categories admin field

= 1.11 =
* Change to help prevent conflict with template modifiers like wptouch

= 1.10 =
* Added input and tokens to the title gizmo
* Added accessibility css code
* Fixed bug with currentUrl not being set right
* Moved base init to template redirect
* Added user css code
* Added Category field to the Recent Posts widget

= 1.9 =
* Added better Cookie Path handling
* Fixed widget override selection bug! Yee Ha!

= 1.8 =
* Fixed Menu Items Assignments in Overrides
* Fixed Preset Saver in the backend
* Added RokStyle gizmo
* Added AutoParagraph gizmo
* Updated MooTools to 1.3

= 1.7 =
* Added RTL css file support from gantry
* Added Page Suffix gizmo
* Modified the default comment styling
* Logo points now to the Site URL not WP URL
* Breadcrumbs 'Home' button points now to the Site URL not WP URL
* Added default styling and home image for the breadcrumbs
* Moved the Home button in breadcrumbs to be widget powered
* Fixed pagination position in RTL mode
* Swapped left padding to right one for lists in RTL
* Added get_header, get_footer, and get_sidebar actions to help plugin compatibility
* Fixed Push pull for sidebars in RTL
* Added Overlay field type
* Added spans to links in certain widgets
* Added default styling for Recent Comments widget
* Breadcrumbs pathway will only appear on the single post or custom page

= 1.6 =
* Fixed swapping widget IDs in overrides

= 1.5 =
* Added check for empty widget positions in render
* Gantry Logo widget 'Per Style' setting is no longer hidden
* Changed some JS binds to follow the new ES5 specs
* Fixed Colorchooser and Gradient fields
* Added cache removal to default widget and override widget ajax actions
* Gantry Pages widget is adding extra 'active' class for current page
* Gantry Categories widget is adding extra 'active' class for current category
* Changed width and padding for the MU Register form
* Fix for cache clear issues
* Made widget instance overrides available to ajax calls

= 1.4 =
* Added displayFooter function and supporting renders for themes.
* Add mootools script in jstools gizmo
* Fix for calling wp_head on the admin side.

= 1.3 =
* Fix for layouts not reversing in RTL mode
* Fix for Duplicating and missing CSS files

= 1.2 =
* Added Minefield to the list of Browsers
* Add support for Signup page to template page overrides
* Force Widget Accessibility Mode off for Gantry Themes
* Fixed addStyle to better handle -override files and get proper css file overrides from template
* Moved cache to be WP Transients based.
* Added base level diagnostics

= 1.1 =
* Fixed support for non WP_Widget based classes.

= 1.0 =
* Changelog Creation

== Upgrade Notice ==

= 1.22 =
This release addresses some small WordPress 3.3 compatibility issues. Please remember to **ALWAYS** do a database backup of your site before updating in case if something goes wrong.

= 1.21 =
This release fixes the bugfix script which could cause the sites to break. If after updating to 1.20 your site broke - please revert your database to the backup from before 1.20. If you already recreated the widgets you can skip this step, as the newly created widgets will already have proper data.

= 1.20 =
This version includes several important fixes and it is highly recommended for everyone to update.