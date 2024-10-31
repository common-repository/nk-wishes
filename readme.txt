=== nK Wishes ===
Contributors: nko
Tags: wishes, wedding
Requires at least: 4.0.0
Tested up to: 4.4
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Wishes for Wedding Sites



== Description ==

This plugin adds custom post format for Wishes. You can use shortcodes to add form to send wishes and list with wishes.


= Shortcodes =

1. __[nk_wishes_form]__ - show wishes form.
1. __[nk_wishes]__ - show wishes list.

Available attributes:

1. __count__ - how many wishes show on the page
1. __pagination__ - show or hide pagination
1. __class__ - add custom classnames

__[nk_wishes count="5" pagination="true" class="my-class-name"]__ - usage with all attributes


= Custom Templates for Theme =

You can change templates for form and wishes output. Copy php files from __nk-wishes/templates/__ to yout theme folder in __your-theme/nk-wishes/__.
Then plugin will check templates in theme and use if it's available.


= Real Examples =

[In Love Wedding Theme](http://wp.nkdev.info/in-love/wishes/)



== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/nk-wishes` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->Plugin Name screen to manage wishes
1. Use shortcodes (see description page)



== Screenshots ==

1. List shortcode
2. Form shortcode
3. Change wish in admin area



== Changelog ==

= 1.0 =
* Initial Release



== Upgrade Notice ==

= 1.0 =
Initial Release