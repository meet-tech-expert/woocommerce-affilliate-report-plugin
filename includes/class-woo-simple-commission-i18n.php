<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://compactimpact.com
 * @since      1.0.0
 *
 * @package    Woo_Simple_Commission
 * @subpackage Woo_Simple_Commission/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Woo_Simple_Commission
 * @subpackage Woo_Simple_Commission/includes
 * @author     Jade-eCommerce <support@j-e.com.hk>
 */
class Woo_Simple_Commission_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woo-simple-commission',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
