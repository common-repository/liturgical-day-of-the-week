<?php
/***
 * Plugin Name: Liturgical Day of the Week
 * Plugin URI: https://wordpress.org/plugins/liturgical-day-of-the-week/
 * Description: Based on the Catholic Liturgical / Lectionary Calendar, this plugin presents with a colored background associated with that day, plus the day's title, via a combination of the USCCB Liturgical Calendar, and CatholicCulture.org's Liturgical Calendar. One configurable option (time zone offset) and a simple shortcode is all that's needed. Also includes alternative text for organizations within the following Dioceses: Boston, Hartford, New York, Newark, Omaha, Philadelphia
 * Version: 1.0.4
 * Requires at least: 5.2
 * Author: Doug "BearlyDoug" Hazard
 * Author URI: https://wordpress.org/support/users/bearlydoug/
 * Text Domain: liturgical-day-of-the-week
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * This program is free software; you can redistribute it and/or modify it under 
 * the terms of the [GNU General Public License](http://wordpress.org/about/gpl/)
 * as published by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, on an "AS IS", but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, see [GNU General Public Licenses](http://www.gnu.org/licenses/), or write to the
 * Free Software Foundation, Inc., 51 Franklin Street, 5th Floor, Boston, MA 02110, USA.
 */

/***
 *	Setting up security stuff and paths...
 */
defined('ABSPATH') or die('Sorry, Charlie. No access for you!');
require_once(ABSPATH.'wp-admin/includes/file.php' );
require_once(ABSPATH.'wp-admin/includes/plugin.php');

/***
 * Including the BearlyDoug functions file...
 */
require_once('functions-bd.php');

/***
 * DEFINE VERSION HERE
 */
define('ldotwVersion', '1.0.4');
define('ldotw', 'ldotw');

/***
 * Liturgical Day of the Week link.
 */
function bearlydougplugins_add_ldotw_submenu(){
	add_submenu_page(
		'bearlydoug',				// Parent Slug
		'Liturgical Day of the Week',	// Page Title
		'Liturgical DotW',			// Menu Title
		'edit_posts',				// Capabilities
		'ldotw',				// Nav Menu Link
		'ldotw_main_admin_interface'	// Function name
	);
}
add_action('admin_menu', 'bearlydougplugins_add_ldotw_submenu', 15);

/***
 * Loading both the Admin and Plugin CSS and JavaScript files here. Will also check to see if the main
 * BearlyDoug CSS file is enqueued. If not, then enqueue it.
 */
add_action('admin_enqueue_scripts', 'ldotw_enqueue_admin_files', 15);
function ldotw_enqueue_admin_files(){
	wp_register_style('ldotw', plugins_url('/includes/_CSS-ldotw.css',__FILE__ ));
	wp_enqueue_style('ldotw');

	/***
	 * This has to get loaded into the footer, only if on the "ldotw" page.
	 */
	if(isset($_GET['page']) && $_GET['page'] == 'ldotw') {
		wp_enqueue_script('ldotwscbuilder', plugins_url('/includes/_JS-ldotwSCBuilder.js?v=2a',__FILE__ ), array(), false, true);
	}

	if(!wp_style_is('bearlydougCSS', $list = 'enqueued')) {
		wp_register_style('bearlydougCSS', plugins_url('/includes/_CSS-bearlydoug.css',__FILE__ ));
		wp_register_script('bearlydougJS', plugins_url('/includes/_JS-bearlydoug.js',__FILE__) );
		wp_enqueue_style('bearlydougCSS');
		wp_enqueue_script('bearlydougJS');
	}
}

/***
 * Loading only the Plugin CSS file here.
 */
add_action('wp_enqueue_scripts', 'ldotw_enqueue_shortcode_files', 15);
function ldotw_enqueue_shortcode_files(){
	wp_register_style('ldotw', plugins_url('/includes/_CSS-ldotw.css?v=1',__FILE__ ));
	wp_enqueue_style('ldotw');
}

/***
 * Handling the Liturgical Day of the Week admin page and tags saving function...
 */
function ldotw_main_admin_interface(){

	/***
	 * We need to get the timezone offset and correct any time inconsistencies...
	 */
	$TZOffset = get_option('bdtzoffset', '0');

	/***
	 * Going to go ahead and set up some variables to handle the server time and corrected WP time
	 * notations for the timezone offset stuff. We'll also set up an array for the corrected WP time
	 * correction feature in the Shortcode Builder area. Setting up the offset minutes...
	 */
	$offsetMinutes = str_replace("-", "", $TZOffset) * 3600;

	/***
	 * If TZOffset is less than 0, then we'll subtract the time. If greater, add it. $tNOW is always based on GMT.
	 */
	$timeNOW = time();
	if($TZOffset >= 0) {
		$correcedTime = $timeNOW + $offsetMinutes;
	} else {
		$correcedTime = $timeNOW - $offsetMinutes;
	}

	$ServerTime = date("h:i:s A", $timeNOW);
	$CorrectedTime = date("h:i:s A", $correcedTime);

	/***
	 *
	 */
	$ldotwInfo = ldotwInfo($correcedTime);
	$ldotwTitle = (isset($ldotwInfo['title'])) ? $ldotwInfo['title'] : 'Liturgical color of the day: White';
	$ldotwSaint = (isset($ldotwInfo['saint']) && $ldotwInfo['saint'] != "SAME") ? $ldotwInfo['saint'] : $ldotwTitle;
	$backgroundColor = $ldotwInfo['backgroundColor'];
	$textColor = $ldotwInfo['textColor'];

	/***
	 * Let's show the WP Admin interface!
	 */
	echo '
	<form action="admin.php?page=ldotw" method="post">
		<input type="hidden" name="ldotwSubmit" value="1">
		<h1 class="bdCTR">Liturgical Day of the Week, v' . constant("ldotwVersion") . '</h1>
		<div class="bdTabs">
<!-- bdTabs Navigation Tabs -->
			<input type="radio" name="bdTabs" class="bdRadio" id="bdTab1" checked >
			<label class="bdLabel" for="bdTab1"><i class="dashicons dashicons-shortcode"></i><span>Shortcode Builder</span></label>
			<input type="radio" class="bdRadio" name="bdTabs" id="bdTab2">
			<label class="bdLabel" for="bdTab2"><i class="dashicons dashicons-info-outline"></i><span>About LDotW</span></label>
			<input type="radio" class="bdRadio" name="bdTabs" id="bdTab3">
			<label class="bdLabel" for="bdTab3"><i class="dashicons dashicons-universal-access"></i><span>More BD Plugins</span></label>

<!-- BD LDotW Hidden Fields -->
			<input type="hidden" id="ldotwtextWording" name="ldotwtextWording" />
			<input type="hidden" id="ldotwtextType" name="ldotwtextType" />
			<input type="hidden" id="ldotwtextCWheel" name="ldotwtextCWheel" />
			<input type="hidden" id="ldotwtextDiocese" name="ldotwtextDiocese" />

<!-- bdTabs Content Tabs -->
			<div id="bdTab-content1" class="bdTab-content">
				<div class="bdWrapper">
					<div class="bdRow">
						<div class="bdDColumn">
							<div>For demo purposes, we show ALL configuration items and style the box below to give you a visual look at how the output looks. See below for instructions on how to style (via a CSS class) the output, and for our styling (that you can simply copy/paste), if desired.</div>
							<br />
							<div id="ldotwDemo" class="ldotwDemo" style="background: rgba(' . $backgroundColor . ',1) !important; color: ' . $textColor . ' !important;"><span id="ldotwSamplecwheel" style="float: right;"> <img src="' . plugin_dir_url( __FILE__ ) . 'images/colorWheel.png" title="green/red/white/red/white" alt="green/red/white/red/white" /></span><span id="ldotwSampleType"> (Solemnity)</span> <span id="ldotwSampleTitle"></span><span id="ldotwSampleSaint"></span></div><br style="clear: right;" />

							<fieldset>
								<legend>Base Configuration</legend>
								<div><strong>Text Display:</strong> How should we display the text above?</div>
								<div class="bdCTR">
									<label><input class="ldotwDWording" type="radio" name="ldotwDWording" value="WordingSaint" checked /> LD Wording, then Saint</label> &emsp; 
									<label><input class="ldotwDWording" type="radio" name="ldotwDWording" value="SaintWording" /> Saint first, then LD Wording</label><br />
									<label><input class="ldotwDWording" type="radio" name="ldotwDWording" value="ldwording" /> Wording only</label> &emsp; 
									<label><input class="ldotwDWording" type="radio" name="ldotwDWording" value="saint" /> Saint Only</label> &emsp; 
									<label><input class="ldotwDWording" type="radio" name="ldotwDWording" value="none" /> None (Color bar only)</label>
								</div>

								<div><br /><strong>Feast, Memorial, Solemnity Placement:</strong> Where should we display this?<br />(note: for the above box, it\'ll show to the left on the first line only)</div>
								<div class="bdCTR">
									<label><input class="ldotwfmdType" type="radio" name="ldotwfmdType" value="wordingleft" checked /> To the left of LDotW Wording</label> &emsp; 
									<label><input class="ldotwfmdType" type="radio" name="ldotwfmdType" value="wordingright" /> To the right of LDotW Wording</label><br />
									<label><input class="ldotwfmdType" type="radio" name="ldotwfmdType" value="saintleft" /> To the left of Saint</label> &emsp; 
									<label><input class="ldotwfmdType" type="radio" name="ldotwfmdType" value="saintright" /> To the right of Saint</label><br />
									<label><input class="ldotwfmdType" type="radio" name="ldotwfmdType" value="none" /> Hide completely</label>
								</div>

								<div><br /><strong>Multiple Colors for Liturgical Day:</strong> Some days have multiple colors available. While we only show the first color as a background, we have the ability to show a color wheel that has all colors for that day when you hover your mouse over the image.</div>
								<div class="bdCTR">
									<label><input class="ldotwcwheel" type="radio" name="ldotwcwheel" value="show" checked /> Show Color Wheel (when available)</label> &emsp; 
									<label><input class="ldotwcwheel" type="radio" name="ldotwcwheel" value="hide" /> Hide Color Wheel</label>
								</div>
							</fieldset>
							<br />

							<fieldset>
								<legend>Diocese Specific configuration</legend>
								<div>If this plugin is in use for any of the following Catholic Dioceses, please set the below toggle to "Yes":<br /><strong><u>Boston, Hartford, New York, Newark, Omaha, and/or Philadelphia</u></strong></div>
								<div>
									<br /><strong>Does your organization belong to one of the above named parishes?</strong> 
									<label><input class="ldotwOtherDiocese" type="radio" name="ldotwOtherDiocese" value="0" checked /> No</label>&emsp;
									<label><input class="ldotwOtherDiocese" type="radio" name="ldotwOtherDiocese" value="1" /> Yes</label>
								</div>
								<div><br />Per the <a href="https://www.usccb.org/committees/divine-worship/liturgical-calendar" target="_blank">USCCB\'s Calendar listings</a>, the above named Dioceses will sometimes have alternate titles used. The LDotW plugin provides these alternate titles, so that we can accommodate for these minor differences. For the 2023-24 Liturgical Calendar, only May 9th, 2024 and May 12th, 2024 have alternate titles.</div>
							</fieldset>
							<br />

							<h3 class="bdCTR">Styling the output</h3>
							<div class="bdCT">
								<div class="bdBox2">
									The only styling this plugin does is the background color and the text color. This puts the power of you controlling the look and feel completely within your own hands, using the "liturgicalDotW" CSS class.  For the example above, I\'m using the same styling as on the WP Admin demo. The below listed code was added into the "Additional CSS" section under WordPress\' built in "Customizer" interface:
									<strong>IMPORTANT</strong>: Do not define any background or text colors, please. Those are handled by the plugin, directly and automatically.
								</div>
								<div class="bdBox2" style="background: #dedede;"><strong>CSS code:</strong>
<pre>.liturgicalDotW {
	width: 70%;
	margin: 10px auto;
	padding: 5px;
	text-align: center;
	font-weight: bold;
	font-size: 1.13em;
	border: 1px solid black;
}</pre>
								</div>
							</div>
							<br />
						</div>
						<div class="bdColumn">
							<div id="bdSCcontainer">
								<textarea id="bdShortCode" class="bdCTR" name="bdShortCode" wrap="soft"></textarea>
							</div>
							<div id="bdMsg" class="bdHide">Text copied into your clipboard. Paste where you need/want it.</div>
							<br />
							<fieldset>
								<legend>This plugin is time-zone dependent.</legend>
								<dl class="fancyList2">
									<dt>Server Time</dt><dd>&nbsp; ' . $ServerTime . '</dd>
									<dt>Fixed Time</dt><dd>&nbsp; ' . $CorrectedTime . '</dd>
								</dl>
								<div><br /><strong>NOTE:</strong> If the "Fixed Time" is incorrect, please go to the "Time Zone Settings" tab on <a href="' . admin_url("admin.php?page=bearlydoug") . '">THIS PAGE</a> and set it as needed.</div>
							</fieldset>
							<h3 class="bdCTR">Supported Liturgical Colors</h3>
							<div class="bdCTR">' . do_shortcode(" [liturgicalcolors] ") . '</div>
						</div>
					</div>
				</div>
			</div>
			<div id="bdTab-content2" class="bdTab-content">
				<div class="bdWrapper">
					<div class="bdRow">
						<div class="bdDColumn">
							<h2 class="bdCTR">About Liturgical Day of the Week (LDotW)</h2>
							<div>Had a parish request that I incorporate the Liturgical Day of the Week (wording and color) onto their newly redesigned website. I was going to initially make this specific to their site, but then realized that this script could be adapted to serve a lot of Catholic parishes... and then realized that I could adapt this into a plugin that could support multiple denominations.</div>
							<div><br />What helps make this plugin a bit unique (especially on its initial release) is the fact that we can handle alternate text as needed. Some Dioceses / Churches need to utilize alternate LDotW titles, which differ from the "standard" ones.</div>
							<div><br />While this is the first version of this plugin, it should be robust enough to handle just about any of your needs.</div>
							<br /><h2 class="bdCTR">What\'s next for LDotW?!</h2>
							<div>The following items are planned updates/enhancements, as this plugin moves forward. Not all of them will be implemented in the next release.&ensp;As I "tick off the checklist", I\'ll note the date/version that feature was added and move it to the bottom of each section.</div>
							<ul class="bdList">
								<li>Overall Plugin</li>
								<li>Updates to reflect current year cycles (This plugin covers the Year B cycle, currently runs through November 30th, 2024)</li>
								<li>Suggestions from you?</li>
							</ul>
							<br /><h2 class="bdCTR">List of changes</h2>
							<ul class="bdList">
								<li>Version 1.0.4</li>
								<li>Emergency fix for Timezone Settings page; any changes wasn\'t being saved.</li>
								<li>Emergency fix for Feast/Memorial/Solemnity toggle. Was not working correctly.</li>
								<li>Emergency fix for when the Liturgical Wording is the same as the Saint. Now checking against that and only displaying one line.</li>
							</ul>
							<ul class="bdList">
								<li>Version 1.0.3</li>
								<li>Migrated the Timezone settings to the "Bearly Doug" page to centralize it.</li>
								<li>Since the "Saint of the Day" plugin is being merged in with this, restructured LDotW to support that natively.</li>
								<li>Added the ability to control what gets shown within the LDotW box (wording), and to control placement.</li>
								<li>Improved the Demo box to visually show the new various toggles on the shortcode builder.</li>
								<li>Added Year B (Nov 27, 2023 through Nov 30, 2024) colors and days.</li>
								<li>Bumped Support to Wordpress 6.4.1</li>
							</ul>
							<ul class="bdList">
								<li>Version 1.0.2</li>
								<li>Fixed errors where no color was defined for a specific day; defaults to a white background and &quot;Liturgical color of the day: White&quot; for wording.</li>
								<li>Added Year A (Nov 27, 2022 through Nov 26, 2023) colors and days.</li>
								<li>Bumped Support to Wordpress 6.1.1</li>
							</ul>
							<ul class="bdList">
								<li>Version 1.0.1</li>
								<li>Initial public launch</li>
							</ul>
						</div>
						<div class="bdColumn">
							<h3 class="bdCTR">Important Notes</h3>
							<div>&bull; The following Diocese have a slightly different title for both May 26th, 2022 and May 29th, 2022: <strong><u>Boston, Hartford, New York, Newark, Omaha, Philadelphia</u></strong>. There is a toggle to allow the alternate title to come through for those two days, under the config settings on the first tab.</div>
							<div><br />&bull; We don\'t use any transparency/opacity on our various backgrounds. If you noticec that this is happening, check the parent div and remove any "opacity" CSS classes. You\'ll want to use <strong><u>background: rgba(R, B, G, OPACITY)</u></strong>, instead.</div>
						</div>
					</div>
				</div>
			</div>
			<div id="bdTab-content3" class="bdTab-content">
				<div class="bdWrapper">
					<div class="bdRow">
						<div class="bdDColumn">
							<ul class="bdList">
								<li><strong>All available BearlyDoug Plugins</strong></li>
								<li><a href="https://wordpress.org/plugins/bd-buttons/" target="_blank"><strong>BD Buttons</strong></a> (Initial launch: May 20th, 2021) - BD Buttons was developed to empower the every day person to be able to buttonize any link with an attention grabbing design.</li>
								<li><a href="https://wordpress.org/plugins/quotopia/" target="_blank"><strong>Quotopia</strong></a> (Initial launch: Oct. 10th, 2020) - Yet another quotes plugin. Allows you to load custom quotes for whatever needs your website has. Quotes are loaded via text files; no database additions needed. Can customize many aspects of the display, using the shortcode builder page.</li>
								<li><a href="https://wordpress.org/plugins/recent-posts-ultimate/" target="_blank"><strong>Recent Posts Ultimate</strong></a> (Initial launch: Oct. 10th, 2020) - Recent Posts Ultimate takes the best features of five VERY popular recent posts plugins, tosses in the ability to show posts with or without HTML code and gives you a Shortcode builder (which you can copy/paste anywhere on a page, a post or inside a widget), while allowing custom post types to be used.</li>
								<li><a href="https://wordpress.org/plugins/liturgical-day-of-the-week/" target="_blank"><strong>Liturgical Day of the Week</strong></a> (Initial launch: Sept. 17th, 2021) - Based on the Catholic Liturgical / Lectionary Calendar, this plugin presents with a colored background associated with that day, plus the day\'s title, via a combination of the USCCB Liturgical Calendar, and CatholicCulture.org\'s Liturgical Calendar. One configurable option (time zone offset) and a simple shortcode is all that\'s needed. Also includes alternative text for organizations within the following Dioceses: Boston, Hartford, New York, Newark, Omaha, Philadelphia</li>
							</ul>
							<br />
							<ul class="bdList">
								<li><strong>PLANNED BearlyDoug Plugins</strong></li>
								<li><strong>BD Business Hours</strong> - A schedule/events calendar that integrates with both Google and Outlook Calendar feeds, while allowing custom category colors, and better update management than what some of these other calendar plugins offer.</li>
							</ul>
							<br />
							<ul class="bdList">
								<li>Some quick shout-outs!</li>
								<li><a href="https://www.linkedin.com/in/thomasjuberg/" target="_blank">Thomas Juberg</a>, AKA "Brother Bear". One of my best men when I got married, the one that opened my eyes on semantic and proper CSS development, one of my best friends, who lives doggone near half a world away.</li>
								<li><a href="https://www.facebook.com/JohnJDonnaII" target="_blank">John Donna</a>. My WordPress Mentor (and sometimes Mentee, now), former boss, former co-worker, good friend. He\'s there when I need him, and even when I don\'t. ;)</li>
								<li>Andor Nagy (@AndorNagy) with WebDesignHut, for the beautiful tabbed content structure you see here.</li>
								<li>Adrian B., from StackOverflow, for the HTML tags allowed functions (two separate guides).</li>
								<li>My wife, Sonia, and my kidlings, Andria and Kaleb. Cause no shout-out would be complete without them!</li>
								<li>YOU, for downloading and installing one of my plugins!</li>
							</ul>
						</div>
						<div class="bdColumn">
							<h3 class="bdCTR">Latest BD News</h3>
							<div>Coming soon!</div>
							<br />
							<div>
								<h3 class="bdCTR">Want to show some love?</h3>
								<img class="bdFloatLeft" src="' . plugin_dir_url( __FILE__ ) . 'images/CodeForCoffee.jpg" />
								<a href="https://paypal.me/BearlyDoug" target="_blank"><img src="' . plugin_dir_url( __FILE__ ) . 'images/PayPal.jpg" /></a><br />
								<a href="https://cash.app/$BearlyDoug" target="_blank"><img src="' . plugin_dir_url( __FILE__ ) . 'images/CashApp.jpg" /></a>
								<div><br />I\'ve been able to benefit, greatly, from the WordPress community, my mentor and other free (and paid) plugins.&ensp;It\'s allowed me to leverage my knowledge into a full time salaried WordPress developer position.</div>
								<div><br />That said, this plugin, and any others I release are done so with the intent of keeping them 100% <strong><u>free / no cost</u></strong> to you.</div>
								<div><br />I feel like this is the best way I can give back to the community that has benefited me so much.</div>
								<div><br />If this plugin has helped you out, why not chip in a few bucks to buy me a cup of coffee or something?&ensp;I have no plans on making this plugin (or any others) a commercial (paid upgrade) one, so kicking in a few bucks (via the PayPal or CashApp images/links above) helps keep this plugin (and others) movin\' forward.&ensp;Thanks!</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
';
}

/***
 * Returns the title, alt title and color for today's LDotW.
 */
function ldotwInfo($date, $altdiocese = null, $lctype = null) {
	/***
	 * Supporting other denominations aside from US-Catholic (default if none selected).
	 * Also pulls together the dataset...
	 */
	$lctype = (is_null($lctype)) ? 'US-Catholic' : sanitize_text_field($lctype) ;
	$json1_url = plugin_dir_path( __FILE__ ) . 'includes/LiturgicalColors_' . $lctype . '.txt';
	$json1 = file_get_contents($json1_url);
	$ldotwDetails = json_decode($json1, TRUE);

	/***
	 * Pulls together the Title, Alternate Title and color, based on the time included in the function.
	 * Places data into an array.
	 */
	$result = array();
	foreach($ldotwDetails as $ldotwDetail){
		$s = $ldotwDetail['startTime'];
		$e = $ldotwDetail['endTime'];
		if($s <= $date && $date <= $e){
			$result['color'] = sanitize_text_field($ldotwDetail['color']);
			$result['saint'] = sanitize_text_field($ldotwDetail['saint']);
			$result['ldotwType'] = ($ldotwDetail['ldotwType'] != "") ? sanitize_text_field($ldotwDetail['ldotwType']) : '';
			$result['colorCombos'] = ($ldotwDetail['colorCombos'] != "") ? $ldotwDetail['colorCombos'] : '';

			if($altdiocese == "yes") {
				if(sanitize_text_field($ldotwDetail['altTitle']) != "") {
					$result['title'] = sanitize_text_field($ldotwDetail['altTitle']);
				} else {
					$result['title'] = 'Liturgical color of the day: White';
				}
			} else {
				$result['title'] = sanitize_text_field($ldotwDetail['title']);
			}
		}
	}

	/***
	 * Now that we have the WORD version of the color, we need to get the HEX code for it.
	 */
	$masterColorList = array( 
		"beige" => "#f5f5dc",
		"black" => "#000000",
		"blue" => "#0000ff",
		"bright red" => "#b10000",
		"deep blue" => "#220878",
		"gold" => "#d4af37",
		"gray" => "#808080",
		"green" => "#009900",
		"pink" => "#ffc0cb",
		"purple" => "#660099",
		"red" => "#ff0000",
		"rose" => "#ff007f",
		"royal blue" => "#4169e1",
		"scarlet" => "#ff2400",
		"linen" => "#faf0e6",
		"violet" => "#240a40",
		"white" => "#ffffff"
	);

	if($masterColorList[$result['color']] != "") {
		$backgroundColor = strtolower($masterColorList[$result['color']]);
	} else {
		$backgroundColor =  "#ffffff";
	}

	/***
	 * Converting the HEX color code into RGB and adding it into the array.
	 */
	$bgColorBase = str_replace('#', '', $backgroundColor);
	$splitbgColorBase = str_split($bgColorBase, 2);
	$r = hexdec($splitbgColorBase[0]);
	$g = hexdec($splitbgColorBase[1]);
	$b = hexdec($splitbgColorBase[2]);
	$result['backgroundColor'] = $r . ', ' . $g . ', ' . $b;
	
	/***
	 * Getting the contrast color (black or white) to the background color. This will become
	 * the text color. We'll add that to the array, as well.
	 */
	$R1 = hexdec(substr($backgroundColor, 1, 2));
	$G1 = hexdec(substr($backgroundColor, 3, 2));
	$B1 = hexdec(substr($backgroundColor, 5, 2));

	$blackColor = "#000000";
	$R2BlackColor = hexdec(substr($blackColor, 1, 2));
	$G2BlackColor = hexdec(substr($blackColor, 3, 2));
	$B2BlackColor = hexdec(substr($blackColor, 5, 2));

	$L1 = 0.2126 * pow($R1 / 255, 2.2) + 0.7152 * pow($G1 / 255, 2.2) + 0.0722 * pow($B1 / 255, 2.2);
	$L2 = 0.2126 * pow($R2BlackColor / 255, 2.2) + 0.7152 * pow($G2BlackColor / 255, 2.2) + 0.0722 * pow($B2BlackColor / 255, 2.2);

	$contrastRatio = 0;
	if ($L1 > $L2) {
		$contrastRatio = (int)(($L1 + 0.05) / ($L2 + 0.05));
	} else {
		$contrastRatio = (int)(($L2 + 0.05) / ($L1 + 0.05));
	}

	if ($contrastRatio > 5) {
		$textColor = '#000';
	} else { 
		$textColor = '#fff';
	}
	$result['textColor'] = $textColor;

	/***
	 * Okay, we've got everything... return it!
	 */
	return $result;
}

/***
 * Checks to see if the bdtzoffset Meta option exists upon activation. If it doesn't,
 * let's create it and default it to 0
 */
function ldotw_install() {
	if(!get_option('bdtzoffset')){
		update_option('bdtzoffset', '0', 'yes');
	}
}
register_activation_hook(__FILE__, 'ldotw_install');

/***
 *	Showing supported Liturgical colors...
 */
function ldotwcolors_shortcode($atts) {

	/***
	 * The color list
	 */
	$masterColorList = array( 
		"beige" => "#f5f5dc",
		"black" => "#000000",
		"blue" => "#0000ff",
		"bright red" => "#b10000",
		"deep blue" => "#220878",
		"gold" => "#d4af37",
		"gray" => "#808080",
		"green" => "#009900",
		"pink" => "#ffc0cb",
		"purple" => "#660099",
		"red" => "#ff0000",
		"rose" => "#ff007f",
		"royal blue" => "#4169e1",
		"scarlet" => "#ff2400",
		"linen" => "#faf0e6",
		"violet" => "#240a40",
		"white" => "#ffffff"
	);

	$theColorList = array("beige", "black", "blue", "bright red", "deep blue", "gold", "gray", "green", "pink", "purple", "red", "rose", "royal blue", "scarlet", "linen", "violet", "white");
	asort($theColorList);
	$colorsListing = "";

	/***
	 * Gonna show row of items with supported colors...
	 */
	foreach($theColorList as $theColor) {
		$colorHex = $masterColorList[$theColor];

		$R1 = hexdec(substr($colorHex, 1, 2));
		$G1 = hexdec(substr($colorHex, 3, 2));
		$B1 = hexdec(substr($colorHex, 5, 2));
		$backgroundColor = $R1 . ', ' . $G1 . ', ' . $B1;

		$blackColor = "#000000";
		$R2BlackColor = hexdec(substr($blackColor, 1, 2));
		$G2BlackColor = hexdec(substr($blackColor, 3, 2));
		$B2BlackColor = hexdec(substr($blackColor, 5, 2));

		$L1 = 0.2126 * pow($R1 / 255, 2.2) + 0.7152 * pow($G1 / 255, 2.2) + 0.0722 * pow($B1 / 255, 2.2);
		$L2 = 0.2126 * pow($R2BlackColor / 255, 2.2) + 0.7152 * pow($G2BlackColor / 255, 2.2) + 0.0722 * pow($B2BlackColor / 255, 2.2);

		$contrastRatio = 0;
		if ($L1 > $L2) {
			$contrastRatio = (int)(($L1 + 0.05) / ($L2 + 0.05));
		} else {
			$contrastRatio = (int)(($L2 + 0.05) / ($L1 + 0.05));
		}

		if ($contrastRatio > 5) {
			$textColor = '#000';
		} else { 
			$textColor = '#fff';
		}
		$ldotwTitle = mb_convert_case($theColor, MB_CASE_TITLE, "UTF-8");

		$colorsListing .= '
		<div class="colorsList" style="background: rgba(' . $backgroundColor . ',1) !important; color: ' . $textColor . ' !important;">' . $ldotwTitle . '</div>';
	}

	/***
	 * Output it!
	 */
	return $colorsListing;
}
add_shortcode('liturgicalcolors', 'ldotwcolors_shortcode');

/***
 *	The ShortCode function...
 *
 * Available toggles:
 * "altdiocese"	Choices are "yes" and "no" (default is "no").
 *				Some Dioceses have a couple days "flipped", so this allows for that.
 * "format"		Choices are "saintwording", "ldwording", "saint" and "none".
 *				If left blank, LD wording is shown first, then the Saint on the next line.
 * "fmslocation"	Choices are "wordingleft", "wordingright", "saintleft", "saintright", and "none".
 *				Default is "wordingleft". Shows Feast / Memorial / Solemnity.
 * "addlcolors"	Choices are "show" and "hide". Default is to show it, when available.
 *				Some days have multiple colors available. When present, a colorwheel is shown.
 *				When you hover your mouse over it, it'll show you the color choices for that day.
 */
function ldotw_shortcode($atts) {
	$ldotwAttribs = shortcode_atts(array(
		'altdiocese'	=> 'no',
		'format'		=> 'all',
		'fmslocation'	=> 'wordingleft',
		'addlcolors'	=> 'show',
		'language'	=> 'en-US'
	), $atts, 'ldotw');
	$altdiocese	= filter_var($ldotwAttribs['altdiocese'], FILTER_SANITIZE_STRING);
	$format		= filter_var($ldotwAttribs['format'], FILTER_SANITIZE_STRING);
	$fmslocation	= filter_var($ldotwAttribs['fmslocation'], FILTER_SANITIZE_STRING);
	$addlcolors	= filter_var($ldotwAttribs['addlcolors'], FILTER_SANITIZE_STRING);
	$language	= filter_var($ldotwAttribs['language'], FILTER_SANITIZE_STRING);
	$ldotwOutput = "";
	$ldotwText = "";

	/***
	 * We need to get the timezone offset and correct any time inconsistencies...
	 */
	$TZOffset = get_option('bdtzoffset', '0');

	/***
	 * Going to go ahead and set up some variables to handle the server time and corrected WP time
	 * notations for the timezone offset stuff. We'll also set up an array for the corrected WP time correction
	 * feature in the Shortcode Builder area.
	 *
	 * Setting up the offset minutes...
	 */
	$offsetMinutes = str_replace("-", "", $TZOffset) * 3600;

	/***
	 * If TZOffset is less than 0, then we'll subtract the time. If greater, add it. $tNOW is always based on GMT.
	 */
	$timeNOW = time();
	if($TZOffset >= 0) {
		$correctedTime = $timeNOW + $offsetMinutes;
	} else {
		$correctedTime = $timeNOW - $offsetMinutes;
	}

	$ServerTime = date("h:i:s A", $timeNOW);
	$CorrectedTime = date("h:i:s A", $correctedTime);
	$TZones = array("-12", "-11", "-10", "-9.5", "-9", "-8", "-7", "-6", "-5", "-4", "-3.5", "-3", "-2.5", "-2", "-1", "0", "1", "2", "3", "3.5", "4", "4.5", "5", "5.5", "5.75", "6", "6.5", "7", "8", "9", "9.5", "10", "10.5", "11", "12", "12.75", "13", "13.75", "14");

	/***
	 * Pulling together all the info
	 */
	$ldotwInfo = ldotwInfo($correctedTime);

	$ldotwTitle = ($altdiocese == "yes" && isset($ldotwInfo['altTitle'])) ? $ldotwInfo['altTitle'] : $ldotwInfo['title'];
	$backgroundColor = $ldotwInfo['backgroundColor'];
	$textColor = $ldotwInfo['textColor'];

	if($ldotwInfo['ldotwType'] != "" && $ldotwInfo['ldotwType'] != "none") {
		$fmsWLeft = ($fmslocation == "wordingleft") ? '(' . $ldotwInfo['ldotwType'] . ') ' : '';
		$fmsWRight = ($fmslocation == "wordingright") ? ' (' . $ldotwInfo['ldotwType'] . ')' : '';
		$fmsSLeft = ($fmslocation == "saintleft") ? '(' . $ldotwInfo['ldotwType'] . ') ' : '';
		$fmsSRight = ($fmslocation == "saintright") ? ' (' . $ldotwInfo['ldotwType'] . ')' : '';
	} else {
		$fmsWLeft = "";
		$fmsWRight = "";
		$fmsSLeft = "";
		$fmsSRight = "";
	}

	if($format == "none") {
		$format2 = $format;
	} else {
		$format2 = ($ldotwInfo['saint'] != "SAME") ? $format : 'ldwording';
	}

	if($format2 == 'none') {
		$ldotwText = '&nbsp;';
	} else if($format2 == 'saintwording') {
		$ldotwText = $fmsSLeft . $ldotwInfo['saint'] . $fmsSRight . '<br />' . $fmsWLeft . $ldotwTitle . $fmsWRight;
	} else if($format2 == 'ldwording') {
		$ldotwText = $fmsWLeft . $ldotwTitle . $fmsWRight;
	} else if($format2 == 'saint') {
		$ldotwText = $fmsSLeft . $ldotwInfo['saint'] . $fmsSRight;
	} else {
		$ldotwText = $fmsWLeft . $ldotwTitle . $fmsWRight . '<br />' . $fmsSLeft . $ldotwInfo['saint'] . $fmsSRight;
	}

	$colorWheel = ($addlcolors == "show" && $ldotwInfo['colorCombos'] != "") ? '<span style="float: right;"> <img src="' . plugin_dir_url( __FILE__ ) . 'images/colorWheel.png" title="' . $ldotwInfo['colorCombos'] . '" alt="' . $ldotwInfo['colorCombos'] . '" /></span>' : '';
	$colorWheelCSS = ($addlcolors == "show" && $ldotwInfo['colorCombos'] != "") ? ' ldotwCWheel' : '';

	$ldotwOutput .= '<div class="liturgicalDotW' . $colorWheelCSS . '" style="background: rgba(' . $backgroundColor . ',1) !important; color: ' . $textColor . ' !important;">' . $colorWheel . '' . $ldotwText . '</div>';

	/***
	 * Output it!
	 */
	return $ldotwOutput;
}
add_shortcode('liturgicaldotw', 'ldotw_shortcode');