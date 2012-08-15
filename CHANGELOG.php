<?php
/**
 * Gantry For Wordpress
 * 
 * @version   1.25 August 15, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
die();
?>

1. Copyright and disclaimer
----------------


2. Changelog
------------
This is a non-exhaustive changelog for Gantry, inclusive of any alpha, beta, release candidate and final versions.

Legend:

* -> Security Fix
# -> Bug Fix
+ -> Addition
^ -> Change
- -> Removed
! -> Note

------- 1.25 Release [] ------
# Fix for custom post types and loop hierarchy not properly supported
# Fix for missing $this in the gantrybodylayout.class.php
# Custom Post Type archive theme files should be now loading properly
# Fix a bug when certain options wouldn't be activated after reloading override settings
# Fix for the overflow in the admin area
# Fix for CSS overflow in the Mobile Menu
+ Added WooCommerce support

------- 1.24 Release [] ------
# Fix for the Warning:preg_match() on the Widgets page when The Events Calendar plugin was activated (possible solution for similar issues with other plugins)
+ Added some small CSS fixes for font-size of the meta elements
^ Gantry iPhone Menu is now a Gantry Mobile Menu as it works both on iPhone and Android platforms
# Fix for the same domain check which could cause issues ie. with Wordpress MU Domain Mapping
# Fixed some Ajax behavior on the widget overrides page

------- 1.23 Release [] ------
# Fixed the z-index of WordPress screen meta tabs on the widgets page
+ Added CSS code used for multi-column blog view

------- 1.22 Release [] ------
+ Added support for the custom variations in widgets (custom CSS classes)
# Fixed the count widgets in WP 3.3 theme settings page
# Fixed the z-index value of the WP 3.3 flyout menus on admin pages with Gantry Overrides bar
# Fixed Clear Cache button and Presets switcher

------- 1.21 Release [] ------
# Fixed the incrementation bug in the bugfix.php script which could cause a widgets id conflict

------- 1.20 Release [] ------
# Added a fix to prevent frozen widgets and widgets appearing in wrong overrides
+ Added a script that should automatically fix all existing frozen widgets and overrides
+ Added support for WordPress Multi Site installations
# Added check for instance settings of WordPress widget classes
# The $ signs in page titles should be now displayed properly
^ Added require_once parameter to the locate_type function
# Updated the SmartLoad gizmo JS file to fix XPath Ignores
# Fixed the situation when the MooTools would only get loaded when the Build Spans gizmo is enabled
# Fixed the situation when front-page.php file wouldn't get loaded from the proper location
^ Modified the MU Register gizmo to add very basic width settings to the activate and signup pages

------- 1.19 Release [] ------
! Adjusted for WordPress plugin directory

------- 1.18 Release [] ------
# Fixed oddity in PHP 5.2.9 where some settings in the backend wouldn't load
+ Added support for additional content type dirs
^ Added ability to filter out page title in Title gizmo
+ Added support for the 9 grid layout
+ Ability to define author name to appear as the page title in the theme settings page


------- 1.17 Release [] ------
+ Added widgets tooltips support
# Fixed styling for the multiple instances of inner-tabs
# Multiple instances of inner-tabs are now having default item selected
^ Changed Cache classname to RokCache for better compatibility
^ Upgraded mootools version
^ Upgraded RokNavMenu version to fix couple issues

------- 1.16 Release [] ------
^ Added WP version check for compatibility with WordPress 3.2
+ Added new Google WebFonts
+ Added support for All In One SEO home page title in Title gizmo

------- 1.15 Release [] ------
+ Added support for the All In One SEO single post and single page titles
+ Added check to see if there's more than 1 widget before any of the dividers in position
# Fix for bad URL generated by the getCurrentUrl

------- 1.14 Release [] ------
# Fixed support for the ordered body layouts

------- 1.13 Release [] ------
# Fix for fusion menu dropdowns
^ JS Speed Optimization: Toggles, SelectBoxes, Gradients, ColorChooser, use a more smart way to get initialized and on load time nothing is initialized until you interact with a field (backward compatible).
^ Admin Tips now allow IDs to better manage the tips XML files (backward compatible).
^ Load page size saved by 35-40%
+ Ability to display single post category in breadcrumbs
+ Added styling for textarea admin field
^ Removed Recent Comments default avatar styling in favor of rt-image class
# Fix for widget divider first with nothing above in sidebar
+ Added LoadPosition gizmo which lets you to load different widget positions in your content using shortcode and positions ID ie. [loadposition id="showcase"]
# Fix for nested layout object reverts

------- 1.12 Release [] ------
+ Added Categories admin field

------- 1.11 Release [] ------
^ Change to help prevent conflict with template modifiers like wptouch

------- 1.10 Release [] -------
^ Added input and tokens to the title gizmo
+ Added accessibility css code
# Fixed bug with currentUrl not being set right
^ Moved base init to template redirect
^ Added user css code
^ Added Category field to the Recent Posts widget

------- 1.9 Release [] -------
# Added better Cookie Path handling
# Fixed widget override selection bug! Yee Ha!

------- 1.8 Release [] -------
# Fixed Menu Items Assignements in Overrides
# Fixed Preset Saver in the backend
+ Added RokStyle gizmo
+ Added AutoParagraph gizmo
^ Updated MooTools to 1.3

------- 1.7 Release [] -------
# Added RTL css file support from gantry
+ Added Page Suffix gizmo
^ Modified the default comment styling
^ Logo points now to the Site URL not WP URL
^ Breadcrumbs 'Home' button points now to the Site URL not WP URL
+ Added default styling and home image for the breadcrumbs
^ Moved the Home button in breadcrumbs to be widget powered
^ Fixed pagination position in RTL mode
^ Swapped left padding to right one for lists in RTL
+ Added get_header, get_footer, and get_sidebar actions to help plugin compatibility
# Fixed Push pull for sidebars in RTL
+ Added Overlay field type
^ Added spans to links in certain widgets
^ Added default styling for Recent Comments widget
^ Breadcrumbs pathway will only appear on the single post or custom page

------- 1.6 Release [] -------
# Fixed swapping widget IDs in overrides

------- 1.5 Release [] -------
# Added check for empty widget postions in render
^ Gantry Logo widget 'Per Style' setting is no longer hidden
^ Changed some JS binds to follow the new ES5 specs
# Fixed Colorchooser and Gradient fields
+ Added cache removal to default widget and override widget ajax actions
^ Gantry Pages widget is adding extra 'active' class for current page
^ Gantry Categories widget is adding extra 'active' class for current category
^ Changed width and padding for the MU Register form
# Fix for cache clear issues
+ Made widget instance overrides available to ajax calls

------- 1.4 Release [] -------
^ Added displayFooter function and supporting renders for themes.
^ Add mootools script in jstools gizmo
# Fix for calling wp_head on the admin side.

------- 1.3 Release [] -------
# Fix for layouts not reversing in RTL mode
# Fix for Duplicating and missing CSS files

------- 1.2 Release [] -------
+ Added Minefield to the list of Browsers
+ Add support for Signup page to template page overrides
^ Force Widget Accessability Mode off for Gantry Themes
# Fixed addStyle to better handle -override files and get propper css file overrides from template
^ Moved cache to be WP Transients based.
+ Added base level diagnostics


------- 1.1 Release [17-Aug-2010] -------
# Fixed support for non WP_Widget based classes.

------- 1.0 Release [15-Aug-2010] -------
! Changelog Creation