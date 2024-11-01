=== Trash emptier ===
Contributors: mark-k
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=N9XMJZP8F66UG&lc=IL¤cy_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted
Tags: Trash
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 0.9
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Provides a configurable control over the trash emptying algorithm wordpress uses without changing wp-config.php

== Description ==

Once a day wordpress deletes all trashed items which had been in the trash for more then N days, where N stands for the value of EMPTY_TRASH_DAYS constant which may be defined in wp-config.php file.
 
This plugin presents a different way to control the number of trashed days. Instead of manually editing wp-config.php which is usually not convenient and somewhat dangerous, you can set the number of days in a settings page.
In addition there is a tool to empty the cache from Items older the a specified number of days.

Usage:
Get to the settings page via the menu "Options" >> "Empty trash"
Get to the delete trash tool page via the menu "Tools" >> "Empty trash" 

Network considerations:
The plugin was not tested in a network installation, but it should work gibing the individual site admin a control over his trash which is possibly different then other sites on the network.
The plugin do not have network wide settings

Important:
For better or worse the plugin employs **exactly** the same deleting algorithm used by the wordpress version running the site.

Limitations:
The plugin will not be able to function well if you have EMPTY_TRASH_DAYS set in your wp-confing.php

== Installation ==

If installing via the wordpress admin from the repository:
1. Activate the plugin through the 'Plugins' menu in WordPress

If manually uploading:
1. Download plugin archive in zip or gzipped tar format and extract the files on your computer.

1. Use an FTP or SFTP client to upload the trashemptier directory to wp-content/plugins directory of your WordPress installation. 

1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently asked questions ==

No questions were asked yet ;)

== Changelog ==

= 0.9 =
Initial release





