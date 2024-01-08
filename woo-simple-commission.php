<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/meet-tech-expert
 * @since             1.0.0
 * @package           Woo_Simple_Commission
 *
 * @wordpress-plugin
 * Plugin Name:       Woo Affilliate Report
 * Plugin URI:        #
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Rinkesh Gupta
 * Author URI:        https://github.com/meet-tech-expert
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-simple-commission
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_WOO_SIMPLE_COMM_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woo-simple-commission-activator.php
 */
function activate_woo_simple_commission() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-simple-commission-activator.php';
	Woo_Simple_Commission_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo-simple-commission-deactivator.php
 */
function deactivate_woo_simple_commission() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-simple-commission-deactivator.php';
	Woo_Simple_Commission_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woo_simple_commission' );
register_deactivation_hook( __FILE__, 'deactivate_woo_simple_commission' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-simple-commission.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woo_simple_commission() {

	$plugin = new Woo_Simple_Commission();
	$plugin->run();

}
run_woo_simple_commission();
