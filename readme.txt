=== SrbTransLatin - SrbTransLatin ===
Contributors: seebeen
Donate link: https://sgi.io/donate
Tags: letter, serbian, cyrillic, latin, transliteration, latinisation, script, multilanguage, wpml-compatible
Requires at least: 5.3
Tested up to: 5.5
Requires PHP: 7.2
Stable tag: 2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

SrbTransLatin plugin allows you to use both Cyrillic and Latin scripts on your website.

== Description ==

### SrbTransLatin: The only WordPress transliteration plugin

SrbTransLatin enables you to have **both cyrillic and latin scripts** on your website. Transliteration is done in-place automatically.

If your content is written in cyrillic script, this plugin will allow your visitors to view the content both in cyrillic and latin scripts.
This plugin also fixes searching cyrillic posts using latin script.

### Features

* Works everywhere - Plugin hooks into WordPress core transliterating your content inplace
* **Auto-Fix permalinks** - Your cyrillic permalinks will be automatically saved as latin (optional)
* **SEARCH FIX** - Search posts written in cyrillic script using both latin and cyrillic script
* **Partial transliteration** - You can selectively choose which parts of your website to keep in cyrilic
* **Widget Ready** - Switch script via sidebar widget
* **Ajax Transliteration** - Transliterates dynamic parts of your website
* SEO Friendly - no double content or SERP penalties!
* Lightweight - Plugin does not use any external stylesheets, or js files.
* WPML Compatible - Fully compatible with WPML. Transliteration only works when Serbian language is active
* Polylang compatible - Fully compatible with Polylang. Transliteration only works when Serbian language is active
* Script / Language selector - Integrates into WPML language selectors in menu / widget

== Documentation ==

If you can't find your anwsers in the FAQ below, documentation can be found [here](https://rtfm.oblak.studio/srbtranslatin)

== Authorship ==

Original version of this plugin was developer by [Predrag Supurović](https://pedja.supurovic.net/).
Plugin development was handed over to [me](https://oblak.studio) in march 2020. Since then, I am the sole author and maintainer of the plugin

== Installation ==

1. Upload serbian-latinisation.zip to plugins via WordPress admin panel, or upload unzipped folder to your plugins folder
2. Activate the plugin through the "Plugins" menu in WordPress
3. Go to Settings->Latinisation to manage the options

== Frequently Asked Questions ==

= Can I use your plugin to transliterate my latin content into cyrillic? =

Short answer: No.

Long Answer: At the moment, no. Such conversions are very hard to do since HTML language is also written in latin script, and converting text only is very resource intensive process.

I'm currently  working on a tool which will enable you to convert your latin content into cyrillic

= How can I keep parts of content in cyrilic? =

You can use [stl_cyr][/stl_cyr] shortcode. This will keep the shortcode text in cyrilic script, even when viewing latin version of the site.

= Will this plugin transliterate my page title into latin as well? =

Yes, it will.

= Will this plugin enable my visitors to search for content using latin script =

Yes it will. Plugin hooks into default WordPress search function and enables searching of cyrilic content using latin text on frontend

= Is this plugin Compatible with WPML =

Yes it is. It has no compatibility issues with WPML, since the transliteration core is being used only in serbian version of the website

= Will I be able to select script for Serbian language in WPML language Widget? =

Yes, Plugin fully integrates with all WPML functions on the frontend because it directly extends the available language list

= I'm having search issues - not all posts show up when searching for them using latin characters = 

Open a support thread, or send me an e-mail.

= Your plugin is converting my cyrilic filenames into latin which prevents them from loading = 

First - you shouldn't be using cyrilic filenames in the first place. Due to the fact that most hosting providers do not have full UTF-8 support.

I'm working on a tool which will convert filenames and post / page content on your website, so the errors will be automagically fixed

Temporary fix: redownload all attachments with cyrilic filenames and reupload them. This plugin will do an on-the-fly conversion of filenames.

= Will this plugin transliterate dynamic content (ajax) =

Yes it will. Option was added in version 2.4

= Some parts of my page aren't being transliterated =

Depending on the theme / plugins you're currently using, some content might be generated via REST API (wp-json). I'm working on adding the functionality to transliterate REST calls

Feel free to contact me via e-mail, and I'll see if I can assist you for your specific case.

== Screenshots ==

1. Plugin Settings page

2. Script Selector

3. WPML Language selector with cyrillic and latin scripts

== Changelog ==

= 2.0.2 =

Bug Fixes:
* Fixed PHP Fatal Error on certain hosting configurations
* Fixed PHP notices on certaing PHP configurations
* Fixed legacy script selector function

= 2.0.1 =
Release date: March 22nd, 2020

Breaking changes:

* Complete plugin is namespaced, old functions can still be used without namespace, but they will throw **E_USER_DEPRECATED** error
* Shortcodes have been renamed, old shortcodes can still be used, but they will throw **E_USER_DEPRECATED** error
* Minimum WP version bumped to 4.8
* Minimum PHP version bumped to **7.0**

Improvements:

* Full PSR-1 and PSR-12 compatibility
* Full PSR-4 compatibility
* Huge performance increase over version 1.70 of SrbTransLatin and Version 1.4 of Serbian Latinisation
* Reworked the settings screen
* Reworked the entire shortcode functionality
* Moved Transliterator to a composer module, details can be found [here](https://github.com/seebeen/Transliterator)

Features:

* New Script Selector Widget
* New Script Selector function

Bug Fixes:

* Fixed transliteration not working when WPML is active
* Fixed buggy transliteration in certain scenarios
* Fixed issues with performance when using Avada Theme
* Fixed Shortcodes not behaving properly when WPML is active
* Fixed Memory leaks
* Fixed Performance issues on cerain hosting providers

== Upgrade Notice ==

When upgrading from 

* SrbTransLatin 1.XX
* Serbian Latinisation 1.X.X

your options will be migrated from the old versions, please double check settings panel to see if everything is in order.

