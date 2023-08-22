=== SrbTransLatin - Serbian Latinisation ===
Contributors: oblakstudio, seebeen
Tags: transliteration, latinisation, multilanguage, wpml, translatepress
Requires at least: 6.0
Tested up to: 6.3.1
Requires PHP: 7.4
Stable tag: 0.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

SrbTransLatin plugin allows you to use both Cyrillic and Latin scripts on your website.

== Description ==

### SrbTransLatin: The BEST WordPress transliteration plugin

SrbTransLatin enables you to have **both cyrillic and latin scripts** on your website. Transliteration is done in-place automatically.

### Features (v3+)

**Transliteration features**:

* Website content is automatically transliterated into latin
* Ajax calls are transliterated into latin (both JSON and HTML)
* Your visitors can search cyrillic content using latin script
* Selective transliteration via shortcodes - Force parts of your website to stay in cyrillic script, use custom transliteration (_Gutenblocks coming soon_)

**Performance features**

* **Cache plugin compatible** - No reinventing the wheel. Works OOB with all the popular caching plugins.
* SEO Friendly - no double content or SERP penalties!
* Optimized autoloading - Plugin is PSR-12 compatible and loads the functionalities only when needed

**File and Media features**

* **Script specific files** - You can have separate versions of images or other files for cyrillic and latin scripts
* **Filename transliteration** - Cyrillic uploads are automatically converted to latin script

**Script Selector features**

* Append the script selector to any menu of your choosing - Either as a dropdown, or inline
* Selector widget - place it any sidebar you'd like
* Custom function - Use `stl_selector` function anywhere in the code (but read the docs first)
* Works everywhere - Plugin hooks into WordPress core transliterating your content inplace

**MultiLanguage features**

Tested with:

* PolyLang
* WPML
* TranslatePress
* qTranslateX

== Documentation ==

If you can't find your anwsers in the FAQ below, documentation can be found [here](https://rtfm.oblak.studio/srbtranslatin)

== Authorship ==

Original version of this plugin was developer by [Predrag Supurović](https://pedja.supurovic.net/).
Plugin development was handed over to [Oblak Solutions](https://oblak.studio) in march 2020. Since then, we am the sole authors and maintainers of the plugin

== Installation ==

1. Upload srbtranslatin.zip to plugins via WordPress admin panel, or upload unzipped folder to your plugins folder
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

= 3.0.0 =

Complete plugin refactor

== Upgrade Notice ==

= 3.0.0 =
This is a major update. All functionalities have been reworked. Plugin is bug free, and 3x faster.
