<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/mattwills8
 * @since             1.0.0
 * @package           Affiliate_Link_Finder
 *
 * @wordpress-plugin
 * Plugin Name:       Affiliate Link Finder
 * Plugin URI:        https://github.com/mattwills8
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Matt Wills
 * Author URI:        https://github.com/mattwills8
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       affiliate-link-finder
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PLUGIN_VERSION', '1.0.0' );


define( 'AFFILIATE_LINK_FINDER_ROOT', plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-affiliate-link-finder-activator.php
 */
function activate_affiliate_link_finder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-affiliate-link-finder-activator.php';
	Affiliate_Link_Finder_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-affiliate-link-finder-deactivator.php
 */
function deactivate_affiliate_link_finder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-affiliate-link-finder-deactivator.php';
	Affiliate_Link_Finder_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_affiliate_link_finder' );
register_deactivation_hook( __FILE__, 'deactivate_affiliate_link_finder' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-affiliate-link-finder.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_affiliate_link_finder() {

	$plugin = new Affiliate_Link_Finder();
	$plugin->run();

}
run_affiliate_link_finder();
