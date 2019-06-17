<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       linkedin.com/in/mustafa-shaaban22
 * @since      1.0.0
 *
 * @package    Ms_api
 * @subpackage Ms_api/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Ms_api
 * @subpackage Ms_api/includes
 * @author     Mustafa Shaaban <mustafashaaban22@gmail.com>
 */
class Ms_api_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ms_api',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
