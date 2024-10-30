<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ukdesignservices.com
 * @since             1.0.0
 * @package           Ltd_Tickets_0
 *
 * @wordpress-plugin
 * Plugin Name:       London Theatre Direct
 * Plugin URI:        https://wordpress.londontheatredirect.com/
 * Description:       Turn your WordPress website into a ticketing site with the London Theatre Direct Tickets Plugin. Become an affiliate and earn commission from referred sales - also compatible with AWIN.
 * Version:           1.0.2
 * Author:            London Theatre Direct
 * Author URI:        https://wordpress.londontheatredirect.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ltd-tickets
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ltd_tickets() {
    define( 'LTD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
    define( 'LTD_PLUGIN_VERSION', '1.0.2' );
    define( 'LTD_PLUGIN_NAME', 'ltd-tickets' );
    define( 'LTD_ABSPATH', dirname( __FILE__ ) . '/' );
    define( 'LTD_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

	$plugin = new Ltd_Tickets();
	$plugin->run();

}


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ltd-tickets-activator.php
 */
function activate_ltd_tickets() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ltd-tickets-defaults.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ltd-tickets-activator.php';
    new Ltd_Tickets_Activator();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ltd-tickets-deactivator.php
 */
function deactivate_ltd_tickets() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ltd-tickets-deactivator.php';
	Ltd_Tickets_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ltd_tickets' );
register_deactivation_hook( __FILE__, 'deactivate_ltd_tickets' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ltd-tickets.php';


run_ltd_tickets();
