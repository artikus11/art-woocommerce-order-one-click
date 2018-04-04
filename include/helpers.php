<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Detect if a WordPress plugin is active
 * A function you can use to check if plugin is active/loaded for your plugins/themes
 * @link //gist.github.com/llgruff/c5666bfeded5de69b1aa424aa80cc14f
 */
function main_is_plugin_active( $plugin ) {
	$network_active = false;
	if ( is_multisite() ) {
		$plugins = get_site_option( 'active_sitewide_plugins' );
		if ( isset( $plugins[$plugin] ) ) {
			$network_active = true;
		}
	}
	return in_array( $plugin, get_option( 'active_plugins' ) ) || $network_active;
}