<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/mattwills8
 * @since      1.0.0
 *
 * @package    Affiliate_Link_Finder
 * @subpackage Affiliate_Link_Finder/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Affiliate_Link_Finder
 * @subpackage Affiliate_Link_Finder/includes
 * @author     Matt Wills <matt_wills8@outlook.com>
 */
class Affiliate_Link_Finder_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'affiliate-link-finder',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
