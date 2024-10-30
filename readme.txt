=== Liturgical Day of the Week ===
Contributors: BearlyDoug
Plugin URI: https://wordpress.org/plugins/liturgical-day-of-the-week/
Donate link: https://paypal.me/BearlyDoug
Tags: Lectionary, Liturgical, Liturgical Day of the Week, Feasts, Memorials, Lectionary Colors, Liturgical Colors, Catholic, Saints, Religious Colors
Requires at least: 5.5
Tested up to: 6.4.1
Stable tag: 1.0.4
Requires PHP: 7.3
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Based on the Catholic Liturgical / Lectionary Calendar, Liturgical Day of the Week (LDotW) presents with a colored background associated with that day, plus the day's title, via a combination of the USCCB Liturgical Calendar, and CatholicCulture.org's Liturgical Calendar. One saveable option (time zone offset), plus alternative texts (for organizations within the following Dioceses: Boston, Hartford, New York, Newark, Omaha, Philadelphia), and a simple shortcode is all that's needed.

== Description ==
Liturgical Day of the Week (LDotW)

I had a parish requestt that I incorporate the LDotW wording and color onto their newly redesigned website. I was going to initially make this specific to their site, but then realized this could be adapted to serve a lot of Catholic parishes.

What helps make this plugin a bit unique (especially on its initial release) is the fact that we can handle alternate text as needed. Some Dioceses / Churches need to utilize alternate LDotW titles, which differ from the "standard" ones.

Version 1.0.3 brings the "Saint of the Day" plugin completely inside LDotW, allowing me to retire "SotD". The code bases and data structures were nearly identical, so it was logical to bring the two plugins together.

This version also brings a few more configuration options, allowing you full control of the output. It also brings a nice visual view when configuring the shortcode options via the shortcode builder.

In addition to that, this version moves the time zone settings to a more centralized location to streamline the plugin. Plus, there was a bit of code clean up to reduce the overall file size and wasted space within the plugin.

Remember to deactivate and delete the "Saint of the Day" plugin, if installed/activated, please.

PLANNED: Incorporating LDotW calendars for other denominations. Version 1.0.0 - 1.0.3 is specific for the Roman Catholic Churches of the United States.

**Current Version 1.0.3**

= Features: = 
* Shortcode builder lets you set whether to display alternate titles, as a result of your parish having slight deviations from the standard Liturgical Calendar (Boston, Philadelphia, etc, use a slightly modified Liturgical calendar from the rest of the dioceses).
* Saint of the Day, as well as Feast, Memorial and Solemnity indicator.
* Some days have more than one color present. Version 1.0.3 introduces an indicator (color wheel with mouseover visual cue) when present for that day.
* Works anywhere you can use shortcode.
* Only background color and text color defined via CSS. You can control the look/feel by adding your own CSS styling to a specific CSS class

This plugin is not compatible with WordPress versions less than 5.0. Requires PHP 5.6+ (though you should be on 7.4 or even 8 by now).

= TROUBLESHOOTING: =
* Check the FAQs/Help located on WordPress' Plugin page, or the Support forum on WordPress.org's plugin area.
* The Shortcode Builder has been extensively tested with both jQuery version 1.12.4 and 3.5.1, without any issues. The output, however, does not need jQuery/JavaScript.

== Installation ==

= If you downloaded this plugin: =
1. Upload the `liturgical-day-of-the-week` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Once activated, locate the "BD Plugins" section in WP Admin, and then click on "Liturgical DotW".
4. Follow the directions on the primary tab, etc.
5. Remember to deactivate and delete the "Saint of the Day" plugin, if installed/activated.

= If you install this plugin through WordPress 2.8+ plugin search interface: =
1. Click "Install" on 'Liturgical Day of the Week".
2. Activate the plugin through the 'Plugins' menu.
3. Once activated, locate the "BD Plugins" section in WP Admin, and then click on "Liturgical DotW".
4. Follow the directions on the primary tab, etc.
5. Remember to deactivate and delete the "Saint of the Day" plugin, if installed/activated.

== Frequently Asked Questions ==
** As this is the first release of RPU, FAQs are a little minimal right now ** 

= Help! The background color is somewhat transparent! =
This is more than likely a result of how CSS behaves, when the "opacity" setting is used. Any elements (including nested elements) contained within that one element will have a semi-transparent/opaque background. The fix for this is to remove any "opacity" CSS calls and to replace them with a "background: rgba()" call. See the "Known Issues / Troubleshooting" section on <a href="https://bearlydoug.com/plugins/liturgical-day-of-the-week/" target="_blank">THIS PAGE</a> for more information.

= What's with the animated bear icon / Why "BearlyDoug"? =
You'll need to check out the plugin and click on "BD Plugins" after you activate this plugin. :)

= Why free? Do you have a commercial version, too? =
Because I want to give back to the community that has given so much to me, no. What you see is what you get.WordPress has allowed me to advance my career and put me into a position where I'm doing okay. That said, you can still support this plugin (and others, as I release them) by hittin' that "Donate" link over on the right.

== Screenshots ==
1. Demo Liturgical Day of the Week (LDotW) look
2. Available LDotW colors
3. Styling the LDotW output via CSS class name
4. WP Admin interface

== Changelog ==
= TODO =
* Updates to reflect current year cycles (currently runs through November 30th, 2021)
* Additional Denominations support (US Catholic currently supported)
* Non United States Diocese (and in their respective languages)
* Suggestions from you?

= 1.0.4 =
* Released (November 23, 2023)
* Emergency fixes to the timezone offset settings page (was not saving changes).
* Feast / Memorial / Solemnity toggle wasn't working correctly. Fixed!

= 1.0.3 =
* Released (November 23, 2023)
* Migrated the Timezone settings to the "Bearly Doug" page to centralize it.
* Since the "Saint of the Day" plugin is being merged in with this, restructured LDotW to support that natively.
* Added the ability to control what gets shown within the LDotW box (wording), and to control placement.
* Improved the Demo box to visually show the new various toggles on the shortcode builder.
* Added Year B (Nov 27, 2023 through Nov 30, 2024) colors and days.
* Bumped Support to Wordpress 6.4.1

= 1.0.2 =
* Released (November 22nd, 2022)
* Fixed errors where no color was defined for a specific day; defaults to a white background and "Liturgical color of the day: White" for wording.
* Added Year A (Nov 27, 2022 through Nov 26, 2023) colors and days.
* Bumped Support to Wordpress 6.1.1

= 1.0.1 =
* Released (September 17th, 2021)
* Changed the timezone offset field name from "ldotwtzoffset" to "bdoffset", since this timezone offset feature will be used on the "Liturgical Day of the Wekk" plugin, as well as the upcoming "Saint of the Day" and "Question of the Week" plugins.
* Fixed the readme.txt file (several references to the RPU plugin changed to reference LDotW, instead).
* Added "pink" and "rose" to the available lectionary colors.
* Due to two identical functions in LDotW and SotD, I've renamed them both, in both plugins to make them specific for each plugin.
* Coding optimization: Combined four separate functions into a single function to clean up code and make this plugin far more efficient. Reduced main plugin file size by almost 5KB.

= 1.0.0 =
* Initial Plugin development and launch (Sept. 17th, 2021)
* Special thank-you to Ed McN at Francis de Sales Catholic Church for the inspiration behind this!

== Upgrade Notice ==
* Coming soon!