=== WebJunk PHPList ===
Contributors: Paul Newman
Donate link: http://WebJunk.com/
Tags: phplist,email management,mailing, mailing list
Requires at least: 2.1.7
Tested up to: 3.0.1
Stable tag: 1.2.0

WebJunk PHPList is a Wordpress plugin that integrates a Seperate PHPList install with Wordpress.

== Description ==

WebJunk PHPList brings PHPlist - to the Wordpress world.
This plugin allows you to use a seperate installation of PHPList. This allows you to use PHPList with any other web application or even directly on the Internet. As a seperate install You can upgrade or reinstall WordPress with it never affecting PHPList. The version of PHPList is also not tied to a plugin. If needed you can move PHPList.
WebJunk PHPList displays the actual configurable subscriber page from PHPList so you are not limited to text only fields. Anything used in PHPList directly will work within Wordpress with this plugin. 
Since PHPList is not dependant on Wordpress at all, you can safely assign other administrators without having to assin them any privledges in Wordpress.
 

phplist is the world's most popular open source email manager

== Installation ==

1. Upload the `wjphplist` folder to the `/wp-content/plugins/` directory
2. Change permissions on directory cache to 755 or 777 permissions (depending on host)
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Add the correct path to your PHPList installation in wp_admin - Settings/Webjunk PHPList
You will see a new page in your menu called "mailing list". 

Please visit the [webjunk](http://webjunk.com/ "WebJunk Wordpress Support") for more information and support.

== Frequently Asked Questions ==

= It don't work =
Please give us specific details. First thing is you must already have PHPList installed on the same server. 
You must also have entered the path to your PHPList install in the top of the wjphplist.php file. 

A page should have been created (by default called "Mailing list") but if it is missing you can create a Page 
(name does not matter) and make sure in Custom Fields it contains:
Name: wj_mail_page
Value: mailz


== Screenshots ==

Screenshots are not yet available, anyway, just install the plugin and try it out, it's pretty easy.


== Other ==
Fixes applied in PHPlists:
* 


== Changelog ==

= 1.2.0
* Cleaned up Code to resolve minor issues reported


= 1.1.0 = 
* Fixed Admin Login issue
* Added back Logout link
* Moved PHPList URL to wp-admin/Settings


= 1.0.1 = 
* Admin uses phplists own phplist.css

== Upgrade Notice ==
= 1.2.0
* Cleaned up Code to resolve minor issues reported

= 1.1.0 = 
* Fixed Admin Login issue
* Added back Logout link
* Moved PHPList URL to wp-admin/Settings

= 1.0.1 =
Fixes some display issues.


