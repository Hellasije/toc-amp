=== Table of Content AMP ===
Contributors: H3llas
Tags: toc, toc amp, table of content, amp, without javascript
Requires at least: 6.0
Tested up to: 6.2
Stable tag: 1.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

The Table of Contents AMP plugin generates a table of contents for posts and pages based on the subheadings in the post content. It works on AMP (Accelerated Mobile Pages) as well. The plugin allows WordPress administrators and developers to easily create a table of contents for their content by adding anchors to the headings and generating a list of links to those anchors.

== Usage ==

Just install the plugin and select desired settings in option. This plugin offers just basic styling. You will need to customize CSS in order that it fits to your theme look and feel.

The plugin also includes a setting for the title of the table of contents, which can be customized by the administrator or developer. The table of contents is generated using a `<details>` html element, which allows users to expand and collapse the table of contents using a summary element. The plugin includes an option to control whether the `<details>` element is open or closed by default.

== Shortcode ==

The plugin includes a shortcode, <strong>[table_of_contents]</strong>, that can be used to insert the table of contents into the content of a post or page. The plugin also includes an option to automatically insert the table of contents into the content of posts or pages, depending on the settings configured by the administrator or developer.

You can also specify the custom title attribute <strong>title="Table of Contents"</strong>. It should look like this <strong>[table_of_contents title="Table of Contents" ]</strong>. If attribute is not specifed, [table_of_contents] shortcode will use title from the settings.<br>

To hide the title completely in output add the <strong>show_title="0" attribute to shortcode</strong>. For example <strong>[table_of_contents title="Table of Contents" show_title="0"]</strong>.

== TOC AMP needs your support ==

It is hard to continue development and support for this free plugin without contributions from users like you. If you enjoy using TOC AMP and find it useful, please consider [making a donation](https://contactform7.com/donate/). Your donation will help encourage and support the plugin's continued development and better user support.

== Recommended plugins ==

The following plugins are recommended for Table of Content AMP users:

* [Widget Logic](https://wordpress.org/plugins/flamingo/) by WPCHef - lets you control the pages that the widget will appear on.

== Installation ==

1. Upload the entire `toc-amp` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** screen (**Plugins > Installed Plugins**).

For basic usage, have a look at the [plugin's website](https://www.ascic.net/).

== Screenshots ==

1. screenshot1.png
2. screenshot2.png

== Changelog ==

= 1.3 =

Added shortcode option.

= 1.2 =

Added option to disable or enable TOC inserting per post types.
Added custom title option.

= 1.1 =

Added CSS styles and classes.

= 1.0 =

Initial version with basic functionality.
