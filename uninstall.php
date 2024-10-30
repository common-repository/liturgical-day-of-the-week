<?php
/***
 * This file is used when the Liturgical Day of the Week plugin is deleted.
 */

/***
 * If uninstall.php is not called by WordPress, don't even THINK about actin' silly.
 */
	if(!defined('WP_UNINSTALL_PLUGIN')) {
		die;
	}
 
/***
 * Need to toss the QotW Timezone Offset WordPress option in [$wpdb->prefix]options table...
 */
	$ldotw_wpOptionName = 'ldotwTZOffset';
	if(!get_option('ldotwTZOffset')){
		delete_option($ldotw_wpOptionName);
		delete_site_option($ldotw_wpOptionName);
	}
?>