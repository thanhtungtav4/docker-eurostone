=== Plugin Name ===
Contributors: akshayshah5189
Donate link: https://paypal.me/imobsphere?locale.x=en_GB
Tags: custom taxonomy, remove taxonomy slug, slug, taxonomy, clean url
Requires at least: 3.0.1
Tested up to: 6.2.2
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin will help you to remove the default custom slug from the url which is common requirement for all the SEO lovers.

With less technical knowledge you can have this feautre into the place.


== Description ==
This plugin will give you the advantage of having the clean seo url. You can remove taxonomy slug and have the seo friend url by click couple of clicks.

This plugin can remove the custom taxonomy slug as well by going into the plugin settings.

You can do this by doing the admin login and check admin menu > Remove Taxonomy Slug Settings

Once you click on it you will be able to see all existing the taxonomy slug you just need to selected it and save the settings.

For the developer i have given the filter as well with the use of that filter you can directly add the slug in the array which will remove the slug.

Filter name is remove_taxonmy_slug_filter.
add_filter( 'remove_taxonmy_slug_filter',function ( $slug_list ){
	return $slug_list
} );

In the above array you need to make sure that you pass the slug name as it is.

It is working with the below functions you just need to make sure that you have used the one of the below function to have the catgory link.

term_link
get_category_link
get_term_link
category_link

This plugin is compatible with custom post type ui as well.

Apart of this you can create the ticket for your query or reach me out for quick question on akshay.shah5189@gmail.com


If you are looking to remmove the post type slug, I am having another plugin that you can find it here : https://wordpress.org/plugins/remove-post-type-slug/

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `remove-taxonmy-slug.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Goto admin menu and click on the taxonomy slug and select the slug from slug list.
4. You are done.


== Screenshots ==

1. Admin side setting screenshot.

== Changelog ==
= 1.1 =
* Version Update
= 1.0 =
* A change since the previous version.
* Another change.
