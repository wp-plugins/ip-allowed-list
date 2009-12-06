=== IP Allowed List ===
Contributors:	Anthony Ferguson
Donate link:	http://www.fergusweb.net/donate/
Tags:			private, ip address, allowed, list, whitelist
Tested up to:	2.8.6
Requires at least:	2.5
Stable tag:		1.1

Limits access to the site to people on an allowed list of IP addresses.

== Description ==

Only people on the allowed list can see your site.  Others will see a customisable "Coming Soon" style of page.  To remove protection, simply disable this plugin.

You'll get a new admin screen that lets you view the allowed list, and remove IPs from that list.  The content that people not on the list see is fully customisable through the admin screen.

Useful when you're working on the site, but aren't ready for the public to see it.  Primarily, I use this for client sites.  I develop the site, allow the client to see it, then remove this plugin when we're ready to launch it.

== Installation ==

1. Upload plugin to your wordpress installation plugin directory
1. Activate plugin through the `Plugins` menu in Wordpress
1. Look at the configuration screen (found under `Tools` in the Wordpress menu)
1. Customise your Tokens or your Coming Soon content if you wish (the defaults will work for most people)
1. You're done!

== Frequently Asked Questions ==

= What's this about tokens? =
Tokens are an easy way to allow other people to add themselves to the list.  If your token is `letmein`, then anybody who goes to http://yoursite.com/?letmein will be added to the list.

There are tokens to Add and Remove yourself from the allowed list.  Tokens only affect that visitors IP - nothing else on the list is changed.

= How do I customise the Coming Soon content? =
Go to the admin page, and click on "Blocked Content".  This lets you see the raw HTML code of the Coming Soon page non-allowed visitors will see.  You can insert any HTML code you like there.

= Can't I use an existing page on my site as the Coming Soon content? =
Not yet.  Look for that in a future release.

== Changelog ==

= 1.1 =
* The codepress editor for editing your "Coming Soon" page HTML has been fixed.
= 1.01 =
* Minor fix in definitions, improved is_writable error message.
= 1.0 =
* Initial release.  Provides core functionality to shut down your blog, allowing only people on the allowed list to see your site.
